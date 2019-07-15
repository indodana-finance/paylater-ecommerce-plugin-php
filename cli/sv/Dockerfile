FROM ubuntu:16.04

RUN apt-get update && \
  apt-get install -y software-properties-common && \
  add-apt-repository ppa:deadsnakes/ppa -y && \
  apt-get update

RUN apt-get install -y python3.7 python3.7-dev python3-pip && \
  pip3 install virtualenv

RUN apt-get install -y liblzma-dev patchelf
