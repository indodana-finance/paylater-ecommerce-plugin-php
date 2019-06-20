import sys
from handler._parser import Parser

class ArgparseParser(Parser):
  """
  Argument parser using argparse
  """

  def __init__(self, parser):
    """
    Construct a positional argument parser

    :param parser: argparse ArgumentParser
    :type  parser: ArgumentParser
    """
    self.parser = parser

  def parse(self, args):
    """
    Parse arguments

    :param args: Arguments
    :type  args: list[str]

    :returns: A dictionary that maps the list of keywords with passed arguments
        according to their order
    :rtype: dict
    """

    try:
      return vars(self.parser.parse_intermixed_args(args))
    except SystemExit:
      # Add a padding between argparse error message and BCL/SVCTL help message
      print()

      sys.exit(3)
