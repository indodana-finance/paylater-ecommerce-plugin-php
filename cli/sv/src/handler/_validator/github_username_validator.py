import os
import sys

from handler._validator import Validator
from helper import logger

class GithubUsernameValidator(Validator):
  """
  Validate that GITHUB_USERNAME environment variable exist and defined
  """

  def __init__(self):
    """
    Construct a GithubUsernameValidator
    """

    super(GithubUsernameValidator, self).__init__()

  def _validate(self):
    """
    Validate that GITHUB_USERNAME environment variable exist and defined
    """

    github_username = os.getenv('GITHUB_USERNAME')

    if github_username == None or github_username == '':
      logger.error(
        'Environment variable {} is not set. This variable is used to ' \
        'identify yourself when deploying a service. Please add {} to your ' \
        '{} or {} and execute the command again in a new terminal window.'.format(
          logger.colorize('GITHUB_USERNAME', logger.YELLOW),
          logger.colorize('export GITHUB_USERNAME=<your-username>', logger.YELLOW),
          logger.colorize('~/.bashrc', logger.YELLOW),
          logger.colorize('~/.zshrc', logger.YELLOW)
        )
      )

      sys.exit(1)
