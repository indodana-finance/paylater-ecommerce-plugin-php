#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential testenv dev app
popd