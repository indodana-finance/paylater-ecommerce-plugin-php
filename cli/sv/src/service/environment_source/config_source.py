import os
import re

from service.environment_source.env_source import EnvSource

class ConfigSource(EnvSource):
  def parse(self, definition, volume_name=None):
    """
    Parse Config environment configuration from .services.d/<service>/config<env>.yml

    :param definition: A dictionary containing definition of Config environment
      artifacts. See example in sv/src/template/service/config/env_config.yml.j2
      in the `artifacts.config` object
    :type  definition: dict

    :key  volume_name: Volume name to load the ConfigMap into
    :type volume_name: str

    :key  volume_mount_path: Absolute path to where the volume should be mounted
      in the initContainer and sidecar container
    :type volume_mount_path: str

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

    # Should match with sv/src/template/kubernetes/generator/configMap.yml.j2
    configmap_name = '{}-{}-configmap'.format(
      self.service.service_name,
      self.service.environment,
    )

    if volume_name == None:
      volume_name = '{}-{}-config-volume'.format(
        self.service.service_name,
        self.service.environment,
      )

    # This dict will extend the ConfigMap data defined in ConfigMap.data
    configmap_data = {}

    # This object will be appended into the Volumes list in
    # Deployment.spec.template.spec.volumes
    volume = {
      'name': volume_name,
      'configMap': {
        'name': configmap_name
      }
    }

    # This list will extend the VolumeMounts list defined in
    # Deployment.spec.template.spec.containers[0].volumeMounts
    volume_mounts = []

    # This object will be appended into the main container args list in
    # Deployment.spec.template.spec.containers[0].args
    main_container_args = []

    config_artifacts = definition.get('files', []) or []

    # This list contains additional files discovered by `directory`-type
    # artifacts
    additional_config_artifacts = []

    index = 0

    # We use nested loop here so we'll first process the environment artifacts
    # defined in config_artifacts before processing the ones in
    # additional_config_artifacts, using the same logic.
    for _config_artifacts in [config_artifacts, additional_config_artifacts]:
      for artifact in _config_artifacts:
        artifact_path = artifact['path']

        if not os.path.exists(artifact_path):
          raise Exception('File or directory {} does not exist'.format(artifact_path))

        mount_type = artifact.get('mount',  {}).get('type', 'env')

        if mount_type == 'directory':
          # Special directory mount type. We'll need to recusrively search the
          # directory to find all files and then append those files to the
          # `additional_config_artifacts` list

          directory_path = artifact['path']
          mount_path = artifact.get('mount',  {}).get('path', directory_path)

          for root, _, files in os.walk(directory_path):
            for name in files:
              filepath = os.path.join(root, name)

              # File path relative to the `directory_path`
              relative_filepath = os.path.relpath(filepath, directory_path)

              additional_config_artifacts.append({
                'path': filepath,
                'mount': {
                  'type': 'file',
                  'path': os.path.join(mount_path, relative_filepath),
                }
              })
        else:
          # For env and file mount type

          filepath = artifact['path']
          config_content = open(filepath, 'r').read()

          # Kubernetes config map only support [-._a-zA-Z0-9] as configmap keyname.
          # Index is appended so the same path can be mounted more than once without
          # conflict
          configmap_key = '{}-{}'.format(
            re.sub('[^-._a-zA-Z0-9]', '_', filepath),
            index
          )

          configmap_data[configmap_key] = config_content

          if mount_type == 'file':
            # Mount the configMap key
            mount_path = artifact.get('mount',  {}).get('path', filepath)

            volume_mounts.append({
              'name': volume_name,
              'mountPath': os.path.join(self.service.base_image_workdir, mount_path),
              'subPath': configmap_key
            })
          elif mount_type == 'env':
            # Mount the configMap key in the /config_env directory using
            # configmap_key as filename
            env_file_mount_path = os.path.join(
              '/config_env',
              configmap_key
            )

            volume_mounts.append({
              'name': volume_name,
              'mountPath': env_file_mount_path,
              'subPath': configmap_key
            })

            # Load the file as environment variables through svctl run --env-file
            # flag
            main_container_args += ['--env-file', env_file_mount_path]

          index += 1

    return [
      {
        'type': 'deployment',
        'override_path': ['spec', 'template', 'spec', 'containers', 0, 'volumeMounts'],
        'spec': volume_mounts
      },
      {
        'type': 'deployment',
        'override_path': ['spec', 'template', 'spec', 'volumes'],
        'spec': [volume]
      },
      {
        'type': 'deployment',
        'override_path': ['spec', 'template', 'spec', 'containers', 0, 'args'],
        'spec': main_container_args
      },
      {
        'type': 'configmap',
        'override_path': ['data'],
        'spec': configmap_data
      }
    ]
