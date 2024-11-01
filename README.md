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
            'translatable' => ['name'],
        // Optional: Uncomment to customize foreign and owner keys
        //  'foreign_key' => 'language_id', 
        //  'owner_key' => 'model_name_id',
       
        // Default values:
        // foreign_key: "language_id"
        // owner_key: "model_id"
       
        //  if you would like to replace it for all models
        //  you can change it in config/laravel-translations
       
    
        ]; 
    }
 ```
# Step 2: Create Blade Form

Create a Blade form for adding category translations. The form should include fields for each language you want to support. Here's an example for English and Arabic:

- `name_en`: English translation of the category name (string).
- `name_ar`: Arabic translation of the category name (string).

Example Blade form:

```blade
<form action="{{ route('categories.store') }}" method="POST">
   @csrf
   <label for="code">Code:</label>
   <input type="text" name="code" required>
   
   <label for="name_en">English Name:</label>
   <input type="text" name="name_en" required>
   
   <label for="name_ar">Arabic Name:</label>
   <input type="text" name="name_ar" required>
   
   <button type="submit">Submit</button>
</form>
```

# Step 3: Retrieve Translation Data
You can easily retrieve translation data using the `getTranslations()` function. This function allows you to fetch translations for a specific field in a specified language.

### Parameters
The `getTranslations()` function takes two parameters:
1. **Language ID**: The ID of the language for which you want to retrieve the translation.
2. **Field Name**: The name of the field you want to retrieve the translation for.


### Example Usage
Here’s how you can use the `getTranslations()` function to retrieve the English and Arabic translations for a category name:

```php
// Retrieve English translation (assuming language ID for English is 1)
$englishName = $category->getTranslations(1, 'name');

// Retrieve Arabic translation (assuming language ID for Arabic is 2)
$arabicName = $category->getTranslations(2, 'name');

// Output the translations
echo "English Name: " . $englishName; // Outputs: English Name: [English translation]
echo "Arabic Name: " . $arabicName; // Outputs: Arabic Name: [Arabic translation]
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

## Security
If you discover any security-related issues, please report them by emailing info@wecansync.com.

## Contributing
Contributions are welcome! To contribute, please fork the repository and submit a pull request. Make sure to follow the coding standards and include tests for any new features.

## License
The package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
