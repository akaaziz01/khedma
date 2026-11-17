#!/bin/bash

echo "===== DEVOPS ENVIRONMENT CHECK ====="

echo ""
echo "Ubuntu:"
lsb_release -a

echo ""
echo "Git:"
git --version

echo ""
echo "Docker:"
docker --version

echo ""
echo "Docker Compose:"
docker compose version

echo ""
echo "Docker Test:"
docker run hello-world

echo ""
echo "===== CHECK COMPLETED ====="
