from PyInstaller.utils.hooks import collect_submodules

# This is required because the `handler` submodule (located in sv/src/handler)
# is loaded dynamically using `__import__`. To make sure that PyInstaller
# include all the handlers in the binary, we need to add this hook.
hiddenimports = collect_submodules('handler')
