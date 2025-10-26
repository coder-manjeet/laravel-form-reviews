<?php

use CoderManjeet\LaravelFormReviews\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_reviews', function (Blueprint $table) {
            $table->id();            

            $table->morphs('reviewable');
            
            $table->string('field_key')->nullable();
            $table->string('field_old_value')->nullable();
            $table->string('field_new_value')->nullable();
            
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->index('reviewer_id');
            $table->unsignedBigInteger('form_submitter_id')->nullable();
            $table->index('form_submitter_id');

            $table->timestamp('reviewed_at')->nullable();
            $table->json('metadata')->nullable();

            $table->string('status');
            $table->text('reviewer_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_reviews');
    }
};