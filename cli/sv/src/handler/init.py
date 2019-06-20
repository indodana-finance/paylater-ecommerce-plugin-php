import os
import sys

import template
from helper import files, logger, path
from handler import CommandHandler
from handler._parser import PositionalParser
from handler._validator import ServiceValidator
from service import Service

FILES = [
  'service',
  'config.yml'
]

class ServiceInit(CommandHandler):
  """
  Generate basic service definition files
  Positional arguments: SERVICE_NAME
  """

  def __init__(self):
    super(ServiceInit, self).__init__(
      parser=PositionalParser('service_name'),
      validators=[
        ServiceValidator(service_name='service_name', negate=True)
      ]
    )

  def _execute(self, kwargs):
    """
    Execute the command

    :param kwargs: Command keyword arguments
    :type  kwargs: dict
    """

    service_name = kwargs['service_name']

    service_definition_directory = path.service_definition_directory(service_name)
    files.ensure_directory(service_definition_directory)

    for template_file in FILES:
      file_path = os.path.join(service_definition_directory, template_file)
      config_template = template.get('service/config/{}'.format(template_file))

      logger.info('Generating {} in {}...'.format(
        logger.colorize(file_path, logger.YELLOW),
        logger.colorize(os.getcwd(), logger.YELLOW)
      ))

      with open(file_path, 'w') as f:
        # The config template does not need any templated variables
        f.write(config_template.render())

    # Generate sample config file
    service = Service(service_name, environment=None)

    config_directory = '{}/config'.format(service_definition_directory)
    files.ensure_directory(config_directory)

    # Generate environment configurations
    for environment in service.config['environments']:
      env_config_filepath = '{}/{}.yml'.format(config_directory, environment)

      logger.info('Generating environment config file {}...'.format(
        logger.colorize(env_config_filepath, logger.YELLOW)
      ))

      with open(env_config_filepath, 'w') as f:
        f.write(template.get('service/config/env_config.yml').render())

    logger.ok(
      'Finished creating sample service definition files for {}. Please edit ' \
      'the files in {} manually.'.format(
        logger.colorize(service_name, logger.YELLOW),
        logger.colorize('{}/{}'.format(os.getcwd(), service_definition_directory), logger.YELLOW),
      )
    )

command = ServiceInit()
