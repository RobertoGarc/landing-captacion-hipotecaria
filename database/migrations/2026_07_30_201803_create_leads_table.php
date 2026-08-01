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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('new')->index();
            $table->string('purpose');
            $table->unsignedBigInteger('property_price')->nullable();
            $table->string('province');
            $table->unsignedBigInteger('financing_amount')->nullable();
            $table->unsignedBigInteger('savings_amount')->nullable();
            $table->unsignedTinyInteger('holders_count')->default(1);
            $table->string('employment_status');
            $table->unsignedBigInteger('monthly_income');
            $table->unsignedBigInteger('monthly_debts')->default(0);
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone');
            $table->boolean('privacy_accepted')->default(false);
            $table->boolean('marketing_accepted')->default(false);
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('gclid')->nullable();
            $table->string('fbclid')->nullable();
            $table->string('referrer')->nullable();
            $table->string('landing_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('clientify_id')->nullable()->index();
            $table->timestamp('clientify_synced_at')->nullable();
            $table->text('clientify_error')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
