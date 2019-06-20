import os
import sys

from handler._validator import Validator
from helper import common, logger, path
from service import Service

class ServiceValidator(Validator):
  """
  Validate the svctl service directory, and optionally the exsitence of a
  service and one of its environment (if constructed with service_name and
  environment keyword arguments).
  """

  def __init__(self, service_name=None, environment=None, negate=False):
    """
    Construct a ServiceValidator

    :key  service_name: The key name for service name argument in the parsed
        arguments passed into validate(). Will skip service validation if is
        None.
    :type service_name: str

    :key  environment: The key name for service environment argument in the
        parsed arguments passed into validate(). Requires `service` to be
        specified. Will skip environment validation if None.
    :type environment: str

    :key  negate: Whether to negate the validation or not, i.e. if this is True,
        this validator will validate that the service config directory does not
        exist. Default is False.
    :type negate: bool
    """

    super(ServiceValidator, self).__init__(
      service_name=service_name,
      environment=environment
    )

    self.negate = negate

  def _validate(self, service_name=None, environment=None):
    """
    Validate that a svctl service has been properly configured. Optionally
    validate whether a service and environment exist in the configuration. Will
    exit with code 3 if any validation failed.

    :key  service_name: Service name to validate. Will skip service validation
        if is None.
    :type service_name: str

    :key  environment: Service environment to validate. Requires `service_name`
        to be specified. Will skip environment validation if None.
    :type environment: str
    """

    if self.negate:
      # Validate that service definition directory does not exist
      service_definition_directory = path.service_definition_directory(service_name)

      if os.path.exists(service_definition_directory):
        logger.error(
          'Service definition directory {} already exist'.format(
            logger.colorize(service_definition_directory, logger.YELLOW)
          )
        )

        sys.exit(1)
    else:
      services_directory = path.services_directory()

      # Check services directory
      if not os.path.isdir(services_directory):
        logger.error(
          'Services directory {} does not exist. Please run the init script ' \
          '{} first'.format(
            logger.colorize(services_directory, logger.YELLOW),
            logger.colorize('./svctl init', logger.YELLOW)
          )
        )

        sys.exit(3)

      # Check whether service is properly defined
      if (service_name != None) and (service_name not in ServiceValidator._services()):
        logger.error('Invalid service "{}"'.format(
          logger.colorize(service_name, logger.YELLOW),
        ))

        logger.error('Available services: {}\n'.format(
          ', '.join([logger.colorize(s, logger.YELLOW) for s in ServiceValidator._services()])
        ))

        sys.exit(3)

      service_obj = Service(service_name, None)

      # Check whether service environment is properly defined
      if (service_name != None) and (environment != None) and (environment not in service_obj.config['environments']):
        logger.error('Invalid environment "{}"'.format(
          logger.colorize(environment, logger.YELLOW),
        ))

        logger.error('Available environments: {}\n'.format(
          ', '.join([logger.colorize(s, logger.YELLOW) for s in service_obj.config['environments']])
        ))

        sys.exit(3)

  @staticmethod
  def _services():
    """
    Get list of defined services

    :returns: List of services
    :rtype: list[str]
    """

    service_directories = os.listdir(path.services_directory())
    service_directories.sort()

    # Discard directories that starts with dot `.`
    service_directories = [d for d in service_directories if not d.startswith('.')]

    return service_directories
