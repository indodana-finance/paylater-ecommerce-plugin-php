#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential opencartv2 dev app
popd
