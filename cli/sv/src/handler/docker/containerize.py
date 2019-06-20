import os
import pendulum
import shutil
import subprocess
import sys
import textwrap

import template
from handler import CommandHandler
from handler._parser import PositionalParser
from handler._validator import ServiceValidator
from helper import common, files, logger, path
from helper.bash import Bash
from helper.docker import dockerfile
from service import Service

class DockerContainerize(CommandHandler):
  """
  Build a Docker image of a service
  Positional arguments: SERVICE_NAME, ENVIRONMENT
  """

  def __init__(self):
    super(DockerContainerize, self).__init__(
      parser=PositionalParser('service_name', 'environment'),
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

    service = Service(service_name, environment)

    logger.info('Building Docker image for {} ({})...'.format(
      logger.colorize(service_name, logger.YELLOW),
      logger.colorize(environment, logger.YELLOW)
    ))

    temporary_mount_directory = common.dict_get(
      service.config,
      ['docker', 'containerize', 'temporary_mount_directory'],
      default='.svctl_docker_mount'
    )

    # Create temporary dockerignore if the dockerfile does not exist
    dockerignore_path = './.dockerignore'
    dockerignore_exists = os.path.exists(dockerignore_path)

    gitignore_path = './.gitignore'
    gitignore_exists = os.path.exists(gitignore_path)

    create_temp_dockerignore = (not dockerignore_exists) and gitignore_exists

    if create_temp_dockerignore:
      shutil.copyfile(gitignore_path, dockerignore_path)

    tag = self._get_image_name(service, versioned=True)
    tag_latest = self._get_image_name(service, versioned=False)

    # Run the host pre-build script
    service.host_prebuild()

    # Build the Docker image
    build_exit_code = self._exec(
      [
        'docker', 'build',
        '--pull',
        '-t', tag,
        '-t', tag_latest,
        '-f', '-',
        '.'
      ],
      dockerfile.generate(service_name, environment)
    )

    # Remove temporary dockerignore
    if create_temp_dockerignore:
      os.remove(dockerignore_path)

    # Clean the temporary mount directory
    files.remove_directory(temporary_mount_directory)

    if build_exit_code != 0:
      logger.error('Failed building Docker image for {} ({})...'.format(
        logger.colorize(service_name, logger.YELLOW),
        logger.colorize(environment, logger.YELLOW)
      ))

      sys.exit(build_exit_code)

    # Run the host post-build script
    service.host_postbuild()

    logger.info('Image tagged as {}'.format(logger.colorize(tag, logger.YELLOW)))
    logger.info('Image tagged as {}'.format(logger.colorize(tag_latest, logger.YELLOW)))

    logger.ok('Finished building Docker image for {} ({})...'.format(
      logger.colorize(service_name, logger.YELLOW),
      logger.colorize(environment, logger.YELLOW)
    ))

    next_steps = textwrap.dedent("""\
      Next steps:
                1. Push the built images
                    $ {aws_ecr_cmd}
                    $ {docker_push1}
                    $ {docker_push2}

                2. Deploy the service using the built image
                    $ {svctl_deploy_cmd}

                3. Monitor the deployment
                    $ {svctl_kube_login_cmd}
                    $ {kubectl}
    """.format(
      aws_ecr_cmd=logger.colorize('$(aws --profile cermati-ecr ecr get-login --no-include-email --region ap-southeast-1)'),
      docker_push1=logger.colorize(f'docker push {tag}'),
      docker_push2=logger.colorize(f'docker push {tag_latest}'),
      svctl_deploy_cmd=logger.colorize(f'./svctl kube deploy <cluster> {service_name} {environment} {tag.split(":")[1]}'),
      svctl_kube_login_cmd=logger.colorize(f'./svctl kube context login <cluster> {service_name}'),
      kubectl=logger.colorize('kubectl get deployment')
    ))

    logger.ok(next_steps)

  @staticmethod
  def _get_image_name(service, versioned=True):
    """
    Generate service image name, including the repository

    :param service: The service to generate image name of
    :type  service: Service

    :key  versioned: Whether to generate image name with proper versioning or
        not. Properly versioned image name contains build environment name,
        build timestamp, and commit hash; while image name without version only
        contains build environment name.
    :type versioned: bool

    :returns: Service image name
    :rtype: str
    """

    image_name = '{repo}/{image}:{env}'.format(
      repo=service.default_repository,
      image=service.service_name,
      env=service.environment
    )

    if versioned:
      image_name = '{image_name}-{date}-{commit}'.format(
        image_name=image_name,
        date=pendulum.now().utcnow().format('YYYYMMDD[T]HHmmss'),
        commit=DockerContainerize._get_short_commit_hash()
      )

    return image_name

  @staticmethod
  def _get_short_commit_hash():
    """
    Get short commit hash from project Git repository

    :returns: Short commit hash (7 characters)
    :rtype: str
    """

    return Bash() \
      .queue('git log --pretty=format:"%h" -n 1') \
      .execute() \
      .get('stdout')[0]

  @staticmethod
  def _exec(command, input):
    """
    Execute a command with stdin input

    :param command: Shell command and arguments, split into an array according to
        space
    :type  command: list[str]

    :param input: String that will be passed to the command's stdin
    :type  input: str

    :returns: Exit code of the command
    :rtype: int
    """

    proc = subprocess.Popen(
      command,
      stdin=subprocess.PIPE,
      stdout=sys.stdout,
      stderr=sys.stderr,
      universal_newlines=True
    )

    proc.communicate(input=input)

    return proc.returncode

command = DockerContainerize()
