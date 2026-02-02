<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('gateway')->nullable();
            $table->text('api_key')->nullable();
            $table->string('instance_id')->nullable();
            $table->string('base_url')->nullable();
            $table->text('account_sid')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('from_number')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_whatsapp_settings');
    }
};
