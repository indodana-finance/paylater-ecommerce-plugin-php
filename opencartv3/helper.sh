#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential opencartv3 dev app
popd
