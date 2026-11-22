
# Docker Desktop Installation

Docker Desktop provides Docker Engine, Docker Compose, and a graphical interface for managing containers on Windows.

## Prerequisites

- Windows 10 64-bit (build 19045 or later) or Windows 11
- WSL 2 installed and updated
- Virtualization enabled in BIOS/UEFI
- Administrator access

## Installation

1. Download Docker Desktop for Windows from the official Docker website:
   <https://www.docker.com/products/docker-desktop/>
2. Run the installer.
3. Use the WSL 2 backend when prompted.
4. Restart Windows if the installer requests it.
5. Start Docker Desktop and wait until it reports that Docker is running.

## Enable WSL Integration

1. Open Docker Desktop.
2. Select **Settings** > **Resources** > **WSL Integration**.
3. Enable integration for the Ubuntu distribution used for development.
4. Select **Apply & Restart**.

## Verify the Installation

Open Ubuntu and run:

```bash
docker --version
docker compose version
docker run hello-world
```

A successful `hello-world` run confirms that Docker can pull an image and start a container.

You can also run the repository verification script:

```bash
./scripts/check-environment.sh
```
