# Laravel Translation Management
[![Current version](https://img.shields.io/packagist/v/wecansync/laravel-translations.svg?logo=composer)](https://packagist.org/packages/wecansync/laravel-translations)
[![Monthly Downloads](https://img.shields.io/packagist/dm/wecansync/laravel-translations.svg)](https://packagist.org/packages/wecansync/laravel-translations/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/wecansync/laravel-translations.svg)](https://packagist.org/packages/wecansync/laravel-translations/stats)
[![codecov](https://codecov.io/gh/wecansync/laravel-translations/branch/main/graph/badge.svg)](https://codecov.io/gh/wecansync/laravel-translations)


## Features
- Store translations in a dedicated database table.
- Easily store, retrieve and update translations.
- Support for multiple languages.


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

1. in <b>Category</b> model add <b>HasTranslations</b> and <b>$translation_model</b> property

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
        //  'foreign_key' => 'language_id', 
        //  'owner_key' => 'model_name_id',
       
        //  by default the foreign key name is "language_id"
        //  and the owner_key name is model_id
       
        //  if you would like to replace it for all models
        //  you can change it in config/laravel-translations
       
    
        ]; 
    ```
## Blade Form

The Blade form for adding category translations should include the following fields:

- `name_en`: English translation of the category name (string).
- `name_ar`: Arabic translation of the category name (string).

Example Blade form:

```blade
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <label for="name_en">English Name:</label>
    <input type="text" name="name_en" required>

    <label for="name_ar">Arabic Name:</label>
    <input type="text" name="name_ar" required>

    <button type="submit">Submit</button>
</form>
```

## Security
If you discover any security-related issues, please email info@wecansync.com.

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request.

## License
The package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
