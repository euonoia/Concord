<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_allocation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('month', 7); // YYYY-MM
            $table->decimal('total_compensation', 15, 2)->default(0);
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected'])->default('sent');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('month');
            $table->index('status');
            // Foreign key removed for TiDB compatibility with users.id type variance
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_allocation_requests');
    }
};
