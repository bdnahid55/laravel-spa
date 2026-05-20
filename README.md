# Laravel SPA

SPA-speed navigation for Laravel Blade. No jQuery required. No Livewire. No Inertia. No build step. Just drop in a trait and a script tag and your app navigates without full page reloads.

Tested with Laravel 10, 11, and 12.

---

## Why I built this

I was working on a Blade-based project and didn't want to pull in Livewire or Inertia just to stop the browser from doing full reloads on every link click. The existing PJAX packages all depend on jQuery and use middleware to crawl HTML — felt like overkill. So I wrote this instead.

The whole thing is a trait you use in your controllers and a small vanilla JS file. That's it.

---

## How it works

When a user clicks a link, the JS intercepts it and sends a fetch request with an `X-Frontend-SPA: true` header. Your controller detects that header and returns a JSON payload with just the page content, styles, scripts, and title instead of a full HTML document. The JS swaps the content area and updates the browser history. Layout, nav, footer — all stay in place.

First load is always a normal full page request, so SEO and auth work exactly as before.

---

## Installation

```bash
composer require sakib/laravel-spa
```

Publish the JS file:

```bash
php artisan vendor:publish --tag=spa-assets
```

Then include it in your layout before the closing `</body>`:

```html
<script src="{{ asset('vendor/laravel-spa/spa-engine.js') }}"></script>
```

---

## Usage

### With @extends / @section (traditional Blade)

Add the trait to your controller:

```php
use Sakib\LaravelSpa\RendersSpaView;

class FrontendController extends Controller
{
    use RendersSpaView;

    public function home()
    {
        return $this->renderSpa('frontend.home', compact('products'));
    }
}
```

Your layout needs a `data-spa-content` attribute on the main content wrapper:

```html
<main data-spa-content>
    @yield('content')
</main>
```

Your page views stay exactly the same as normal Blade:

```blade
@extends('layouts.app')

@section('title', 'Home')

@section('style')
    <style>
        .hero { background: #1a3c6e; }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1>Hello</h1>
    </div>
@endsection

@section('script')
    <script>
        console.log('home loaded');
    </script>
@endsection
```

### With x-layout components (modern Blade)

Your layout component needs `data-spa-layout-style` on its own style blocks and `data-spa-layout-script` on its own script blocks so the package knows what belongs to the layout vs what belongs to the page:

```html
<!-- resources/views/components/app-layout.blade.php -->
<head>
    <style data-spa-layout-style>
        /* your global styles */
    </style>

    {{ $style ?? '' }}
</head>
<body>
    <main data-spa-content>
        {{ $slot }}
    </main>

    <script src="..." data-spa-layout-script></script>
    <script data-spa-layout-script>
        /* your global scripts */
    </script>
    <script src="{{ asset('vendor/laravel-spa/spa-engine.js') }}"></script>

    {{ $script ?? '' }}
</body>
```

Page views are clean — no attributes needed anywhere:

```blade
<x-app-layout title="Contact">

    <x-slot:style>
        <style>
            .contact-card { border-radius: 12px; }
        </style>
    </x-slot:style>

    <div class="container py-5">
        <!-- your content -->
    </div>

    <x-slot:script>
        <script>
            $('#sendBtn').on('click', function() {
                // works fine, jQuery is always available from layout
            });
        </script>
    </x-slot:script>

</x-app-layout>
```

---

## Link handling

By default every internal link is intercepted. To opt a link out:

```html
<a href="/logout" data-spa="off">Logout</a>
```

Links with `target="_blank"`, `download`, `mailto:`, `tel:`, or external origins are automatically ignored.

---

## Active nav links

Add `data-spa-link="true"` to your nav links and the package will toggle an `active` class on the current page automatically:

```html
<a href="{{ route('home') }}" data-spa-link="true">Home</a>
```

---

## NProgress support

If NProgress is loaded on the page the package will start and stop it automatically during navigation. No configuration needed.

---

## Programmatic navigation

```js
window.spaNavigate('/some-page');

// with options
window.spaNavigate('/some-page', { push: true, scroll: false });
```

---

## Comparison with existing packages

| | spatie/laravel-pjax | jacobbennett/pjax | sakib/laravel-spa |
|---|---|---|---|
| jQuery required | yes | yes | no |
| Middleware based | yes | yes | no (trait per controller) |
| JSON payload | no | no | yes |
| x-layout support | no | no | yes |
| Script lifecycle | no | no | yes |
| Zero dependencies | no | no | yes |

---

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12

---

## License

MIT
