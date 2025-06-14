<?php

namespace Omaralalwi\LexiTranslate\Traits;

use Omaralalwi\LexiTranslate\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Omaralalwi\LexiTranslate\Models\Scopes\TranslationsScopes;
use Omaralalwi\LexiTranslate\Traits\HasLocale;

trait LexiTranslatable
{
    use HasCache, TranslationsScopes, HasLocale;

    /**
     * Get the list of translatable fields for the model.
     *
     * @return array
     */
    public function getTranslatableFields(): array
    {
        return $this->translatableFields ?? [];
    }

    /**
     * Retrieve fresh translations without caching.
     *
     * @return MorphMany
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Translate a specific column for the given locale.
     *
     * @param string $column The column to translate.
     * @param string|null $locale The locale to translate to. Defaults to app locale.
     * @return string The translated value or the original column value if no translation is found.
     *
     *  Note:
     *  some cases we can add translations for attributes not in table
     *  for ex:
     *  we can ass service, then add translations for it's name and description
     *  even if service model does not has name and description attributes .
     *
     */
    public function translate(string $column, ?string $locale = null): ?string
    {
        $locale = $this->getValidatedLocale($locale);
        $originalText = '';

        $modelType = class_basename($this);
        $cachePrefix = self::getCachePrefix();
        $cacheKey = "{$cachePrefix}_{$modelType}_{$this->id}_{$column}_{$locale}";

        $translation = self::isCacheEnabled()
            ? Cache::remember($cacheKey, now()->addHours(self::cacheTtl()), fn() => $this->getTranslation($column, $locale))
            : $this->getTranslation($column, $locale);

        if (array_key_exists($column, $this->attributes)) {
            $originalText = $this->attributes[$column];
        }

        return $translation?->text ?? $originalText;
    }

    /**
     * Get a specific translation for a column and locale.
     *
     * @param string $column The column name.
     * @param string $locale The locale.
     */
    public function getTranslation($column, $locale)
    {
        return $this->morphOne(Translation::class, 'translatable')
            ->where('column', $column)
            ->where('locale', $locale)
            ->first();
    }

    /**
     * Get the translated value of an attribute.
     *
     * @param string $attribute The attribute name.
     * @param string|null $locale The locale for the translation. Defaults to app locale.
     * @return string|null The translated value or null.
     */
    public function transAttr(string $attribute, ?string $locale = null): ?string
    {
        return $this->translate($attribute, $locale);
    }

    /**
     * Clear all translation caches for the model.
     *
     * @return void
     */
    public function clearTranslationsCache(): void
    {
        if (self::isCacheEnabled()) {
            self::clearBaseTranslationsCache($this);

            foreach ($this->getTranslatableFields() as $column) {
                self::clearTranslationCache($this, $column);
            }
        }
    }

    /**
     * Create or update translations for multiple columns and locales.
     *
     * @param array $translations The translations data.
     * @return void
     */
    public function setTranslations(array $translations): void
    {
        foreach ($translations as $locale => $columns) {
            foreach ($columns as $column => $text) {
                $this->setTranslation($column, $locale, $text);
            }
        }

        $this->clearTranslationsCache();
    }

    /**
     * Create or update a translation for a single column and locale.
     *
     * @param string $column The name of the column to translate.
     * @param string $locale The locale for the translation.
     * @param string $text The translation text.
     * @return void
     */
    public function setTranslation(string $column, string $locale, string $text): void
    {
        $this->translations()->updateOrCreate(
            ['column' => $column, 'locale' => $locale],
            ['text' => $text]
        );

        if (self::isCacheEnabled()) {
            self::clearTranslationCache($this, $column);
        }
    }

    /**
     * Determine if the given locale is supported.
     *
     * @param string $locale The locale to check.
     * @return bool True if the locale is supported, false otherwise.
     */
    public function isSupportedLocal(string $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales());
    }

    /**
     * Determine if the given attribute is translatable.
     *
     * @param string $field The attribute name.
     * @return bool True if the attribute is translatable, false otherwise.
     */
    public function isTranslatableAttribute(string $field): bool
    {
        return in_array($field, $this->getTranslatableFields());
    }
}
