<?php

use Omaralalwi\LexiTranslate\Enums\Language;

return [

    /*
    |--------------------------------------------------------------------------
    | Translations Table Name
    |--------------------------------------------------------------------------
    |
    | Define the database table name for storing translations. You can change
    | this to match your application’s preferences to avoid table conflicts
    | with other packages or project-specific tables.
    |
    */
    'table_name' => 'lexi_translations',

    /*
    |--------------------------------------------------------------------------
    | Enable Translation Caching
    |--------------------------------------------------------------------------
    |
    | Set this to true to enable caching of translations. Caching helps improve
    | performance, especially for large-scale applications by reducing database
    | queries. Disable caching during development or if your data is highly
    | dynamic to avoid outdated translations.
    |
    */
    'use_cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | Define a prefix for translation cache keys. This helps to avoid key
    | collisions when using a shared cache storage across multiple systems.
    |
    */
    'cache_prefix' => 'lexi_trans',

    /*
    |--------------------------------------------------------------------------
    | Cache Time-to-Live (TTL) in Hours
    |--------------------------------------------------------------------------
    |
    | If caching is enabled, you can define the cache duration (TTL) in hours.
    | The translations will remain cached for this period, after which the cache
    | will be refreshed. If this value is not set, it will use the default cache
    | TTL from the application's caching configuration.
    |
    | Example: 6 hours is the default cache duration.
    |
    */
    'cache_ttl' => 6, // Cache translations for 6 hours

    /*
    |--------------------------------------------------------------------------
    | API Locale Header Key
    |--------------------------------------------------------------------------
    |
    | Define the header key used to specify the desired locale in API requests.
    | This value is checked in the middleware to set the application's locale
    | dynamically based on the incoming request.
    |
    | Example: 'Accept-Language'
    |
    */
    'api_locale_header_key' => 'Accept-Language',

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Specify the list of supported locales in your application. These locales
    | are used when creating translations from requests. Add as many locales as
    | your application needs, but avoid including unused locales to improve
    | performance and manageability.
    |
    | Example: ['en', 'ar', 'fr', 'es']
    |
    */
    'supported_locales' => [
        Language::EN->value,
        Language::AR->value,
        Language::ZH->value,
        Language::ES->value,
        Language::FR->value,
        Language::HI->value,
        Language::RU->value,
        Language::PT->value,
        Language::DE->value,
        Language::JA->value,
    ],

];
