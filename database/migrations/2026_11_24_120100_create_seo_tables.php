<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('score');
            $table->unsignedInteger('page_count');
            $table->json('report');
            $table->timestamps();
        });

        // Las páginas públicas también pueden pedir a los buscadores que no las indexen.
        Schema::table('insights', function (Blueprint $table) {
            $table->string('og_image_alt')->nullable()->after('meta_description');
            $table->boolean('robots_index')->default(true)->after('og_image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->dropColumn(['og_image_alt', 'robots_index']);
        });
        Schema::dropIfExists('seo_audits');
        Schema::dropIfExists('seo_settings');
    }
};
