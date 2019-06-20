import subprocess
import sys

from helper import logger

def can_i(cluster_name, cluster_info, op='create', subj='deployment'):
  kubectl_args = [
    '--context', cluster_name,
    '--namespace', cluster_info.get('namespace', 'default'),
    'auth',
    'can-i',
    op,
    subj
  ]

  exit_code = _exec(kubectl_args)

  return exit_code == 0

def apply(cluster_name, cluster_info, yaml_data):
  kubectl_args = [
    '--context', cluster_name,
    '--namespace', cluster_info.get('namespace', 'default'),
    'apply',
    '-f', '-'
  ]

  logger.info('Executing {}'.format(
    logger.colorize(' '.join(['kubectl'] + kubectl_args), logger.YELLOW)
  ))

  exit_code = _exec(
    kubectl_args,
    input=yaml_data,
    stdout=sys.stdout,
    stderr=sys.stdout
  )

  if exit_code != 0:
    logger.error('Kubectl command exited with code {}'.format(exit_code))

    sys.exit(exit_code)

def _exec(kubectl_args, input=None, stdout=subprocess.PIPE, stderr=subprocess.PIPE):
  proc = subprocess.Popen(
    ['kubectl'] + kubectl_args,
    stdin=subprocess.PIPE,
    stdout=stdout,
    stderr=stderr,
    universal_newlines=True
  )

  proc.communicate(input=input)

  return proc.returncode
