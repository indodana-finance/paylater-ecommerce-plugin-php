#!/usr/bin/env python3.7

import os
import sys

from config import Config

def _cwd_to_project_dir():
  """
  Change working directory to project root directory
  """

  os.chdir(Config().get('project_directory'))

def main():
  """
  Main handler for svctl. Requires at least one command line argument to
  indicate which handler will be called. This script should not be invoked by
  BCL scripts located in app/ and not called directy from command line.
  """

  subcommand = sys.argv[1]
  args = sys.argv[2:]

  # Imports a module in handler/ according to subcommand passed
  handler_module_path = 'handler.{}'.format(subcommand)
  handler = __import__(handler_module_path, fromlist=[handler_module_path])

  # Change working directory to project root directory
  svctl_directory = os.getcwd()
  _cwd_to_project_dir()
  project_directory = os.getcwd()

  # Save svctl directory path relative to project root directory for future use
  Config().set('svctl_directory', os.path.relpath(svctl_directory, project_directory))

  return handler.command.run(args)

if __name__ == '__main__':
  main()
