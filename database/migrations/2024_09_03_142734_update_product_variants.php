<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->foreignId('sku_id')->constrained('product_variant_skus')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->integer('stock')->nullable();
            $table->dropForeign(['sku_id']);
            $table->dropColumn('sku_id');
        });

    }
};
