<?php

namespace WeCanSync\LaravelTranslations\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::saved(function ($model) {
            self::manageTranslations($model);
        });

        static::deleted(function ($model) {
            $translations = new $model->translation_model['model'];
            $translations->where('model_id', $model->id)->delete();
        });
    }

    private static function manageTranslations($model): void
    {
        if (isset($model->translation_model['skip_routes']) && !is_null($model->translation_model['skip_routes'])) {
            if (request()->routeIs($model->translation_model['skip_routes'])) {
                return;
            }
        }
        $translations = request()->get(self::getTranslationDataKey($model));
        if($translations) {
            foreach ($translations as $language => $field) {
                if (self::getLanguageModel()->exists($language)) {
                    self::manageRecord($model, $language, $field);
                }
            }
        }
    }

    private static function getTranslationDataKey($model): string
    {
        return isset($model->translation_model['translations_data_key']) ? $model->translation_model['translations_data_key'] : config('laravel-translations.translations_data_key');
    }

    private static function getLanguageModel()
    {
        $defaultLanguageModel = config('laravel-translations.language_model');

        return new $defaultLanguageModel;
    }

    private static function manageRecord($model, $language_id, $translations): void
    {
        $foreign_key = self::getModelForeignKey($model);
        $owner_key = self::getModelOwnerKey($model);
        $translation_model = new $model->translation_model['model'];
        $translation_model->updateOrCreate(
            [
                $foreign_key => $language_id,
                $owner_key => $model->id,
            ],
            $translations
        );
    }

    private static function getModelForeignKey($model): string
    {
        return isset($model->translation_model['foreign_key']) ? $model->translation_model['foreign_key'] : config('laravel-translations.foreign_key');
    }

    private static function getModelOwnerKey($model): string
    {
        return isset($model->translation_model['owner_key']) ? $model->translation_model['owner_key'] : config('laravel-translations.owner_key');

    }

    public function withTranslations($translations): void
    {
        foreach ($translations as $language_id => $translation) {
            self::manageRecord($this->getModel(), $language_id, $translation);
        }
    }

    public function clearTranslations($language_id, ?string $key = null): bool
    {
        return $this->translationRelation()->where($key ?? self::getModelForeignKey($this), $language_id)->delete();
    }

    public function translationRelation(): HasMany
    {
        return $this->hasMany(self::getTranslationsModel($this), self::getModelOwnerKey($this), 'id');
    }

    private static function getTranslationsModel($model): mixed
    {
        return isset($model->translation_model['model'])
            ? new $model->translation_model['model']
            : null;
    }

    public function getTranslations($language_id, $field, ?string $key = null): mixed
    {
        return $this->translationRelation()->where($key ?? self::getModelForeignKey($this), $language_id)->select($field)->first()?->$field;
    }
}
