import os
import shutil
import stat
import subprocess

def ensure_directory(directory_path):
  """
  Ensure that the directory exists. If it does not exist, it will be created.

  :param directory_path: path to the desired directory
  :type  directory_path: absolute path string
  """

  try:
    os.stat(directory_path)
  except OSError:
    os.makedirs(directory_path)

  mode = os.stat(directory_path).st_mode

  if not stat.S_ISDIR(mode):
    raise Exception('Path {} already exist and is a file'.format(directory_path))

def remove_directory(directory_path):
  """
  Ensure that the directory does not exists

  :param directory_path: path to the directory
  :type  directory_path: absolute path string
  """

  try:
    shutil.rmtree(directory_path)
  except OSError:
    pass

def copy_mount(src, dst):
  """
  Copy a directory/file "mount" style:
    - If `src` is a directory, then all files and directory contained within
      will be copied inside the `dst` directory. overwriting any existing
      files. Will raise an exception if `dst` already exist and is a file.
    - If `src` is a file, then it will be copied to `dst`, overwriting it.
      Will raise an exception if `dst` is a directory.

  :param src: Source path
  :type  src: str

  :param dst: Copy destination path
  :type  dst: str
  """

  if not os.path.exists(src):
    raise Exception('Path {} does not exist'.format(src))

  src_mode = os.stat(src).st_mode
  dst_mode = None

  if os.path.exists(dst):
    dst_mode = os.stat(dst).st_mode

  if stat.S_ISDIR(src_mode):
    if dst_mode and not stat.S_ISDIR(dst_mode):
      raise Exception('Directory copy destination {} exists and not a directory'.format(dst))

    ensure_directory(dst)
    subprocess.check_output(['cp', '-a', os.path.join(src, '.'), dst])
  else:
    if dst_mode and stat.S_ISDIR(dst_mode):
      raise Exception('File copy destination {} exists and is a directory'.format(dst))

    ensure_directory(os.path.dirname(dst))
    subprocess.check_output(['cp', src, dst])
