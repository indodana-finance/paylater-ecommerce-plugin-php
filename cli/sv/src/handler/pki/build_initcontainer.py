import argparse
import os
import pendulum
import subprocess
import sys

import template
from config import Config
from constant import docker as docker_constant
from handler import CommandHandler
from handler._parser import ArgparseParser
from handler._validator import ServiceValidator
from helper import bash, files, logger
from service import Service

class BuildInitContainer(CommandHandler):
  """
  Build a PKICTL service certificate InitContainer
  """

  def __init__(self):
    parser = argparse.ArgumentParser()

    parser.add_argument('service_name', help='Service name')
    parser.add_argument('environment', help='Environment')

    parser.add_argument('-e', '--use-existing',
      help='Use existing service certificate rather than generating a new one',
      action='store_true',
      default=False
    )

    parser.add_argument('-c', '--clean',
      help='Remove generated service certificate after the image has been built',
      action='store_true',
      default=False
    )

    parser.add_argument('-d', '--cert-base-dir',
      help='Directory that stores the service certificate directories. ' \
           'Default is PKICTL\'s $HOME/certificates directory',
      default=f'{os.getenv("HOME")}/certificates'
    )

    super(BuildInitContainer, self).__init__(
      parser=ArgparseParser(parser),
      validators=[
        ServiceValidator(service_name='service_name', environment='environment')
      ]
    )

  def _execute(self, kwargs):
    """
    Execute the command

    :param kwargs: Command keyword arguments
    :type  kwargs: dict
    """

    service_name = kwargs['service_name']
    environment = kwargs['environment']
    use_existing = kwargs['use_existing']
    clean = kwargs['clean']
    certificate_base_dir = kwargs['cert_base_dir']

    service = Service(service_name, environment)

    certificate_bundle = f'{service_name}-{environment}'
    certificate_dir = os.path.join(certificate_base_dir, certificate_bundle)

    if os.path.exists(certificate_dir):
      if use_existing:
        logger.info(
          'Using existing service certificate at {cert_dir}',
          cert_dir=logger.colorize(certificate_dir)
        )
      else:
        logger.error(
          'Certificate directory {cert_dir} already exist. Please manually',
          'remove that directory beforehand or use the {flag} flag',
          cert_dir=logger.colorize(certificate_dir),
          flag=logger.colorize('--use-existing')
        )

        sys.exit(1)
    else:
      if use_existing:
        logger.error(
          'Certificate directory {cert_dir} does not exit',
          cert_dir=logger.colorize(certificate_dir)
        )

        sys.exit(1)
      else:
        logger.info(
          'A new service certificate will be created at {cert_dir}',
          cert_dir=logger.colorize(certificate_dir)
        )

    # Ensure the service certificate exist
    if not use_existing:
      pkictl_command = f'./pkictl service certs generate prod {certificate_bundle} --java'

      pkictl_full_command = f"""
      cd {Config().get('svctl_directory')}
      {pkictl_command}
      """

      logger.info(
        'Executing {pkictl_command}...',
        pkictl_command=logger.colorize(pkictl_command)
      )

      pkictl_exit_code = bash.Bash.script(pkictl_full_command)

      if pkictl_exit_code != 0:
        logger.error(
          'PKICTL returned non-zero exit code {exit_code}. Aborting...',
          exit_code=pkictl_exit_code
        )

        sys.exit(1)

    # Build the image
    build_date = pendulum.now().utcnow().format("YYYYMMDD[T]HHmmss")

    image_base_name = f'{service.default_repository}/{certificate_bundle}-pkictl-initcontainer'
    tag = f'{image_base_name}:{build_date}'
    tag_latest = f'{image_base_name}:latest'

    dockerfile = template.get('docker/pkictl_service_cert_dockerfile').render(
      service_name=service_name,
      environment=environment
    )

    docker_build_command = [
      'docker', 'build',
      '--pull',
      '-t', tag,
      '-t', tag_latest,
      '-f', '-',
      certificate_dir
    ]

    logger.info(
      'Executing {docker_build_command}...',
      docker_build_command=logger.colorize(' '.join(docker_build_command))
    )

    docker_build_proc = subprocess.Popen(
      docker_build_command,
      stdin=subprocess.PIPE,
      stdout=sys.stdout,
      stderr=sys.stderr,
      universal_newlines=True
    )

    docker_build_proc.communicate(input=dockerfile)

    if docker_build_proc.returncode != 0:
      logger.error(
        'Docker build returned non-zero exit code {exit_code}. Aborting...',
        exit_code=docker_build_proc.returncode
      )

      sys.exit(1)

    logger.info('Image tagged as {tag}', tag=logger.colorize(tag))
    logger.info('Image tagged as {tag}', tag=logger.colorize(tag_latest))

    logger.ok(
      'Finished building service certificate Docker image for {service_name} ({environment})...',
      service_name=logger.colorize(service_name),
      environment=logger.colorize(environment)
    )

    # Remove generated certificate
    if clean and not use_existing:
      files.remove_directory(certificate_dir)

command = BuildInitContainer()
