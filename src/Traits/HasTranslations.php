<?php

namespace WeCanSync\LaravelTranslations\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::created(function ($model) {
            $languages = self::getLanguageModel();
            foreach ($languages->all() as $language) {
                $translations = self::getTranslatableFields($model, $language);
                if ($translations) {
                    self::createRecord($model, $language, $translations);
                }
            }
        });

        static::saving(function ($model) {
            if (isset($model->id)) {
                $languages = self::getLanguageModel();
                foreach ($languages->all() as $language) {
                    $translations = self::getTranslatableFields($model, $language);
                    if ($translations) {
                        self::updateRecord($model, $language, $translations);
                    }
                }
            }
        });

        static::deleted(function ($model) {
            $translations = new $model->translation_model['model'];
            $translations->where('model_id', $model->id)->delete();
        });

    }

    private static function getLanguageModel()
    {
        $defaultLanguageModel = config('laravel-translations.language_model');
        return new $defaultLanguageModel;
    }

    private static function getTranslatableFields($model, $language): array
    {
        $translations = [];
        foreach ($model->translation_model['translatable'] as $field) {
            $translations[$field] = request()->get($field.'_'.$language->code);
        }

        return $translations;
    }

    private static function createRecord($model, $language, $translations): void
    {
        $foreign_key = self::getModelForeignKey($model);
        $owner_key = self::getModelOwnerKey($model);
        $translation_model = new $model->translation_model['model'];
        $translation_model->create(array_merge($translations, [
            $foreign_key => $language->id,
            $owner_key => $model->id,
        ]));
    }

    private static function getModelForeignKey($model): string
    {
        return isset($model->translation_model['foreign_key']) ? $model->translation_model['foreign_key'] : config('laravel-translations.foreign_key');
    }

    private static function getModelOwnerKey($model): string
    {
        return isset($model->translation_model['owner_key']) ? $model->translation_model['owner_key'] : config('laravel-translations.owner_key');

    }

    private static function updateRecord($model, $language, $translations): void
    {
        $foreign_key = self::getModelForeignKey($model);
        $owner_key = self::getModelOwnerKey($model);
        $translation_model = new $model->translation_model['model'];
        $exists = $translation_model->where($foreign_key, $language->id)
            ->where($owner_key, $model->id)
            ->first();
        if ($exists) {
            $exists->update(array_merge($translations));
        } else {
            $translation_model::create(array_merge($translations, [
                $foreign_key => $language->id,
                $owner_key => $model->id,
            ]));
        }
    }

    public function getTranslations($language_id, $field): mixed
    {
        $foreign_key = self::getModelForeignKey($this);

        return $this->translationRelation()->where($foreign_key, $language_id)->select($field)->first()?->$field;
    }

    public function translationRelation(): HasMany
    {
        return $this->hasMany(self::getTranslationsModel($this), self::getModelOwnerKey($this), 'id');
    }

    private static function getTranslationsModel($model)
    {
        return isset($model->translation_model['model'])
            ? new $model->translation_model['model']
            : null;
    }
}
