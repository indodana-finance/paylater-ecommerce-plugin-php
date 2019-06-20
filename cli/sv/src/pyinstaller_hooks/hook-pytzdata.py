from PyInstaller.utils.hooks import collect_data_files

# See https://github.com/pyinstaller/pyinstaller/issues/3528#issuecomment-443539780
datas = collect_data_files('pytzdata', include_py_files=True)
