class EnvSource(object):
  """
  Base class for source of environment artifact(s)
  """

  def __init__(self, service):
    self.service = service

  def parse(self, definition, **kwargs):
    raise NotImplementedError
