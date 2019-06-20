def dict_get(obj, path, default=None):
  """
  Get element of a dictionary based on a path

  :param obj: Dictionary to get element from
  :type  obj: dict

  :param path: List of string, indicating path to the element. Ex: ['a', 'b']
      means that the element located in obj['a']['b'] will be returned
  :type  path: list[str]

  :key  default: Value to be returned when the element does not exist
  :type default: object
  
  :returns: Element of the dictionary
  :rtype: object
  """

  if len(path) == 0:
    return obj

  try:
    value = obj[path[0]]
  except (KeyError, TypeError):
    return default

  return dict_get(value, path[1:], default=default)

def dict_set(obj, path, value):
  """
  Set element of a dictionary based on a path. This function will mutate the
  passed dictionary.

  :param obj: Dictionary to set element of
  :type  obj: dict

  :param path: List of string, indicating path to the element. Ex: ['a', 'b']
      means that the element located in obj['a']['b'] will be updated with value
  :type  path: list[str]

  :param value: Value to be assigned to element indicated by path
  :type  value: object
  """

  if len(path) == 1:
    obj[path[0]] = value

    return

  field = None

  if isinstance(obj, dict):
    field = obj.get(path[0], None)
  elif isinstance(obj, list):
    field = obj[path[0]]

  if field == None:
    obj[path[0]] = {}

  return dict_set(obj[path[0]], path[1:], value)

def dict_merge(dict_a, path_a, dict_b, path_b):
  """
  Merge elements of two dictionaries. If the elements are dict or list, the
  content will be combined. This function will mutate the first dictionary
  passed.

  :param dict_a: The dictionary to merge into
  :type  dict_a: dict

  :param path_a: Path to the element to be merged into
  :type  path_a: list[str]

  :param dict_b: The dictionary to merge from
  :type  dict_b: dict

  :param path_b: Path to the element to be merged from
  :type  path_b: list[str]
  """

  obj_a = dict_get(dict_a, path_a)
  obj_b = dict_get(dict_b, path_b)

  if obj_b != None:
    if (obj_a != None) and (isinstance(obj_b, list)):
      dict_set(dict_a, path_a, obj_a + obj_b)
    elif (obj_a != None) and (isinstance(obj_b, dict)):
      obj_merge = dict(obj_a)
      dict_update(obj_merge, obj_b)

      dict_set(dict_a, path_a, obj_merge)
    else:
      dict_set(dict_a, path_a, obj_b)

def dict_update(dict_a, dict_b):
  """
  Merge two dictionaries recursively, the second dictionary will add or replace
  elements in the first dictionary.

  :param dict_a: The dictionary to merge into
  :type  dict_a: dict

  :param dict_b: The dictionary to merge from
  :type  dict_b: dict

  :returns: The first dictionary, mutated
  :rtype: dict
  """

  for key, value in dict_b.items():
    if isinstance(value, dict):
      if dict_a.get(key) == None:
        dict_a[key] = {}

      if isinstance(dict_a.get(key), dict):
        dict_a[key] = dict_update(dict_a.get(key), value)
    else:
      dict_a[key] = value

  return dict_a
