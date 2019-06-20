import jinja2
import os

def get(template_path):
  """
  Retrieve a jinja2 template from file

  :param template_path: Path to the jinja2 template relative to the template/
      directory. If not found, the path will be assumed to be absolute.
  :type  template_path: str

  :returns: Jinja2 template object
  :rtype: jinja2.Template
  """

  template_dir_path = os.path.dirname(os.path.realpath(__file__))

  # List of template paths to try
  paths = [
    os.path.join(template_dir_path, template_path),
    os.path.join(template_dir_path, '{}.j2'.format(template_path)),
    template_path
  ]

  for path in paths:
    if not os.path.exists(path):
      continue

    return jinja2.Template(open(path, 'r').read())

  raise IOError('Template {} does not exist'.format(template_path))
