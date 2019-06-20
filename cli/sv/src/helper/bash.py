import os
import subprocess
import sys

class Bash(object):
  def __init__(self):
    """
    Create a Bash helper class
    """

    self.commands = []

  def queue(self, command):
    """
    Queue a command

    :param command: Shell command
    :type  command: str

    :returns: self
    :rtype: Bash
    """

    self.commands.append(command)

    return self

  def execute(self):
    """
    Execute all queued commands

    :returns: Dictionary containing three objects:
        - stdout: List of standard outputs produced by the queued commands
        - stderr: List of standard errors produced by the queued commands
        - code: Overall exit code
    :rtype: dict
    """

    delimiter = '###DELIMITER###'

    combined_commands = ' && echo "{0}" && echo "{0}" >&2 && ' \
      .format(delimiter) \
      .join(self.commands)

    process = subprocess.Popen(
      [combined_commands],
      shell=True,
      executable='/bin/bash',
      stdout=subprocess.PIPE,
      stderr=subprocess.PIPE,
      universal_newlines=True
    )

    stdout, stderr = process.communicate()

    self.commands = []

    return {
      'stdout': [out.strip() for out in stdout.split(delimiter)],
      'stderr': [err.strip() for err in stderr.split(delimiter)],
      'code': process.returncode
    }

  @staticmethod
  def script(script):
    """
    Execute a Bash script

    :param script: Bash script to execute
    :type  script: str

    :returns: Tuple of (stdout, stderr)
    :rtype: tuple
    """

    process = subprocess.Popen(
      [script],
      shell=True,
      executable='/bin/bash',
      stdout=sys.stdout,
      stderr=sys.stderr,
      universal_newlines=True
    )

    process.communicate()

    return process.returncode

  @staticmethod
  def execl(script):
    """
    Execute a Bash script, replacing the current svctl process with said script.
    This function does not return

    :param script: Bash script to execute
    :type  script: str
    """
    os.execl('/bin/bash', '/bin/bash', '-c', script)
