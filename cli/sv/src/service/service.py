import jinja2
import os
import pendulum
import re
import subprocess
import sys
import yaml
from dotenv import load_dotenv

import template
from .environment_source import ConfigSource, VaultSource
from config import Config
from constant import docker, kubernetes as kubernetes_constant
from helper import common, files, logger, path
from helper.bash import Bash
from helper.kubernetes import kubectl, config_overrides as kubernetes_config_overrides

class Service(object):
  """
  Class representing a service

  :var service_name: Name of the service
  :var environment: Service environment
  :var info: Service information and metadata
  :var kubernetes_config: Configuration regarding Kubernetes deployment
  """

  def __init__(self, service_name, environment, version=None):
    """
    Create a Service instance

    :param service_name: Name of the service
    :type  service_name: str

    :param environment: Service environment
    :type  environment: str

    :keyword version: Version (Docker image tag) to deploy. Defaults to
        self.environment, indicating that the latest image of that environment
        will be deployed.
    :type    version: str
    """

    self.service_name = service_name
    self.environment = environment
    self.version = version or self.environment

    self.directory = path.service_definition_directory(service_name)
    self.service_script = path.service_definition_script(service_name)

    self.config = yaml.full_load(
      open(path.service_definition_config(service_name))
    )

    # Environment configuration
    if self.environment:
      self.env_config = yaml.full_load(
        open('{}/config/{}.yml'.format(self.directory, self.environment))
      )

      self.env_directory = '{}/config/{}'.format(self.directory, self.environment)

    # Docker-related config
    self.default_repository = common.dict_get(
      self.config,
      ['repository'],
      default='{}/{}'.format(
        docker.REGISTRY['DEFAULT'],
        self.config['team']
      )
    )

    self.build_image = common.dict_get(
      self.config,
      ['build_image', 'name'],
      default='{}-build'.format(self.service_name)
    )

    self.build_image_repository = common.dict_get(
      self.config,
      ['build_image', 'repository'],
      default=self.default_repository
    )

    self.build_image_user = common.dict_get(
      self.config,
      ['build_image', 'user'],
      default='appRunner'
    )

    self.base_image = common.dict_get(
      self.config,
      ['base_image', 'name'],
      default='{}-base'.format(self.service_name)
    )

    self.base_image_repository = common.dict_get(
      self.config,
      ['base_image', 'repository'],
      default=self.default_repository
    )

    self.base_image_user = common.dict_get(
      self.config,
      ['base_image', 'user'],
      default='appRunner'
    )

    if self.environment:
      self.base_image_workdir = common.dict_get(
        self.config,
        ['base_image', 'workdir'],
        default='/home/{user}/{service}/.build/{service}/{env}'.format(
          user=self.base_image_user,
          service=self.service_name,
          env=self.environment
        )
      )

    # Kubernetes config
    self.kubernetes_config = None

    kubernetes_config_path = '{}/config.yml'.format(
      path.kubernetes_directory(self.service_name)
    )

    if os.path.exists(kubernetes_config_path):
      self.kubernetes_config = yaml.full_load(open(kubernetes_config_path))

  def run(self, args=[], load_config=False):
    """
    Run this service. The svctl process will be replaced by a bash process that
    executes the actual `run` function

    :key  args: List of arguments passed into the run() function
    :type args: list[str]

    :key  load_config: Whether to load (copy) config files into the build
      directory before running the service. Only useful in local development
      environment since the config files might no longer exist in the service
      container.
    :type load_config: bool
    """

    build_directory = path.service_build_directory(
      self.service_name,
      self.environment
    )

    if not os.path.exists(build_directory):
      logger.error(
        'Build artifacts at {} do not exist, run the build script first'.format(
          logger.colorize(build_directory, logger.YELLOW)
        )
      )

      sys.exit(1)

    # Copy config files into the build directory, also load config files
    # designated as DOTENV files to environment variables
    if load_config:
      config_artifacts = self.env_config \
        .get('artifacts', {}) \
        .get('config', {}) \
        .get('files', []) or []

      for _, artifact in enumerate(config_artifacts):
        artifact_path = artifact['path']

        if not os.path.exists(artifact_path):
          raise Exception('Config file or directory {} does not exist'.format(
            artifact_path
          ))

        mount_type = artifact.get('mount',  {}).get('type', 'env')

        if mount_type == 'directory' or mount_type == 'file':
          mount_path = artifact.get('mount',  {}).get('path', artifact_path)

          files.copy_mount(
            artifact_path,
            os.path.join(build_directory, mount_path)
          )
        elif mount_type == 'env':
          load_dotenv(dotenv_path=artifact_path)

    # Render the run Bash script from template
    run_script = template.get('internal/run.sh').render(
      service_name=self.service_name,
      environment=self.environment,
      args=args,
      build_directory=build_directory,
      service_definition_script=self.service_script,
      svctl_directory=Config().get('svctl_directory')
    )

    logger.info('Starting {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

    Bash.execl(run_script)

  def build(self):
    """
    Build this service
    """

    build_directory = path.service_build_directory(
      self.service_name,
      self.environment
    )

    # Clean existing build directory and ensure that the build directory exists
    if (os.path.exists(build_directory)):
      logger.info('Cleaning previous build at {}...'.format(
        logger.colorize(build_directory, logger.YELLOW)
      ))

      files.remove_directory(build_directory)

    files.ensure_directory(build_directory)

    # Render the build Bash script from template
    build_script = template.get('internal/build.sh').render(
      service_name=self.service_name,
      environment=self.environment,
      build_directory=build_directory,
      service_definition_script=self.service_script
    )

    logger.info('Building {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

    exit_code = Bash.script(build_script)

    if exit_code != 0:
      sys.exit(exit_code)

    logger.ok('Finished building {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

  def postbuild(self):
    """
    Run the postbuild script of this service
    """

    build_directory = path.service_build_directory(
      self.service_name,
      self.environment
    )

    if (not os.path.exists(build_directory)):
      logger.error(
        'Build artifacts at {} do not exist, run the build script first' \
          .format(logger.colorize(build_directory, logger.YELLOW)
        )
      )

      sys.exit(1)

    # Render the postbuild Bash script from template
    postbuild_script = template.get('internal/postbuild.sh').render(
      service_name=self.service_name,
      environment=self.environment,
      build_directory=build_directory,
      service_definition_script=self.service_script,
      svctl_directory=Config().get('svctl_directory')
    )

    logger.info('Running post build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

    exit_code = Bash.script(postbuild_script)

    if exit_code != 0:
      sys.exit(exit_code)

    logger.ok('Finished running post build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

  def host_prebuild(self):
    """
    Run the host_prebuild script of this service
    """

    build_directory = path.service_build_directory(
      self.service_name,
      self.environment
    )

    # Render the host_prebuild Bash script from template
    host_prebuild_script = template.get('internal/host_prebuild.sh').render(
      service_name=self.service_name,
      environment=self.environment,
      build_directory=build_directory,
      service_definition_script=self.service_script,
      svctl_directory=Config().get('svctl_directory')
    )

    logger.info('Running host pre-build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

    exit_code = Bash.script(host_prebuild_script)

    if exit_code != 0:
      sys.exit(exit_code)

    logger.ok('Finished running host pre-build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

  def host_postbuild(self):
    """
    Run the host_postbuild script of this service
    """

    build_directory = path.service_build_directory(
      self.service_name,
      self.environment
    )

    # Render the host_postbuild Bash script from template
    host_postbuild_script = template.get('internal/host_postbuild.sh').render(
      service_name=self.service_name,
      environment=self.environment,
      build_directory=build_directory,
      service_definition_script=self.service_script,
      svctl_directory=Config().get('svctl_directory')
    )

    logger.info('Running host post-build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

    exit_code = Bash.script(host_postbuild_script)

    if exit_code != 0:
      sys.exit(exit_code)

    logger.ok('Finished running host post-build script for {} ({})...'.format(
      logger.colorize(self.service_name, logger.YELLOW),
      logger.colorize(self.environment, logger.YELLOW)
    ))

  def deploy(self, cluster_name='all'):
    """
    Deploy this service to Kubernetes cluster(s)

    :param cluster_name: Which cluster to deploy to. Deploy to all cluster by
        default
    :type  cluster_name: str
    """

    yamls = self.generate_kubernetes_yaml()

    for cluster in self.kubernetes_config['clusters']:
      if cluster != cluster_name:
        continue

      cluster_info = self.kubernetes_config['clusters'][cluster]

      for subj in ['deployment', 'service']:
        if not kubectl.can_i(cluster, cluster_info, op='create', subj=subj):
          logger.error(
            'No permission to create {} in namespace {} of cluster {}'.format(
              logger.colorize(subj, logger.YELLOW),
              logger.colorize(cluster_info['namespace'], logger.YELLOW),
              logger.colorize(cluster, logger.YELLOW)
            )
          )

          sys.exit(1)

    for cluster in self.kubernetes_config['clusters']:
      if cluster != cluster_name:
        continue

      logger.info('Deploying to Kubernetes cluster {}'.format(
        logger.colorize(cluster, logger.YELLOW)
      ))

      cluster_info = self.kubernetes_config['clusters'][cluster]
      cluster_yaml = yamls[cluster]

      kubectl.apply(cluster, cluster_info, cluster_yaml)

    logger.ok(
      'Deployment finished, please verify the deployment rollout manually ' \
      'with {}'.format(
        logger.colorize('kubectl describe deployment {}-deployment'.format(
          self.service_name
        ), logger.YELLOW)
      )
    )

  def generate_kubernetes_yaml(self):
    """
    Generate Kubernetes YAMLs containing multiple documents that can be used to
    deploy this service.

    :returns: Dictionary containing YAML data for each cluster defined in
        kubernetes/config.yml
    :rtype: dict
    """

    clusters = {}

    # Name of the in-memory Kubernetes volume that will store PKICTL certificate
    certs_volume_name = '{}-{}-certs-volume'.format(
      self.service_name,
      self.environment,
    )

    # Name of the directory, relative to volume root, that will store the
    # service's PKICTL certificate
    certs_path = '.certs'

    # List of Kubernetes configuration overrides
    kubernetes_configuration_overrides = []

    # Add PKICTL service certificate initContainer
    if common.dict_get(self.config, ['pki', 'enabled'], default=False):
      kubernetes_configuration_overrides += kubernetes_config_overrides.pkictl_certs_initcontainer(
        service=self,
        volume_name=certs_volume_name,
        certs_path=certs_path
      )

    # Parse environment configuration as Kubernetes configuration overrides
    kubernetes_configuration_overrides += self._parse_env_config(
      certs_volume_name=certs_volume_name,
      certs_path=certs_path
    )

    for cluster_name, cluster_config in self.kubernetes_config['clusters'].items():
      kubernetes_specs = {
        'configmap': self._generate_kubernetes_configmap(),
        'deployment': self._generate_kubernetes_deployment(),
        'service': self._generate_kubernetes_service(cluster_config)
      }

      # Remove SVCTL arguments and readd it later since those arguments should
      # be at the end of the args array
      main_container_args = common.dict_get(
        kubernetes_specs['deployment'],
        ['spec', 'template', 'spec', 'containers', 0, 'args'],
        default=[]
      )

      common.dict_set(
        kubernetes_specs['deployment'],
        ['spec', 'template', 'spec', 'containers', 0, 'args'],
        []
      )

      # Override/extend the YAMLs according to environment configurations
      for config_override in kubernetes_configuration_overrides:
        yaml_type = config_override['type']
        override_path = config_override['override_path']
        spec = config_override['spec']

        common.dict_merge(
          kubernetes_specs[yaml_type], override_path,
          spec, []
        )

      # Readd SVCTL additional service arguments at the end of the args array
      common.dict_merge(
        kubernetes_specs['deployment'],
        ['spec', 'template', 'spec', 'containers', 0, 'args'],
        main_container_args,
        []
      )

      clusters[cluster_name] = '---\n' + '\n---\n'.join([
        yaml.dump(kubernetes_specs['configmap'], default_flow_style=False),
        yaml.dump(kubernetes_specs['deployment'], default_flow_style=False),
        yaml.dump(kubernetes_specs['service'], default_flow_style=False)
      ])

    return clusters

  def _generate_kubernetes_deployment(self):
    """
    Generate Kubernetes deployment from base template and config defined in
    svctl services directory.

    In increasing order of importance, there are three sources of data that are
    used in generating the deployment document:
      - base template defined in constant/kubernetes.py
      - template service configuration defined in svctl services directory
        (.services.d/<service-name>kubernetes/deployment.yml)
      - raw service configuration defined in svctl services directory
        (.services.d/<service-name>kubernetes/deployment.yml)

    :returns: Dictionary with similar structure to deployment-v1-apps schema of
        Kubernetes API. Can be converted into YAML with yaml.dump()
    :rtype: dict
    """

    # Render and parse base deployment template
    deployment_template = template.get('kubernetes/generator/deployment.yml')

    deployment_yaml = deployment_template.render(
      deployment_date=int(pendulum.now().utcnow().timestamp()),
      deployer=os.getenv('GITHUB_USERNAME'),
      version=self.version,
      service_name=self.service_name,
      environment=self.environment,
      repository=self.default_repository,
      organization=self.config['organization'],
      team=self.config['team'],
      product=self.config['product']
    )

    deployment = yaml.full_load(deployment_yaml)

    deployment_definition_path = '{}/deployment.yml'.format(
      path.kubernetes_directory(self.service_name)
    )

    if os.path.exists(deployment_definition_path):
      # Load service configuration
      deployment_definition = yaml.full_load(open(deployment_definition_path))

      template_section = deployment_definition.get('template')
      raw_section = deployment_definition.get('raw')

      # Update deployment according to the "template" section of the config
      if template_section:
        for mapping in kubernetes_constant.TEMPLATE_MAP['DEPLOYMENT']:
          common.dict_merge(
            deployment, mapping['destination'],
            template_section, mapping['source']
          )

      # Update deployment according to the "raw" section of the config
      if raw_section:
        common.dict_update(deployment, raw_section)

    return deployment

  def _generate_kubernetes_service(self, cluster_config):
    """
    Generate Kubernetes service from base template and config defined in svctl
    services directory.

    In increasing order of importance, there are three sources of data that are
    used in generating the service document:
      - base template defined in constant/kubernetes.py
      - template service configuration defined in svctl services directory
        (.services.d/<service-name>kubernetes/service.yml)
      - raw service configuration defined in svctl services directory
        (.services.d/<service-name>kubernetes/service.yml)

    :returns: Dictionary with similar structure to service-v1-core schema of
        Kubernetes API. Can be converted into YAML with yaml.dump()
    :rtype: dict
    """

    # Render and parse base service template
    service_template = template.get('kubernetes/generator/service.yml')
    service_yaml = service_template.render(service_name=self.service_name)

    service = yaml.full_load(service_yaml)

    service_definition_path = '{}/service.yml'.format(
      path.kubernetes_directory(self.service_name)
    )

    if os.path.exists(service_definition_path):
      # Load service configuration
      service_definition = yaml.full_load(open(service_definition_path))

      template_section = None
      raw_section = None

      if service_definition != None:
        template_section = service_definition.get('template')
        raw_section = service_definition.get('raw')

      # Update service according to the "template" section of the config
      if template_section:
        for mapping in kubernetes_constant.TEMPLATE_MAP['SERVICE']:
          common.dict_merge(
            service, mapping['destination'],
            template_section, mapping['source']
          )

        # Add additional vendor-specific information for services of
        # LoadBalancer type
        service_type = common.dict_get(template_section, ['type'])

        loadbalancer_type = common.dict_get(
          template_section,
          ['loadBalancerOptions', 'type']
        )

        if (service_type == 'LoadBalancer') and (loadbalancer_type != None):
          loadbalancer_config = common.dict_get(
            kubernetes_constant.CLOUD_PROVIDER_LOADBALANCER_MAP,
            [cluster_config['cloud_vendor'], 'type', loadbalancer_type]
          )

          common.dict_merge(service, ['metadata'], loadbalancer_config, [])

      # Update service according to the "raw" section of the config
      if raw_section:
        common.dict_update(service, raw_section)

    return service

  def _generate_kubernetes_configmap(self):
    """
    Generate empty Kubernetes configmap

    :returns: Dictionary with similar structure to configmap-v1-core schema of
        Kubernetes API. Can be converted into YAML with yaml.dump()
    :rtype: dict
    """

    configmap_yaml = template.get('kubernetes/generator/configMap.yml') \
      .render(
        service_name=self.service_name,
        environment=self.environment
      )

    # Return base configmap template which will be overriden by
    # _parse_env_config
    return yaml.full_load(configmap_yaml)

  def _parse_env_config(self, certs_volume_name, certs_path):
    """
    Parse environment config file

    :param certs_volume_name: Name of the volume containing service certificate
    :type  certs_volume_name: str

    :param certs_path: Where the service certificate directory is located,
      relative to the volume's root
    :type  certs_path: str

    :returns: List of Kubernetes configuration overrides. Each configuration
      override is a dict that contain three fields:
        - type: the kind of Kubernetes configuration to override. Currently
            there are three valid values: configmap, deployment, and service
        - override_path: list of strings that specifies the path where the
            configuration will be overriden. See the `path_a` argument of
            `dict_merge()` in `common.py`
        - spec: the specification that will override (or extend) the
            configurations in `override_path`
    :rtype: list[dict]
    """

    # Valid environment artifact sources
    artifact_source_mapping = {
      'vault': VaultSource(
        service=self,
        certs_volume_name=certs_volume_name,
        certs_path=certs_path
      ),
      'config': ConfigSource(service=self)
    }

    artifacts = self.env_config.get('artifacts', {})

    kubernetes_configuration_overrides = []

    for source, definition in artifacts.items():
      artifact_source = artifact_source_mapping[source]

      kubernetes_configuration_overrides += artifact_source.parse(definition)

    return kubernetes_configuration_overrides
