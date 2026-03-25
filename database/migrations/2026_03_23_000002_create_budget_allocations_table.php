<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('month', 7); // YYYY-MM
            $table->decimal('total_compensation', 15, 2)->default(0);
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected'])->default('sent');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('month');
            $table->index('status');
            // no foreign key to avoid TiDB type mismatches
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_allocations');
    }
};
