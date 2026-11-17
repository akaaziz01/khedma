#!/bin/bash

set -e

echo "================================"
echo " DevOps Environment Setup"
echo "================================"

echo "[1/3] Updating Ubuntu..."
sudo apt update
sudo apt upgrade -y

echo "[2/3] Installing essential tools..."
sudo apt install -y git curl wget unzip zip nano ca-certificates

echo "[3/3] Checking installations..."
git --version
curl --version

echo "================================"
echo " Environment setup completed!"
echo "================================"

echo "Docker must be installed separately through Docker Desktop."
