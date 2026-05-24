# Laravel SPA — by [Sakib](https://github.com/saakiiib)

By default, every link click in Laravel loads a full new page — HTML, CSS, JS, fonts, everything re-downloads. SPA (Single Page Application) navigation skips that. Only the content changes, the rest of the page stays. Faster, smoother, no white flash between pages.

Most Laravel SPA solutions force you to either abandon Blade entirely (Inertia) or add jQuery (pjax). This package adds that same speed on top of your existing Blade views — no rewrite, nothing changes except how fast it feels.

---

## Quick Start

```bash
composer require saakiiib/laravel-spa
php artisan vendor:publish --tag=spa-assets
```
> Add `/public/vendor` to your `.gitignore` to avoid committing published assets.

Then see [Full Setup Guide](#full-setup-guide) below.

---

## Full Setup Guide

### 1. Layout file

Add `@spaContent` to your content wrapper and `@spaEngine` before `</body>`.

Works with both `@extends` and `x-layout`:

```blade
{{-- @extends style --}}
<div @spaContent>
    @yield('content')
</div>
@spaEngine
```

```blade
{{-- x-layout style --}}
<div @spaContent>
    {{ $slot }}
</div>
@spaEngine
```

### 2. Navigation links

Add `@spa` to any anchor you want SPA navigation on. Links without `@spa` work normally.

```blade
<a href="/" @spa>Home</a>
<a href="/about" @spa>About</a>
<a href="/logout">Logout</a>  {{-- normal full reload --}}
```

### 3. Controller

No trait, no import, no setup — just return `spa()`:

```php
public function index()
{
    return spa('pages.home');
}

public function about()
{
    return spa('pages.about', compact('data'));
}
```

### 4. Page views

No changes needed. Your existing Blade views work as-is:

```blade
{{-- @extends style --}}
@extends('layouts.app')
@section('content')
    <h1>Hello</h1>
@endsection
```

```blade
{{-- x-layout style --}}
<x-layout>
    <h1>Hello</h1>
</x-layout>
```

---

## What works out of the box

- URL updates, back/forward button, refresh, direct links — all work
- Per-page `<style>` and `<script>` load and unload on every navigation
- Session expiry redirects cleanly instead of breaking
- Works with both `@extends` and `x-layout` — no difference in setup

---

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12

---

## Contributing

Found a bug or want to improve something? PRs are welcome on [GitHub](https://github.com/saakiiib/laravel-spa).

---

## License

MIT — [Sakib](https://github.com/saakiiib)