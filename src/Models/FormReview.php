<?php

namespace CoderManjeet\LaravelFormReviews\Models;

use CoderManjeet\LaravelFormReviews\Enums\Status;
use CoderManjeet\LaravelFormReviews\Traits\HasDynamicEnumMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormReview extends Model
{
    use HasFactory, SoftDeletes, HasDynamicEnumMethods;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'field_key',
        'field_old_value',
        'field_new_value',
        'reviewer_id',
        'form_submitter_id',
        'reviewed_at',
        'metadata',
        'status',
        'reviewer_notes'
    ];

    protected $casts = [
        'metadata' => 'array',
        'status' => Status::class,
        'reviewed_at' => 'datetime',
    ];

    protected $dates = [
        'reviewed_at',
        'deleted_at'
    ];

    /**
     * Get the reviewer that owns the review
     */
    public function reviewer()
    {
        $reviewerModel = config('form-reviews.reviewer_model') ?? 'App\Models\User';
        return $this->belongsTo($reviewerModel, 'reviewer_id');
    }
    
    /**
     * Get the form submitter
     */
    public function formSubmitter()
    {
        $submitterModel = config('form-reviews.form_submitter_model') ?? 'App\Models\User';
        return $this->belongsTo($submitterModel, 'form_submitter_id');
    }

    /**
     * Scope to get reviews by status
     */
    public function scopeByStatus($query, Status $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get the reviewable model that this review belongs to
     */
    public function reviewable()
    {
        return $this->morphTo();
    }

    /**
     * Get the status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }
}