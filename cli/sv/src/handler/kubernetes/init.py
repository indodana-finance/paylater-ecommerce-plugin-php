import os

import template
from handler import CommandHandler
from handler._parser import PositionalParser
from handler._validator import ServiceValidator, KubernetesValidator
from helper import files, logger, path

class KubernetesInit(CommandHandler):
  """
  Deploy a service to Kubernetes cluster(s)
  Positional arguments: CLUSTER_NAME, SERVICE_NAME, ENVIRONMENT, VERSION
  """

  def __init__(self):
    super(KubernetesInit, self).__init__(
      parser=PositionalParser('service_name'),
      validators=[
        ServiceValidator(service_name='service_name', environment=None),
        KubernetesValidator(service_name='service_name', cluster_name=None, negate=True)
      ]
    )

  def _execute(self, kwargs):
    """
    Execute the command

    :param kwargs: Command keyword arguments
    :type  kwargs: dict
    """

    FILES = [
      'config.yml',
      'deployment.yml',
      'service.yml'
    ]

    service_name = kwargs['service_name']

    kubernetes_directory = path.kubernetes_directory(service_name)
    files.ensure_directory(kubernetes_directory)

    for template_file in FILES:
      file_path = os.path.join(kubernetes_directory, template_file)
      config_template = template.get('kubernetes/config/{}'.format(template_file))

      logger.info('Generating {} in {}...'.format(
        logger.colorize(file_path, logger.YELLOW),
        logger.colorize(os.getcwd(), logger.YELLOW)
      ))

      with open(file_path, 'w') as f:
        # The config template does not need any templated variables
        f.write(config_template.render())

    logger.ok(
      'Finished creating sample Kubernetes configuration for {}'.format(
        logger.colorize(service_name, logger.YELLOW)
      )
    )

command = KubernetesInit()
