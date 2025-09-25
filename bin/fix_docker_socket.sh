#!/usr/bin/env bash

ROOT_DOCKER_SOCKET="/var/run/docker.sock"

if [ -e "$ROOT_DOCKER_SOCKET" ]; then
    sudo mv "$ROOT_DOCKER_SOCKET" "${ROOT_DOCKER_SOCKET}_old"
fi

sudo ln -s "$HOME/.docker/desktop/docker.sock" "$ROOT_DOCKER_SOCKET"
