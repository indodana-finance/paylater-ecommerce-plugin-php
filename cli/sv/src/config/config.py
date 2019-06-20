import os
import sys
import yaml

import template
from helper import logger

class Singleton(type):
  _instances = {}

  def __call__(cls, *args, **kwargs):
    if cls not in cls._instances:
      cls._instances[cls] = super(Singleton, cls).__call__(*args, **kwargs)

    return cls._instances[cls]

_Config = Singleton('_Config', (), {})

class Config(_Config):
  """
  Singleton class to store svctl configurationss
  """

  __metaclass__ = Singleton

  def __init__(self):
    self.config_filename = 'svctl.yml'
    self._validate()

    self.config = yaml.full_load(open(self.config_filename))

  def get(self, config_name):
    return self.config.get(config_name)

  def set(self, config_name, config_value):
    self.config[config_name] = config_value

  def _validate(self):
    if not os.path.exists(self.config_filename):
      logger.error(
        'SVCTL config file {} did not exist and has been created in {}. Please ' \
        'review that file\'s content before running any SVCTL command.'.format(
          logger.colorize(self.config_filename, logger.YELLOW),
          logger.colorize(os.getcwd(), logger.YELLOW),
        )
      )

      with open(self.config_filename, 'w') as f:
        f.write(template.get('svctl/svctl.yml').render())

      sys.exit(1)
