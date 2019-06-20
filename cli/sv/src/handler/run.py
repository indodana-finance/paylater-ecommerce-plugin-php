import argparse
import os
from dotenv import load_dotenv

from handler import CommandHandler
from handler._parser import ArgparseParser
from handler._validator import ServiceValidator
from helper import logger, path
from service import Service

class ServiceRun(CommandHandler):
  """
  Run a service
  Positional arguments: SERVICE_NAME, ENVIRONMENT
  Flags:
    --env-file <path>: Path to dotenv file to load env from. Can be specified
                       multiple times
  """

  def __init__(self):
    parser = argparse.ArgumentParser()

    parser.add_argument('-e', '--env-file',
      help='Path to file to load env from',
      action='append'
    )

    parser.add_argument('-l', '--load-config',
      help='Whether to load (copy) config ' +
        'files into the build directory before running the service. Only ' +
        'useful in local development environment since the config files ' +
        'might no longer exist in the service container.',
      action='store_true',
      default=False
    )

    parser.add_argument('service_name', help='Service name')

    parser.add_argument('environment', help='Environment')

    parser.add_argument('service_args',
      help='Service arguments',
      nargs='*',
      default=[]
    )

    super(ServiceRun, self).__init__(
      parser=ArgparseParser(parser),
      validators=[
        ServiceValidator(service_name='service_name', environment='environment')
      ]
    )

  def _execute(self, kwargs):
    """
    Execute the command

    :param kwargs: Command keyword arguments
    :type  kwargs: dict
    """

    service_name = kwargs['service_name']
    environment = kwargs['environment']
    service_args = kwargs['service_args']
    load_config = kwargs['load_config']

    if (not load_config) and not os.path.exists('/.dockerenv'):
      env_config_file = '{}/{}.yml'.format(
        path.service_definition_directory(service_name),
        environment
      )

      logger.warn(
        'It seems that this service is running outside a container. If it is ' \
        'running on a development machine, the {} flag should be used to ' \
        'load configuration files defined in the {} config file.'.format(
          logger.colorize('--load-config', logger.YELLOW),
          logger.colorize(env_config_file, logger.YELLOW),
        )
      )

    if kwargs['env_file'] != None:
      for env_file in kwargs['env_file']:
        if os.path.exists(env_file):
          logger.info('Loading environment variables from {}'.format(
            logger.colorize(env_file, logger.YELLOW)
          ))

          load_dotenv(dotenv_path=env_file)
        else:
          logger.warn('Env file {} does not exist'.format(
            logger.colorize(env_file, logger.YELLOW)
          ))

    service = Service(service_name, environment)
    service.run(args=service_args, load_config=load_config)

command = ServiceRun()
