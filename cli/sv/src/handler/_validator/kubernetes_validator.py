import os
import sys

from handler._validator import Validator
from helper import logger, path
from service import Service

class KubernetesValidator(Validator):
  """
  Validate the service Kubernetes directory, and optionally the exsitence of a
  cluster (if constructed with cluster_name keyword arguments). Can be negated.
  """

  def __init__(self, service_name='service_name', cluster_name=None,
               negate=False):
    """
    Construct a ServiceValidator

    :key  service_name: The key name for service name argument in the parsed
        arguments passed into validate(). Required.
    :type service_name: str

    :key  cluster_name: The key name for Kubernets cluster name argument in the
        parsed arguments passed into validate(). Will skip cluster validation if
        None.
    :type cluster_name: str

    :key  negate: Whether to negate the validation or not, i.e. if this is True,
        this validator will validate that the service Kubernetes config
        directory does not exist. Default is False.
    :type negate: bool
    """

    super(KubernetesValidator, self).__init__(
      service_name=service_name,
      cluster_name=cluster_name
    )

    self.negate = negate

  def _validate(self, service_name=None, cluster_name=None):
    """
    Validate that a svctl service has Kubernetes configuration in its directory.
    Will exit with code 3 if any validation failed.

    :key  service_name: Service to validate
    :type service_name: str

    :key  cluster_name: Cluster to validate. Will skip cluster validation if
        None.
    :type cluster_name: str
    """

    kubernetes_directory = path.kubernetes_directory(service_name)

    if self.negate:
      # Validate that Kubernetes config directory does not exist
      if os.path.isdir(kubernetes_directory):
        logger.error('Kubernetes config directory {} already exist'.format(
          logger.colorize(kubernetes_directory, logger.YELLOW)
        ))

        sys.exit(1)

      return
    else:
      # Validate that Kubernetes config directory does exist
      if not os.path.isdir(kubernetes_directory):
        logger.error(
          'Kubernetes config directory {} does not exist. Please run the init ' \
          'script {} first'.format(
            logger.colorize(kubernetes_directory, logger.YELLOW),
            logger.colorize('./svctl kube init', logger.YELLOW)
          )
        )

        sys.exit(3)

      if cluster_name:
        service = Service(service_name, None)

        # Validate cluster_name
        if cluster_name not in service.kubernetes_config['clusters']:
          logger.error('Invalid cluster name "{}"'.format(
            logger.colorize(cluster_name, logger.YELLOW),
          ))

          logger.error('Available clusters: {}\n'.format(
            ', '.join([logger.colorize(c, logger.YELLOW) for c in service.kubernetes_config['clusters']])
          ))

          exit(3)
