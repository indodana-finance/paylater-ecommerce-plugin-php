from handler._parser import Parser

class PositionalParser(Parser):
  """
  Positional argument parser
  """

  def __init__(self, *keys):
    """
    Construct a positional argument parser

    :param keys: List of keywords for each position in the argument
    :type  keys: list[str]
    """
    self.keys = keys

  def parse(self, args):
    """
    Parse arguments. The length of passed arguments should match with the length
    of the keywords list, otherwise the parser will raise an Exception.

    :param args: Arguments
    :type  args: list[str]

    :returns: A dictionary that maps the list of keywords with passed arguments
        according to their order
    :rtype: dict
    """

    if len(self.keys) != len(args):
      raise Exception('Length of the arguments does meet expectation')

    return {key: arg for key, arg in zip(self.keys, args)}
