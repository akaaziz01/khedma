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

---

## Setup & Storage Link

Ensure public storage is linked so user avatar uploads are accessible by the web server:

```bash
php artisan storage:link
```

---

## Profile Management [C2]

### API Endpoints (Sanctum Authenticated)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/profile` | Retrieve the authenticated user along with their active role profile (`client_profile` or `provider_profile`). |
| `POST` | `/api/v1/profile` | Update profile information. Accepts both JSON and `multipart/form-data` for avatar image uploads (JPEG, PNG, WebP up to 2 MB). |
| `DELETE` | `/api/v1/profile/avatar` | Delete user avatar from public storage and reset `avatar_path` to `null`. |
| `POST` | `/api/v1/provider/profile` | Legacy provider endpoint delegated to the profile update logic (fixes missing profile bug via `updateOrCreate`). |

### Web Interface

- Route: `/profile` (Blade view `resources/views/profile.blade.php`, extends `layouts.app`).
- Accessible via the **My Profile** link in the navbar user dropdown.

### Manual Testing Guide

1. **Start the local server & link storage**:
   ```bash
   php artisan storage:link
   php artisan serve
   ```
2. **Login**:
   - Navigate to `http://localhost:8000/login`.
   - Log in using an existing client or provider account (or register via `/register`).
3. **Navigate to Profile**:
   - Click the user avatar pill in the top-right header and select **My Profile** (or go directly to `/profile`).
4. **Test Photo Upload**:
   - Click **Upload New Photo** and choose an image (`.jpg`, `.png`, or `.webp` under 2 MB).
   - Observe the immediate client-side preview via `URL.createObjectURL`.
   - Click **Save Changes**. Verify the success toast appears and the new avatar displays immediately in the header.
5. **Test Photo Removal**:
   - Click **Remove Photo** and confirm the dialog.
   - Verify the avatar resets to initials in the form and header.
6. **Test Field Updates & Validation**:
   - Change your name, phone (Tunisian format: 8 digits, optional `+216` prefix), and select a Tunisian governorate.
   - For providers: update business name, category, hourly rate, bio, and add/remove skills tags.
   - Click **Save Changes** and verify changes persist after refresh.
   - Try invalid inputs (e.g. invalid phone number, >2 MB file) to verify inline validation error messages.
