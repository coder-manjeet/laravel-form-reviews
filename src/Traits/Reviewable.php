<?php

namespace CoderManjeet\LaravelFormReviews\Traits;

use CoderManjeet\LaravelFormReviews\Models\FormReview;
use CoderManjeet\LaravelFormReviews\Enums\Status;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait Reviewable
{
    /**
     * Get all reviews for this model
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(FormReview::class, 'reviewable');
    }

    /**
     * Get reviews by specific status
     */
    public function reviewsByStatus(Status $status): Collection
    {
        return $this->reviews()->where('status', $status)->get();
    }

    /**
     * Get pending reviews
     */
    public function pendingReviews(): Collection
    {
        return $this->reviewsByStatus(Status::PENDING);
    }

    /**
     * Get submitted reviews
     */
    public function submittedReviews(): Collection
    {
        return $this->reviewsByStatus(Status::SUBMITTED);
    }

    /**
     * Get approved reviews
     */
    public function approvedReviews(): Collection
    {
        return $this->reviewsByStatus(Status::APPROVED);
    }

    /**
     * Get rejected reviews
     */
    public function rejectedReviews(): Collection
    {
        return $this->reviewsByStatus(Status::REJECTED);
    }

    /**
     * Check if the model has any reviews
     */
    public function hasReviews(): bool
    {
        return $this->reviews()->exists();
    }

    /**
     * Check if the model has pending reviews
     */
    public function hasPendingReviews(): bool
    {
        return $this->reviews()->where('status', Status::PENDING)->exists();
    }

    /**
     * Check if the model has submitted reviews
     */
    public function hasSubmittedReviews(): bool
    {
        return $this->reviews()->where('status', Status::SUBMITTED)->exists();
    }

    /**
     * Check if all reviews for this model are approved
     */
    public function allReviewsApproved(): bool
    {
        $totalReviews = $this->reviews()->count();
        
        if ($totalReviews === 0) {
            return false;
        }

        $approvedReviews = $this->reviews()->where('status', Status::APPROVED)->count();
        
        return $totalReviews === $approvedReviews;
    }

    /**
     * Check if any reviews for this model are rejected
     */
    public function hasRejectedReviews(): bool
    {
        return $this->reviews()->where('status', Status::REJECTED)->exists();
    }

    /**
     * Get the latest review for this model
     */
    public function latestReview(): ?FormReview
    {
        return $this->reviews()->latest()->first();
    }

    /**
     * Get reviews for a specific field
     */
    public function reviewsForField(string $fieldKey): Collection
    {
        return $this->reviews()->where('field_key', $fieldKey)->get();
    }

    /**
     * Get pending reviews for a specific field
     */
    public function pendingReviewsForField(string $fieldKey): Collection
    {
        return $this->reviews()
            ->where('field_key', $fieldKey)
            ->where('status', Status::PENDING)
            ->get();
    }

    /**
     * Create a new review for this model
     */
    public function createReview(array $reviewData): FormReview
    {
        return $this->reviews()->create($reviewData);
    }

    /**
     * Create a field review for this model
     */
    public function createFieldReview(
        string $fieldKey,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?int $reviewerId = null,
        ?int $formSubmitterId = null,
        array $metadata = [],
        Status $status = Status::PENDING,
        ?string $reviewerNotes = null
    ): FormReview {
        return $this->createReview([
            'field_key' => $fieldKey,
            'field_old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
            'field_new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
            'reviewer_id' => $reviewerId,
            'form_submitter_id' => $formSubmitterId,
            'metadata' => $metadata,
            'status' => $status,
            'reviewer_notes' => $reviewerNotes,
        ]);
    }

    /**
     * Get the review statistics for this model
     */
    public function getReviewStats(): array
    {
        $reviews = $this->reviews()->get();

        $reviewsStats = [];
        foreach (Status::cases() as $status) {
            $reviewsStats[$status->value] = $reviews->where('status', $status)->count();
        }   

        $reviewsStats['total'] = $reviews->count();
        return $reviewsStats;
    }

    /**
     * Scope to filter models that have reviews
     */
    public function scopeHasReviews(Builder $query): Builder
    {
        return $query->whereHas('reviews');
    }

    /**
     * Scope to filter models that have pending reviews
     */
    public function scopeHasPendingReviews(Builder $query): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) {
            $reviewQuery->where('status', Status::PENDING);
        });
    }

    /**
     * Scope to filter models that have submitted reviews
     */
    public function scopeHasSubmittedReviews(Builder $query): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) {
            $reviewQuery->where('status', Status::SUBMITTED);
        });
    }

    /**
     * Scope to filter models that have approved reviews
     */
    public function scopeHasApprovedReviews(Builder $query): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) {
            $reviewQuery->where('status', Status::APPROVED);
        });
    }

    /**
     * Scope to filter models that have rejected reviews
     */
    public function scopeHasRejectedReviews(Builder $query): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) {
            $reviewQuery->where('status', Status::REJECTED);
        });
    }

    /**
     * Scope to filter models where all reviews are approved
     */
    public function scopeAllReviewsApproved(Builder $query): Builder
    {
        return $query->whereHas('reviews')
            ->whereDoesntHave('reviews', function ($reviewQuery) {
                $reviewQuery->whereNotIn('status', [Status::APPROVED]);
            });
    }

    /**
     * Scope to filter models that have reviews by a specific reviewer
     */
    public function scopeReviewedBy(Builder $query, int $reviewerId): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) use ($reviewerId) {
            $reviewQuery->where('reviewer_id', $reviewerId);
        });
    }

    /**
     * Scope to filter models that have reviews for a specific field
     */
    public function scopeHasReviewsForField(Builder $query, string $fieldKey): Builder
    {
        return $query->whereHas('reviews', function ($reviewQuery) use ($fieldKey) {
            $reviewQuery->where('field_key', $fieldKey);
        });
    }
}