<?php

declare(strict_types=1);

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
        Schema::create('mpesa_transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_type', 50)->index();
            $table->string('transaction_id', 50)->nullable()->index();
            $table->string('conversation_id', 100)->nullable()->index();
            $table->string('originator_conversation_id', 100)->nullable();
            $table->string('checkout_request_id', 100)->nullable()->index();
            $table->string('merchant_request_id', 100)->nullable();
            $table->string('phone', 15)->nullable()->index();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('account_reference', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('shortcode', 20)->nullable();
            $table->integer('result_code')->nullable();
            $table->string('result_description', 500)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('callback_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
