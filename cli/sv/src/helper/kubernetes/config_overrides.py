import os

from helper import common

def pkictl_certs_initcontainer(service, volume_name, mount_path='/secrets',
                               certs_path='.certs'):
  """
  Create Kubernetes configuration overrides that adds a couple of things:
    - PKICTL service certificate InitContainer to copy the service's
      certificate and private key to /usr/share/pki/certs/service
    - In-memory Volume that stores said certificate
    - A volume mount to the first Container (i.e. the main service container)
      that mounts the certificate volume into /usr/share/pki/certs/service

  :param service: Service
  :type  service: Service

  :param volume_name: Name of the volume that will contain the service
    certificate
  :type  volume_name: str

  :key  mount_path: Where should the volume be mounted on the InitContainer
  :type mount_path: str

  :key  certs_path: Where should the service certificate directory be copied
    into, relative to the volume's root
  :type certs_path: str

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

  # The service blueprint prefix
  pkictl_blueprint_prefix = common.dict_get(
    service.config,
    ['pki', 'pkictl_blueprint_prefix'],
    default=service.service_name
  )

  # The service blueprint name for this service as registered to PKICTL
  pkictl_service_blueprint = '{}-{}'.format(
    pkictl_blueprint_prefix,
    service.environment
  )

  # PKICTL Service Certificate image
  certs_image = '{repo}/{blueprint}-pkictl-initcontainer:latest'.format(
    repo=service.default_repository,
    blueprint=pkictl_service_blueprint
  )

  # This is the PKICTL Service Certificate init container which contains the
  # service certificate and private key to be used to authenticate with Vault.
  # Will be appended into Deployment.spec.template.spec.initContainers
  certs_init_container = {
    'command': [
      'sh', '-c',
      'mkdir -p {volume}/{certs_path} && cp -r {blueprint} {volume}/{certs_path}'.format(
        volume=mount_path,
        blueprint=pkictl_service_blueprint,
        certs_path=certs_path
      )
    ],
    'image': certs_image,
    'name': 'certs-init',
    'volumeMounts': [{
      'name': volume_name,
      'mountPath': mount_path
    }]
  }

  # This in-memory volume will contain PKICTL certificate copied from the init
  # container
  volume = {
    'name': volume_name,
    'emptyDir': {
      'medium': 'Memory',
      'sizeLimit': '1Mi'
    }
  }

  # VolumeMount to mount certificates into the default directory
  # (/usr/share/pki/certs/service). Will extend the VolumeMounts list defined in
  # Deployment.spec.template.spec.containers[0].volumeMounts
  volume_mount = {
    'name': volume_name,
    'mountPath': os.path.join('/usr/share/pki/certs/service', pkictl_service_blueprint),
    'subPath': os.path.join(certs_path, pkictl_service_blueprint)
  }

  return [
    {
      'type': 'deployment',
      'override_path': ['spec', 'template', 'spec', 'initContainers'],
      'spec': [certs_init_container]
    },
    {
      'type': 'deployment',
      'override_path': ['spec', 'template', 'spec', 'containers', 0, 'volumeMounts'],
      'spec': [volume_mount]
    },
    {
      'type': 'deployment',
      'override_path': ['spec', 'template', 'spec', 'volumes'],
      'spec': [volume]
    }
  ]
