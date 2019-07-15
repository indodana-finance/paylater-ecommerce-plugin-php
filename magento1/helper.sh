#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential magento1 dev app
popd
