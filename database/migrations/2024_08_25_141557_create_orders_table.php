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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('order_type', ['Order', 'Reimbursement', 'Service', 'Material']);
            $table->date('date');
            $table->unsignedBigInteger('construction_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('supplier_id');
            $table->integer('quantity_items');
            $table->text('description');
            $table->decimal('total_value');
            $table->enum('payment_method', ['Cash', 'InvoicedPaymentForecast', 'InvoicedBoleto', 'InvoicedInvoice']);
            $table->enum('purchase_status', ['Resolved', 'Request Finance']);
            $table->timestamps();

            $table->foreign('construction_id')->references('id')->on('constructions');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
