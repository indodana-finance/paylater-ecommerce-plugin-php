from abc import ABCMeta, abstractmethod

class Parser:
  """
  Abstract class for command arguments parser
  """

  __metaclass__ = ABCMeta

  @abstractmethod
  def parse(self, args):
    """
    Abstract method to parse arguments. Should raise an Exception if the parsing
    failed.

    :param args: Arguments
    :type  args: list[str]

    :returns: Parsed arguments as dictionary
    :rytpe: dict
    """

    raise NotImplementedError
