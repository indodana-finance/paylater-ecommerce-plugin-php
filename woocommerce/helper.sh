#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential woocommerce dev app
popd
