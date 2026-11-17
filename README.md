# DevOps Work Environment

## About

This repository contains the tools, installation instructions and configurations required to prepare our shared development environment for the DevOps project.

The objective is to ensure that all team members work with the same environment.

## Required Technologies

- Windows 10/11
- WSL 2
- Ubuntu
- Docker Desktop
- Docker Compose
- Git
- Visual Studio Code

## Installation Guide

Follow these steps in order:

1. Install and configure WSL 2.
2. Install Ubuntu.
3. Install Docker Desktop.
4. Enable Docker integration with WSL.
5. Install Git and essential Linux tools.
6. Configure Visual Studio Code.
7. Verify the environment.

## Verification

Run these commands inside Ubuntu:

```bash
docker --version
docker compose version
docker run hello-world
git --version
```

## Repository Structure

- `docs/`: Installation documentation.
- `scripts/`: Environment setup and verification scripts.

## Team

Shared development environment for our university DevOps project.
