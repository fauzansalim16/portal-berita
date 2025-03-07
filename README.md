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

## Kelola Kategori

### Create Category

**URL:** `POST http://localhost:8000/api/categories`

**Headers:**

```json
{
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

**Body:**

```json
{
    "name": "Teknologi",
    "slug": "teknologi",
    "description": "Kategori untuk artikel teknologi"
}
```

### Get All Categories

**URL:** `GET http://localhost:8000/api/categories`

**Headers:**

```json
{
    "Authorization": "Bearer {token}"
}
```

## Membuat Post Baru (POST Request)

**URL:** `POST http://localhost:8000/api/posts`

**Headers:**

```json
{
    "Authorization": "Bearer {your_token}",
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

**Body:**

```json
{
    "category_id": 1,
    "title": "Memahami Laravel Caching",
    "slug": "memahami-laravel-caching",
    "content": "Laravel menyediakan sistem caching yang mudah digunakan untuk meningkatkan performa aplikasi. Artikel ini akan menjelaskan bagaimana mengimplementasikan caching secara efektif.",
    "excerpt": "Pengenalan sistem caching di Laravel untuk meningkatkan performa aplikasi",
    "is_published": true,
    "published_at": "2024-03-06 10:00:00"
}
```

### Cara Kerja Cache pada Create Post

**Proses Request:**

1. Request diterima oleh `PostController@store`
2. Data divalidasi melalui `PostRequest`
3. `PostRepository->create()` dipanggil dengan data yang valid

**Proses di Repository:**

```php
public function create(array $data)
{
    $post = $this->post->create($data);
    $this->clearCache();
    return $post;
}

private function clearCache()
{
    Cache::forget('posts.all');
    Cache::forget('posts.published');
}
```

**Proses Event:**

1. Model Observer `PostObserver` terdeteksi adanya post baru
2. Event `PostChanged` dipicu dengan action `created`
3. Listener `ClearPostCache` menangani event tersebut
4. Cache yang terkait dengan post dihapus

## Mendapatkan Post (GET Request)

### Request ke API (Single Post)

**URL:** `GET http://localhost:8000/api/posts/1`

**Headers:**

```json
{
    "Authorization": "Bearer {your_token}",
    "Accept": "application/json"
}
```

### Request ke API (Semua Post)

**URL:** `GET http://localhost:8000/api/posts`

### Cara Kerja Cache pada Get Post

**Alur Kerja Cache secara Keseluruhan:**

**Saat Read Operation:**

1. Sistem mencoba mengambil data dari cache terlebih dahulu
2. Jika tidak ada di cache, query database dijalankan
3. Hasil query disimpan ke cache untuk request berikutnya
4. TTL (Time To Live) diatur 6 jam untuk post dan 24 jam untuk kategori

**Saat Write Operation (Create/Update/Delete):**

1. Data diperbarui di database
2. Observer mendeteksi perubahan dan memicu event
3. Event listener menghapus cache yang terkait
4. Permintaan read berikutnya akan mengisi ulang cache dengan data terbaru
