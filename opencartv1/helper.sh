#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential opencartv1 dev app
popd
