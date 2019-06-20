import os
import shutil
import stat
import subprocess

import template
from config import Config
from helper import bash, common, files, path
from service import Service

def generate(service_name, environment, dry_run=False):
  """
  Generate a Dockerfile based on service and environment

  :param service_name: Name of the service
  :type  service_name: str

  :param environment: Environment
  :type  environment: str

  :returns: Dockerfile content
  :rtype: str
  """

  service = Service(service_name, environment)
  build_directory = path.service_build_directory(service_name, environment)

  # Build-time environment variables
  inherited_host_env_list = common.dict_get(
    service.config,
    ['docker', 'containerize', 'inherited_host_env'],
    default=[]
  )

  inherited_host_envs = {}

  for env in inherited_host_env_list:
    if os.getenv(env) == None:
      raise Exception('Host-inherited environment variable {} is not defined'.format(env))

    inherited_host_envs[env] = os.getenv(env)

  # Build-time mounts
  temporary_mount_directory = common.dict_get(
    service.config,
    ['docker', 'containerize', 'temporary_mount_directory'],
    default='.svctl_docker_mount'
  )

  temporary_mount_directory_archive = '.build_time_archive.tar.gz'

  build_time_mounts = common.dict_get(
    service.config,
    ['docker', 'containerize', 'build_time_mounts'],
    default=[]
  )

  if not dry_run:
    # Clean the temporary mount directory
    files.remove_directory(temporary_mount_directory)
    files.ensure_directory(temporary_mount_directory)

    # Copy mounted files and directories into the temporary mount directory
    for mount in build_time_mounts:
      source_path = mount['mount_directory']
      target_path = mount.get('target_directory', '/').lstrip('/')

      temporary_target_path = os.path.join(
        temporary_mount_directory,
        target_path
      )

      files.copy_mount(source_path, temporary_target_path)

    # Compress the directory. touch-ing the temporary_mount_directory_archive
    # before running tar is required so the file exist and can be properly
    # excluded. If we run the tar command while the excluded file does not
    # exist or we don't use the exclude flag at all, then the command will exit
    # with exit code 1:
    #   tar: .: file changed as we read it
    # That's because there's a file (the .tar.gz file) that's been modified
    # while the tar command is running
    compress_command = bash.Bash()
    compress_command.queue(f'cd {temporary_mount_directory}')
    compress_command.queue(f'touch {temporary_mount_directory_archive}')
    compress_command.queue(f'tar --exclude {temporary_mount_directory_archive} -czf {temporary_mount_directory_archive} .')
    compress_command.execute()

  # Copy the archive containing files in the `temporary_mount_directory` to the
  # container using COPY
  build_time_mount_files = [{
    'mount_directory': os.path.join(temporary_mount_directory, temporary_mount_directory_archive),
    'target_directory': os.path.join('/', temporary_mount_directory_archive)
  }]

  # Extract the archive
  build_time_mount_command = f'cd / && tar -xzf {temporary_mount_directory_archive} --no-same-owner'

  # Render the Dockerfile
  dockerfile_template = template.get('docker/dockerfile')

  return dockerfile_template.render(
    base_image=service.base_image,
    base_repository=service.base_image_repository,
    base_user=service.base_image_user,
    build_directory=build_directory,
    build_image=service.build_image,
    build_repository=service.build_image_repository,
    build_user=service.build_image_user,
    environment=environment,
    service_name=service_name,
    svctl_directory=Config().get('svctl_directory'),
    build_time_envs=inherited_host_envs,
    build_time_mounts=build_time_mount_files,
    build_time_mount_command=build_time_mount_command
  )
