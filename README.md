# Lexi Translate

<p align="center">
  <a href="https://omaralalwi.github.io/lexi-translate" target="_blank">
    <img src="https://raw.githubusercontent.com/omaralalwi/lexi-translate/master/public/images/lexi-translate-banner.jpg" alt="lexi translate banner">
  </a>
</p>

simplify managing translations for multilingual Eloquent models with power of **morph relationships** and **caching** .

Its lightweight design and flexibility make it an excellent choice for applications needing multi-language support with minimal performance overhead.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Defining LexiTranslatable Models](#defining-lexitranslatable-models)
  - [update or Create Translations](#update-or-Create-translations)
  - [Retrieving Translations](#retrieving-translations)
  - [Cache Handling](#cache-handling)
  - [More Examples](#more-examples)
  - [Helper Functions](#helper-functions)
- [Testing](#testing)
- [Alternative Solutions](#alternative-solutions)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Features](#features)
- [Security](#security)
- [License](#license)
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
you can use `lexi_locales` to get supported locals as array, depend on `supported_locales` in config file.

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

Please note that the `supported_locales` setting in the configuration file defines the locales that will be handled by the cache by default. 
If you add additional locales for translations, make sure to include them in the `supported_locales` list to ensure proper cache handling. Failing to do so may result in cache issues for locales not added to the list.

---

## Features

- **Dynamic Morph Relationships:** Manage translations across different models with ease, thanks to its dynamic morph able relationships.
- **Automatic Caching:** Enjoy enhanced performance as translations are automatically cached and invalidated, ensuring quick access and updates.
- **Fallback Mechanism:** Never worry about missing translations—Lexi Translate falls back to the default language if a translation is not available.
- **Simple, Intuitive API:** A clean and consistent API for adding, retrieving, and managing translations.
- **Eloquent-Friendly:** Seamlessly integrates with Laravel's Eloquent ORM, making it easy to work with translated data while maintaining the power of Laravel’s query builder.
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

 
## Helpful Open Source Packages
  
- <a href="https://github.com/omaralalwi/Gpdf"><img src="https://raw.githubusercontent.com/omaralalwi/Gpdf/master/public/images/gpdf-banner-bg.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> Gpdf </a> Open Source HTML to PDF converter for PHP & Laravel Applications, supports Arabic content out-of-the-box and other languages..

- <a href="https://github.com/omaralalwi/laravel-taxify"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-taxify/master/public/images/taxify.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Taxify" /> **laravel Taxify** </a> Laravel Taxify provides a set of helper functions and classes to simplify tax (VAT) calculations within Laravel applications.

- <a href="https://github.com/omaralalwi/laravel-deployer"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-deployer/master/public/images/deployer.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Deployer" /> **laravel Deployer** </a> Streamlined Deployment for Laravel and Node.js apps, with Zero-Downtime and various environments and branches.

- <a href="https://github.com/omaralalwi/laravel-trash-cleaner"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-trash-cleaner/master/public/images/laravel-trash-cleaner.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Trash Cleaner" /> **laravel Trash Cleaner** </a>clean logs and debug files for debugging packages.

- <a href="https://github.com/omaralalwi/laravel-time-craft"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-time-craft/master/public/images/laravel-time-craft.jpg" width="26" height="26" style="border-radius:13px;" alt="laravel Trash Cleaner" /> **laravel Time Craft** </a>simple trait and helper functions that allow you, Effortlessly manage date and time queries in Laravel apps.

- <a href="https://github.com/omaralalwi/laravel-startkit"><img src="https://raw.githubusercontent.com/omaralalwi/laravel-startkit/master/public/screenshots/backend-rtl.png" width="26" height="26" style="border-radius:13px;" alt="Laravel Startkit" /> **Laravel Startkit** </a>  Laravel Admin Dashboard, Admin Template with Frontend Template, for scalable Laravel projects.
