<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();

            $table->foreignId('employee_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('customer_name', 255);
            $table->string('customer_phone', 30);

            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');

            // Copia de los datos del servicio al momento de reservar.
            $table->string('service_name');
            $table->decimal('service_price', 12, 2);
            $table->unsignedSmallInteger('duration_minutes');

            // pending, confirmed, cancelled, completed
            $table->string('status', 20)->default('pending');

            $table->timestamps();

            $table->index([
                'business_id',
                'appointment_date',
                'employee_id',
                'status',
            ], 'appointments_business_date_employee_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};