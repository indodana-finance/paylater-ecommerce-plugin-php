def services_directory():
  """
  Returns relative path to svctl services directory

  :returns: Relative path to svctl services directory
  :rtype: str
  """

  return '.services.d'

def service_definition_directory(service_name):
  """
  Returns relative path to service directory

  :param service_name: Name of the service
  :type  service_name: str

  :returns: Relative path to service directory
  :rtype: str
  """

  return '{}/{}'.format(services_directory(), service_name)

def service_definition_script(service_name):
  """
  Returns relative path to service definition script

  :param service_name: Name of the service
  :type  service_name: str

  :returns: Relative path to service definition script
  :rtype: str
  """

  return '{}/service'.format(service_definition_directory(service_name))

def service_definition_config(service_name):
  """
  Returns relative path to service information file

  :param service_name: Name of the service
  :type  service_name: str

  :returns: Relative path to service information file
  :rtype: str
  """

  return '{}/config.yml'.format(service_definition_directory(service_name))

def service_build_directory(service_name, environment):
  """
  Returns relative path to service information file

  :param service_name: Name of the service
  :type  service_name: str
  
  :param environment: Service environment
  :type  environment: str

  :returns: Relative path to service information file
  :rtype: str
  """

  return '.build/{}/{}'.format(
    service_name,
    environment
  )

def kubernetes_directory(service_name):
  """
  Returns relative path to service Kubernetes directory

  :param service_name: Name of the service
  :type  service_name: str

  :returns: Relative path to service Kubernetes directory
  :rtype: str
  """

  return '{}/kubernetes'.format(service_definition_directory(service_name))
