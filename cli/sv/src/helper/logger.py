import sys

BLACK = '\033[30m'
BLUE = '\033[34m'
CYAN = '\033[36m'
DARK_GRAY = '\033[38m'
GREEN = '\033[32m'
LIGHT_GRAY = '\033[37m'
MAGENTA = '\033[35m'
RED = '\033[31m'
YELLOW = '\033[33m'
NORMAL = '\033[39m'

def colorize(msg, color=YELLOW):
  """
  Wrap a string in ANSI color code

  :param msg: Message to colorize
  :type  msg: str

  :key  color: ANSI color code
  :type color: str

  :returns: Colored string
  :rtype: str
  """

  return '{0}{1}{2}'.format(color, msg, NORMAL)

def _log(stream, header, messages, formats):
  stream.write(header)
  stream.write(' '.join(messages).format(**formats))
  stream.write('\n')

  stream.flush()

def info(*messages, **formats):
  """
  Print info message to stdout
  """

  header = colorize('[INFO]  ', CYAN)
  _log(sys.stdout, header, messages, formats)

def ok(*messages, **formats):
  """
  Print ok message to stdout
  """

  header = colorize('[OK]    ', GREEN)
  _log(sys.stdout, header, messages, formats)

def warn(*messages, **formats):
  """
  Print warn message to stderr
  """

  header = colorize('[WARN]  ', YELLOW)
  _log(sys.stderr, header, messages, formats)

def error(*messages, **formats):
  """
  Print error message to stderr
  """

  header = colorize('[ERROR] ', RED)
  _log(sys.stderr, header, messages, formats)
