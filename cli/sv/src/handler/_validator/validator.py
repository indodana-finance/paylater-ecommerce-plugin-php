from abc import ABCMeta, abstractmethod

class Validator:
  """
  Abstract class for command arguments validator
  """

  __metaclass__ = ABCMeta

  def __init__(self, **mapping):
    """
    Construct an argument validator

    :param mapping: Dictionary (passed as keyword arguments) indicating what
        fields should be fetched by _validate() from its passed mapped_args.
        Example: _validate() will vallidate two arguments, `a` and `b`, but the
        args dictionary passed into _validate() used different naming scheme,
        e.g. `arg_a` and `arg_b`. To properly validate this, pass a=arg_a' and
        b='arg_b' to this constructor.
    :type  mapping: dict
    """

    self.mapping = mapping

  def validate(self, kwargs):
    """
    Validate a parsed arguments. If any of the keyword arguments of this class'
    constructor is None, the default value defined in _validate() method
    signature will be used.

    :param kwargs: Parsed arguments
    :type  kwargs: dict or list[str]
    """

    mapped_args = {}

    for validate_key, args_key in self.mapping.items():
      if args_key == None:
        continue

      mapped_args[validate_key] = kwargs[args_key]

    self._validate(**mapped_args)

  @abstractmethod
  def _validate(self, **mapped_args):
    """
    Abstract method to validate a parser according to keyword argument
    `mapped_args`. This method should call sys.exit() with appropirate exit code
    on failed validation.

    :param mapped_args: Parsed arguments
    :type  mapped_args: dict
    """

    raise NotImplementedError
