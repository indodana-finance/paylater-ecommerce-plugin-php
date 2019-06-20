from handler import CommandHandler
from handler._parser import PositionalParser
from handler._validator import ServiceValidator, KubernetesValidator, GithubUsernameValidator
from helper import logger
from service import Service

class KubernetesGenerate(CommandHandler):
  """
  Deploy a service to Kubernetes cluster(s)
  Positional arguments: CLUSTER_NAME, SERVICE_NAME, ENVIRONMENT, VERSION
  """

  def __init__(self):
    super(KubernetesGenerate, self).__init__(
      parser=PositionalParser('cluster_name', 'service_name', 'environment', 'version'),
      validators=[
        ServiceValidator(service_name='service_name', environment='environment'),
        KubernetesValidator(service_name='service_name'),
        GithubUsernameValidator()
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
    cluster_name = kwargs['cluster_name']
    version = kwargs['version']

    service = Service(service_name, environment, version=version)
    yamls = service.generate_kubernetes_yaml()

    print(yamls[cluster_name])

command = KubernetesGenerate()
