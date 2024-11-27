<?php

return [

    /*
     * Path to the model where you store languages data (English, Arabic, ...)
     */
    'language_model' => 'App\Models\Language',

    /*
     * Foreign key referencing the 'languages' table.
     * This field indicates the language in which the translation is written.
     */
    'foreign_key' => 'language_id',

    /*
     * Foreign key referencing another table.
     * This field links the translation to a specific record in another table,
     * allowing for multiple translations for that record.
     */
    'owner_key' => 'model_id',
    /*
     * Key name for translations data in the request array.
     * This specifies where to look for the array of translations in the request payload.
     */
    'translations_data_key' => 'translations',
];
