#!/bin/bash

ENVIRONMENT=$1

pushd ../../../cli
  ./dbctl mysql role get-credential opencartv1 ${ENVIRONMENT}  app
popd
