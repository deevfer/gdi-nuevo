<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Correo saliente
            $table->boolean('mail_enabled')->default(false);
            $table->string('mail_mailer')->default('smtp');
            $table->string('mail_host')->nullable();
            $table->unsignedInteger('mail_port')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->text('mail_to_address')->nullable();

            // reCAPTCHA v3
            $table->boolean('recaptcha_enabled')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->text('recaptcha_secret_key')->nullable();
            $table->decimal('recaptcha_min_score', 3, 2)->default(0.5);

            // Medición y publicidad
            $table->string('google_analytics_id')->nullable();
            $table->string('google_tag_manager_id')->nullable();
            $table->string('google_ads_id')->nullable();
            $table->string('google_ads_lead_label')->nullable();
            $table->string('meta_pixel_id')->nullable();
            $table->boolean('linkedin_enabled')->default(false);
            $table->string('linkedin_partner_id')->nullable();
            $table->string('linkedin_contact_conversion_id')->nullable();
            $table->string('linkedin_lead_conversion_id')->nullable();
            $table->longText('custom_head_scripts')->nullable();
            $table->longText('custom_body_scripts')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
