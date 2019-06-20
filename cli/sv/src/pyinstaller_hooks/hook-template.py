from PyInstaller.utils.hooks import collect_data_files

# This is required because the `template` directory contains static files that
# are not directly imported by Python.
datas = collect_data_files('template', include_py_files=True)
