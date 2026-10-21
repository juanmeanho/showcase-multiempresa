
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('business_agenda_settings')) {
            Schema::create('business_agenda_settings', function (Blueprint $table) {
                $table->id();

                $table->foreignId('business_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unsignedSmallInteger('slot_interval_minutes')
                    ->default(60);

                $table->time('opening_time')->default('08:00:00');
                $table->time('closing_time')->default('18:00:00');

                $table->timestamps();
            });
        }

        if (! Schema::hasTable('employee_availability_slots')) {
            Schema::create('employee_availability_slots', function (Blueprint $table) {
                $table->id();

                $table->foreignId('business_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('employee_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                $table->unique(
                    ['employee_id', 'day_of_week', 'start_time'],
                    'employee_slot_day_time_unique'
                );

                $table->index(
                    ['business_id', 'employee_id', 'day_of_week'],
                    'emp_slots_business_employee_day_idx'
                );
            });
        } else {
            $indexes = collect(
                \Illuminate\Support\Facades\DB::select(
                    'SHOW INDEX FROM employee_availability_slots'
                )
            )->pluck('Key_name')->unique();

            if (! $indexes->contains('emp_slots_business_employee_day_idx')) {
                Schema::table('employee_availability_slots', function (Blueprint $table) {
                    $table->index(
                        ['business_id', 'employee_id', 'day_of_week'],
                        'emp_slots_business_employee_day_idx'
                    );
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_availability_slots');
        Schema::dropIfExists('business_agenda_settings');
    }
};
