<?php

return [

    /*
     * Path to the model where you store languages data (English, Arabic, ...)
     */
    'language_model' => 'App\Models\Language',

    /*
     * Name of the relationship key between the language model and where you gonna store the translated data
     */
    'foreign_key' => 'language_id',

    /*
     * Name of the relationship key between the main model and where you gonna store the translated data
     */
    'owner_key' => 'model_id',
];
