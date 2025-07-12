# Lexi Translate

<p align="center">
  <a href="https://omaralalwi.github.io/lexi-translate" target="_blank">
    <img src="https://raw.githubusercontent.com/omaralalwi/lexi-translate/master/public/images/lexi-translate-banner.jpg" alt="lexi translate banner" width="600">
  </a>
</p>

Easily manage translations for multilingual Eloquent models using morph relationships and caching.

Its lightweight design and flexibility make it an excellent choice for applications needing multi-language support with minimal performance overhead.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Defining LexiTranslatable Models](#defining-lexitranslatable-models)
  - [update or Create Translations](#update-or-Create-translations)
  - [Retrieving Translations](#retrieving-translations)
  - [More Examples](#more-examples)
  - [Helper Functions](#helper-functions)
  - [Usage in Queries](#Usage-in-Queries)
  - [Cache Handling](#cache-handling)
  - [Using middlewares](#using-middlewares-for-locale-management)
- [Testing](#testing)
- [Alternative Solutions](#alternative-solutions)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Features](#features)
- [Security](#security)
- [License](#license)
- [Contributors](#contributors)
- [Helpful Packages](#helpful-open-source-packages)

## Installation

You can install the package via Composer:

```bash
composer require omaralalwi/lexi-translate
```

### Publishing Configuration File

```bash
php artisan vendor:publish --tag=lexi-translate
```

update table name (if you need, before migration) or any thing in config file if you need .

### Publishing Migration File (optional)

```bash
php artisan vendor:publish --tag=lexi-migrations
```

### Migration for `translations` Table

Run the following command to create the `translations` table:

```bash
php artisan migrate
```

## Usage

### Defining LexiTranslatable Models

To use the package, include the `LexiTranslatable` trait in your Eloquent models, and add translated attributes in `$translatableFields` array:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Omaralalwi\LexiTranslate\Traits\LexiTranslatable;

class Post extends Model
{
    use LexiTranslatable;

    protected $translatableFields = ['title', 'description'];
}
```

### Update or Create Translations

You can use `setTranslations` method to create or update bulk translations for a model in a single step:

```php
$post = Post::find(1);
// must same following format
$post->setTranslations([
    'ar' => [
        'name' => 'العنوان باللغة العربية',
        'description' => 'الوصف باللغة العربية',
    ],

    'en' => [
        'name' => 'English language Title',
        'description' => 'description in English language',
    ],
]);

```

OR You can use `setTranslation` method to create or update one translation for a model in a single step:

```php
$post->setTranslation('title', 'en', 'English Language Title');
$post->setTranslation('description', 'en', 'English Language description');

$post->setTranslation('title', 'ar', 'عنوان باللغة العربية');
$post->setTranslation('description', 'ar', 'وصف باللغة العربية');
```

**Note** you can add translated `name` and `description` for `Post` model even if `Post` model did not has (`name` and `description`) attributes .

### Retrieving Translations

**Important Note:** To get better performance , **Do Not Depend on `translations`** relation directly when return translations, because it did not use cache Never.
it did not use cache to keep it return `MorphMany` relation , it Return fresh translations from DB , So you can depend on it to create and update translations .

**To retrieve translations, simply use `transAttr` method :**

By default it return default app local, else you can specify local.

```php
// get title and description in default app local
$title = $post->transAttr('title');
$title = $post->transAttr('description');

// or get title and description in specific local
$titleInArabic = $post->transAttr('title', 'ar');
$titleInEnglish = $post->transAttr('title', 'ar');
```

### More Examples

you can find more detail examples in **[Examples File](examples.md)** .

### Helper Functions

you can use `lexi_locales` to get supported locals as array, depend on `supported_Locales` in config file.

### Usage in Queries

it is easy to use the `scopeSearchByTranslation` and `scopeFilterByTranslation` methods:

#### search by Translated attribute

```php
$posts = Post::searchByTranslation('title', 'keyword')->get();
```

####  Specify Locale

```php
$posts = Post::searchByTranslation('title', 'keyword', 'ar')->get();
```

#### Filter Posts by Exact Translated Description

```php
$posts = Post::filterByTranslation('description', 'Specific Translated Text')->get();
```


### Cache Handling

**Disable Cache**:

by default the cache enabled, you can disable it  by make `use_cache` = false , in `config/lexi-translate.php` file

**Cache Management**:

Lexi Translate automatically caches translations to boost performance.
Also Cache is cleared automatically when translations are updated or deleted by `booted` function in `Translation` model .

**Clear Model Cache Manually**:

If you need to manually clear the cache, you can do so `$model->clearTranslationsCache()` for ex :

```php
$post->clearTranslationsCache();
```

---
**Note**:

Please note that the `supported_Locales` setting in the configuration file defines the locales that will be handled by the cache by default.
If you add additional locales for translations, make sure to include them in the `supported_Locales` list to ensure proper cache handling. Failing to do so may result in cache issues for locales not added to the list.

---

### Using Middlewares for Locale Management

**(this is Optional)**

**This section is optional , it is additional features to handle language switching for API Or Web , without need to install another package .**

LexiTranslate provides built-in middlewares to handle locale switching seamlessly for both web and API requests. 
These middlewares simplify the process of dynamically setting the application's locale based on user input or request headers.


#### **1 . WebLocalized Middleware**

The `WebLocalized` middleware is designed to handle locale switching for web requests. It determines the locale based on the following order of priority:
- The `locale` route parameter.
- The `locale` query string parameter.
- The current session's locale.
- The `locale` stored in cookies.
- The application's default locale.

#### Registering the Middleware

```php
// Other middlewares...
'localized.web' => \Omaralalwi\LexiTranslate\Middleware\WebLocalized::class,
```
[Register Middleware in Laravel](https://laravel.com/docs/11.x/middleware#registering-middleware)

#### Applying the Middleware to Routes

just add `locale` prefix for all routes that want to apply multilingual for them .

```php
Route::prefix('{locale}')->middleware('localized.web')->group(function () {
     // your routes
});
```
OR
```php
Route::middleware(['localized.web'])->group(function () {
    Route::get('/{locale}/dashboard', function () {
        return view('dashboard');
    });
});
```

#### **2. ApiLocalized Middleware**

The `ApiLocalized` middleware is designed for API requests. It sets the application's locale based on the value of a custom header defined in your configuration file (`api_locale_header_key`). If the header is not provided, it defaults to the application's default locale.

#### Registering the Middleware

```php
 // Other middlewares...
'localized.api' => \Omaralalwi\LexiTranslate\Middleware\ApiLocalized::class,
```

#### Applying the Middleware to API Routes

```php
Route::middleware(['localized.api'])->group(function () {
        // your routes
});
```

---

## Features

- **Dynamic Morph Relationships:** Manage translations across different models with ease, thanks to its dynamic morph able relationships.
- **Automatic Caching:** Enjoy enhanced performance as translations are automatically cached and invalidated, ensuring quick access and updates.
- **Fallback Mechanism:** Never worry about missing translations—Lexi Translate falls back to the default language if a translation is not available.
- **Simple, Intuitive API:** A clean and consistent API for adding, retrieving, and managing translations.
- **Eloquent-Friendly:** Seamlessly integrates with Laravel's Eloquent ORM, making it easy to work with translated data while maintaining the power of Laravel’s query builder.
-  **Search and Filter:** Scopes for search and filters by translations .
- **Built-in middlewares** to handle locale switching seamlessly for both web and API requests.
- **Feature Tests:** supported with Feature Tests .
- **Customize table name:** in config file you can change `table_name` to any name as you want.

## Testing

To run the tests for this package:

```bash
composer test
```

---

## Alternative Solutions

If Lexi Translate doesn't fully meet your application's needs, you may also consider these popular alternatives:

- **[Spatie Laravel Translatable](https://github.com/spatie/laravel-translatable):**  
  Stores translations in a JSON column within the main table. Best suited for smaller applications with simple multilingual requirements.

- **[Astrotomic Laravel Translatable](https://github.com/Astrotomic/laravel-translatable):**  
  Similar to Spatie's package but includes additional features like better locale handling. It’s an excellent choice for lightweight multilingual support.

Both packages offer robust solutions for managing translations but rely on JSON-based storage. If you require scalable, relational storage with built-in caching and dynamic morph relationships, **Lexi Translate** is the better choice for large-scale or performance-critical applications.

--- 

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent updates.

## Contributing

We welcome contributions! If you'd like to contribute, please check the [CONTRIBUTING](CONTRIBUTING.md) guide for details.

### Contributors

This project exists thanks to all the people who contribute.

- [Omar alalwi](https://github.com/omaralalwi)

## Security

If you discover any security-related issues, please email [omaralwi2010@gmail.com](mailto:omaralwi2010@gmail.com) instead of using the issue tracker.

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.

---

## Contributors ✨

Thanks to these wonderful people for contributing to this project! 💖

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/omaralalwi">
        <img src="https://avatars.githubusercontent.com/u/25439498?v=4" width="100px;" alt="Omar Al Alwi"/>
        <br />
        <sub><b>Omar Al Alwi</b></sub>
      </a>
      <br />
      🏆 Owner
    </td>
    <!-- Contributors -->
    <td align="center">
      <a href="https://github.com/HamzaHassanM">
        <img src="https://avatars.githubusercontent.com/u/62448602?v=4" width="100px;" alt="Contributor Name"/>
        <br />
        <sub><b>Hamza Hasan</b></sub>
      </a>
      <br />
      💻 Contributor
    </td>
  </tr>
</table>

Want to contribute? Check out the [contributing guidelines](./CONTRIBUTING.md) and submit a pull request! 🚀

---

## Helpful Open Source Packages

- <a href="https://github.com/omaralalwi/Gpdf"><img src="https://raw.githubusercontent.com/omaralalwi/Gpdf/master/public/images/gpdf-banner-bg.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> Gpdf </a> Open Source HTML to PDF converter for PHP & Laravel Applications, supports Arabic content out-of-the-box and other languages..

- <a href="https://github.com/omaralalwi/laravel-taxify"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-taxify/master/public/images/taxify.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> **laravel Taxify** </a> Laravel Taxify provides a set of helper functions and classes to simplify tax (VAT) calculations within Laravel applications.

- <a href="https://github.com/omaralalwi/laravel-deployer"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-deployer/master/public/images/deployer.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Deployer" /> **laravel Deployer** </a> Streamlined Deployment for Laravel and Node.js apps, with Zero-Downtime and various environments and branches.

- <a href="https://github.com/omaralalwi/laravel-trash-cleaner"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/laravel-trash-cleaner.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Trash Cleaner" /> **laravel Trash Cleaner** </a>clean logs and debug files for debugging packages.

- <a href="https://github.com/omaralalwi/laravel-time-craft"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-time-craft/master/public/images/laravel-time-craft.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Trash Cleaner" /> **laravel Time Craft** </a>simple trait and helper functions that allow you, Effortlessly manage date and time queries in Laravel apps.

- <a href="https://github.com/omaralalwi/laravel-startkit"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-startkit/master/public/screenshots/backend-rtl.png" width="26" height="26" style="border-radius:13px;" alt="Laravel Startkit" /> **Laravel Startkit** </a>  Laravel Admin Dashboard, Admin Template with Frontend Template, for scalable Laravel projects.
