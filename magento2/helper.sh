#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential magento2 dev app
popd
