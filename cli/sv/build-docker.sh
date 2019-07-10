#!/bin/bash

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

pushd $SCRIPT_DIR

mkdir -p dist
mkdir -p ../.env.sv-docker

docker run --rm -ti \
  --user $(id --user):$(id --group) \
  --volume $(pwd)/requirements.txt:/svctl/requirements.txt:ro \
  --volume $(pwd)/src:/svctl/src:ro \
  --volume $(pwd)/dist:/svctl_dist:rw \
  --volume $(pwd)/../.env.sv-docker:/tmp/venv:rw \
  svctl-build /bin/bash -c "
    set -e

    cp -r /svctl /tmp/svctl

    pushd /tmp/svctl
      if [[ ! -f /tmp/venv/bin/activate ]]; then
        virtualenv --python "$(which python3.7)" /tmp/venv
      fi

      source /tmp/venv/bin/activate

      pip install --no-cache-dir -r requirements.txt

      pushd src
        pyinstaller -y main.spec
      popd
    popd

    mv /tmp/svctl/src/dist/main /svctl_dist/main
"

popd
