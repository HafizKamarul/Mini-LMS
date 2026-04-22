<?php

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
        Schema::create('question_bank_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->enum('type', ['single_choice', 'multiple_choice', 'true_false', 'short_answer'])->default('single_choice');
            $table->decimal('marks', 8, 2)->default(1);
            $table->text('explanation')->nullable();
            $table->json('keywords')->nullable();
            $table->string('fingerprint', 64)->unique();
            $table->timestamps();
        });

        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_question_id')->constrained('question_bank_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('question_bank_question_id')
                ->nullable()
                ->after('exam_id')
                ->constrained('question_bank_questions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('question_bank_question_id');
        });

        Schema::dropIfExists('question_bank_options');
        Schema::dropIfExists('question_bank_questions');
    }
};