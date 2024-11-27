# Laravel Translation Management
[![Current version](https://img.shields.io/packagist/v/wecansync/laravel-translations.svg?logo=composer)](https://packagist.org/packages/wecansync/laravel-translations)
[![Monthly Downloads](https://img.shields.io/packagist/dm/wecansync/laravel-translations.svg)](https://packagist.org/packages/wecansync/laravel-translations/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/wecansync/laravel-translations.svg)](https://packagist.org/packages/wecansync/laravel-translations/stats)
[![codecov](https://codecov.io/gh/wecansync/laravel-translations/branch/main/graph/badge.svg)](https://codecov.io/gh/wecansync/laravel-translations)

## Overview

Laravel Translation Management is a package designed to simplify the management of translations in Laravel applications. It allows you to store translations in a dedicated database table, making it easy to manage multiple languages and update translations as needed.


## Features
- **Database Storage**: Store translations in a dedicated database table for easy management.
- **Multi-Language Support**: Easily handle translations for multiple languages.
- **Configurable Keys**: Customize foreign and owner keys as per your application's requirements.
- **Simple Integration**: Easy to integrate with your existing Laravel models.


## Installation
You can install the package via Composer:

```bash
composer require wecansync/laravel-translations
```

Publish config file
```bash
php artisan vendor:publish --provider="WeCanSync\LaravelTranslations\PackageServiceProvider"
```


## Usage

# Step 1: Update Your Model 
In your `Category` model (or any model you wish to manage translations for), add the `HasTranslations` trait and define the `$translation_model` property

 ```php
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Model;
    use WeCanSync\LaravelTranslations\Traits\HasTranslations;
    
    class ModelName extends Model
    {
       use HasTranslations;
   
       protected $translation_model = [
            'model' => TranslationsModelName::class,
        // Optional: Uncomment to customize foreign and owner keys.
        //  'foreign_key' => 'language_id', 
        //  'owner_key' => 'model_name_id',
        
        // Optional: Uncomment to customize key name for translations data in the request array.
        //  'translations_data_key' => 'translations_array'
        
       // Optional: Uncomment to skip storing/updating translations for specific routes.
       //   'skip_routes' => ['route.update', 'route.store', ...]
       // Use this to update the main model data without affecting translations.
       // Note: Deleting the model will still remove its associated translations.
       // Use wildcards like 'route.*' to match multiple routes.
       
        // Default values:
        // foreign_key: "language_id"
        // owner_key: "model_id"
        // translations_data_key: "translations"
       
        //  if you would like to replace it for all models
        //  you can change it in config/laravel-translations
        ]; 
    }
 ```

# Step 2: Create Blade Form
Create a Blade form for adding category translations. The form should include fields for each language you want to support.

Here's an example for English and Arabic:

English language: Use language ID (example: 1).

`translations[1][name]`: English translation of the category name (string)

Arabic language: Use language ID (example: 2).

`translations[2][name]`: Arabic translation of the category name (string)

This ensures the translation fields are correctly linked to their respective language IDs.



### Example Blade form:

```blade
<form action="{{ route('categories.store') }}" method="POST">
   @csrf
   <label for="code">Code:</label>
   <input type="text" name="code" required>
   @foreach($languages as $language)
        <div class="form-group">
            <label>Name({{$language->code}})</label>
            <input type="text" class="form-control" name="translations[{{$language->id}}][name]" >
        </div>
    @endforeach
   
   <button type="submit">Submit</button>
</form>
```

# Step 3: Retrieve Translation Data
You can easily retrieve translation data using the `getTranslations()` function. This function allows you to fetch translations for a specific field in a specified language.

### Parameters

1. **`languageId`** (*required*):  
   The ID of the language for which you want to retrieve the translation.

2. **`fieldName`** (*required*):  
   The name of the field you want to retrieve the translation for.

3. **`key`** (*optional*):
    - This parameter allows you to fetch translation data using a specific key instead of the default language ID.
    - Useful when you need to query translations based on a custom identifier.

### Example Usage

```php
$translation = $model->getTranslations(1, 'name'); 
// Retrieves the 'name' translation for language ID 1 (e.g., English).

$translationByCode = $model->getTranslations('en', 'name', 'code'); 
// Retrieves the 'name' translation using the key 'code' instead of an ID.
```

### Handling Missing Translations
If a translation does not exist for the specified language or field, the function will return null. You can handle this accordingly in your application:

```php
$translation = $category->getTranslations(3, 'name'); // Assuming 3 is the language ID for a non-existent language

if ($translation) {
    echo "Translation: " . $translation;
} else {
    echo "Translation not available.";
}
```
This way, you can manage translations effectively and ensure that your application behaves gracefully when translations are missing.


# Step 3: Delete Translation Data
You can easily delete translation data using the `clearTranslations()` function. This function allows you to delete translations data for a specific model in a specified language.

### Parameters

1. **`languageId`** (*required*):  
   The ID of the language for which you want to delete the translation.

3. **`key`** (*optional*):
   This parameter allows you to delete translation data using a specific key instead of the default language ID.

### Example Usage

```php
$category->clearTranslations(1); 
// Delete the translations for language ID 1 (e.g., English).

$category->clearTranslations('en', 'code'); 
// Delete the translation using the key 'code' instead of an ID.
```


# Optional: Manage Translation Directly
You can manage translation data directly when storing multiple records of the main model at the same time using the `withTranslations()` function. This function allows you to store or update translation data for a specific model in a specified language.

## Configuration
To use `withTranslations()`, ensure your model is configured with the following:

```php
protected $translation_model = [
          'skip_routes' => ['categories.update', 'categories.store', ...]
     ]; 
]
 ```
### Parameters

1. **`translations`** (*required*):  
   The translations data array to store or update

### Example Usage

#### Storing Translations

```php
$translations_data = [
    1 => [
        'name' => 'English Name',
    ],
    2 => [
        'name' => 'Arabic Name',
    ]
];

Category::query()->create($request->validated())->withTranslations($translations_data);
// Store the translations for language ID 1 (English) and language ID 2 (Arabic).
```
#### Updating Translations

```php
$category = Category::query()->find($category_id)->update($request->validated());

$translations_data = [
    1 => [
        'name' => 'Updated English Name',
    ],
    2 => [
        'name' => 'Updated Arabic Name',
    ]
];

$category->withTranslations($translations_data);
// Update the translations for language ID 1 (English) and language ID 2 (Arabic).
```

## Security
If you discover any security-related issues, please report them by emailing info@wecansync.com.

## Contributing
Contributions are welcome! To contribute, please fork the repository and submit a pull request. Make sure to follow the coding standards and include tests for any new features.

## License
The package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
