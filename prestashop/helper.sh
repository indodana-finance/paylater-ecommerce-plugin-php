#!/bin/bash

pushd ../../../cli
  ./dbctl mysql role get-credential prestashop dev app
popd