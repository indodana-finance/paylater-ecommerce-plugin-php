from handler import CommandHandler
from handler._parser import PositionalParser
from handler._validator import ServiceValidator
from helper.docker import dockerfile

class DockerGenerateDockerfile(CommandHandler):
  """
  Generate a dockerfile for a service
  Positional arguments: SERVICE_NAME, ENVIRONMENT
  """

  def __init__(self):
    super(DockerGenerateDockerfile, self).__init__(
      parser=PositionalParser('service_name', 'environment'),
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

    print(dockerfile.generate(service_name, environment, dry_run=True))

command = DockerGenerateDockerfile()
