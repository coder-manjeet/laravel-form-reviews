<?php

namespace CoderManjeet\LaravelFormReviews\Facades;

use Illuminate\Support\Facades\Facade;

class FormReview extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'form-reviews';
    }
}