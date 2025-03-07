<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project

News portal project

# API Documentation

## Register User

**URL:** `POST http://localhost:8000/api/register`

**Headers:**

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

**Body:**

```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

## Resend Verification Email

**URL:** `POST http://localhost:8000/api/email/verification-notification`

**Headers:**

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

**Body:**

```json
{
    "email": "test@example.com"
}
```

## Login (Akan gagal jika email belum diverifikasi)

**URL:** `POST http://localhost:8000/api/login`

**Headers:**

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

**Body:**

```json
{
    "email": "test@example.com",
    "password": "password123"
}
```

## Verify Email (User mengklik link dari email)

**URL:** `GET http://localhost:8000/api/email/verify/{id}/{hash}`

**Notes:** URL ini akan dikirim ke email pengguna.

## Upload Featured Image

**URL:** `POST http://localhost:8000/api/posts/1/featured-image`

**Headers:**

```json
{
    "Authorization": "Bearer {token}",
    "Accept": "application/json"
}
```

**Body:** Form-data

| Key   | Type | Value             |
| ----- | ---- | ----------------- |
| image | File | Pilih file gambar |

## Upload Gallery Images

**URL:** `POST http://localhost:8000/api/posts/1/gallery`

**Headers:**

```json
{
    "Authorization": "Bearer {token}",
    "Accept": "application/json"
}
```

**Body:** Form-data

| Key      | Type | Value                      |
| -------- | ---- | -------------------------- |
| images[] | File | Pilih beberapa file gambar |

## Delete Media

**URL:** `DELETE http://localhost:8000/api/posts/1/media/1`

**Headers:**

```json
{
    "Authorization": "Bearer {token}",
    "Accept": "application/json"
}
```

## Toggle Bookmark (Tambah/Hapus)

**URL:** `POST /api/posts/{post_id}/bookmark`

## Dapatkan Semua Bookmark User

**URL:** `GET /api/bookmarks`

## Cek Status Bookmark

**URL:** `GET /api/posts/{post_id}/bookmark`
