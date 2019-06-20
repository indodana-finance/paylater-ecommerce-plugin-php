import sys
from abc import ABCMeta, abstractmethod

from helper import logger

class CommandHandler:
  """
  An abstract class for svctl command handlers. A command will need to implement
  three methods:
    - __init__() to specify what argument parser and validator(s) the command
      will use.
    - _execute() to execute the command.
  """

  __metaclass__ = ABCMeta

  def __init__(self, parser, validators=[]):
    """
    Construct a command handler

    :param parser: Argument parser to use
    :type  parser: Parser

    :param validators: List of Validator to use
    :type  validators: list[Validator]
    """

    self.parser = parser
    self.validators = validators

  def run(self, args):
    """
    Run the command after parsing and validating the command's arguments

    :param args: Positional arguments
    :type  args: list[str]
    """

    kwargs = None

    try:
      kwargs = self.parser.parse(args)
    except Exception as e:
      logger.error(e)
      print()  # Add a margin between error message and svctl help message

      sys.exit(3)

    self._validate(kwargs)
    self._execute(kwargs)

  def _validate(self, kwargs):
    """
    Validate command requirements

    :param kwargs: Parsed keyword arguments
    :type  kwargs: dict
    """

    for validator in self.validators:
      validator.validate(kwargs)

  @abstractmethod
  def _execute(self, kwargs):
    """
    Execute the command

    :param kwargs: Parsed keyword arguments
    :type  kwargs: dict
    """

    raise NotImplementedError
