<?php

use CoderManjeet\LaravelFormReviews\Enums\Status;

return [

    /*
    |--------------------------------------------------------------------------
    | Form Reviews Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for the form reviews package.
    | This includes options such as setting default review statuses and
    | other related configurations.
    |
    */

    'default_status' => Status::PENDING->value,

    'statuses' => Status::values(),

    'reviewer_model' => config('auth.providers.users.model', 'App\Models\User'),

    'form_submitter_model' => config('auth.providers.users.model', 'App\Models\User'),

    'notification_email' => env('FORM_REVIEWS_NOTIFICATION_EMAIL', 'admin@example.com'),
];