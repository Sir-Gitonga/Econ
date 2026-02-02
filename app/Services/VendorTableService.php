<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VendorTableService
{
    /**
     * Creates vendor-isolated tables automatically when a vendor registers.
     */
    public static function createVendorTables($vendorId)
    {
        $vendorTables = [
            'users',
            'products',
            'categories',
            'brands',
            'slides',
        ];

        foreach ($vendorTables as $baseTable) {
            $vendorTable = "{$baseTable}_vendor_{$vendorId}";

            if (!Schema::hasTable($vendorTable)) {

                Schema::create($vendorTable, function (Blueprint $table) use ($baseTable) {
                    switch ($baseTable) {
                        case 'users':
                            $table->id();
                            $table->string('name');
                            $table->string('email')->unique();
                            $table->string('mobile')->nullable()->unique();
                            $table->string('password');
                            $table->rememberToken();
                            $table->timestamps();
                            break;

                        case 'products':
                            $table->id();
                            $table->string('name');
                            $table->text('description')->nullable();
                            $table->decimal('price', 10, 2);
                            $table->integer('stock')->default(0);
                            $table->timestamps();
                            break;

                        case 'categories':
                            $table->id();
                            $table->string('name');
                            $table->string('slug')->unique();
                            $table->timestamps();
                            break;

                        case 'brands':
                            $table->id();
                            $table->string('name');
                            $table->string('logo')->nullable();
                            $table->timestamps();
                            break;

                        case 'slides':
                            $table->id();
                            $table->string('title')->nullable();
                            $table->string('subtitle')->nullable();
                            $table->string('image');
                            $table->string('link')->nullable();
                            $table->integer('order')->default(0);
                            $table->timestamps();
                            break;
                    }
                });
            }
        }
    }
}
