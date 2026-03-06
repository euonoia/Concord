<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Following TiDB rules: CREATE TABLE IF NOT EXISTS
        // Using DB::statement for precise TiDB compatibility if needed, 
        // but Schema::create is generally fine if it translates well.
        // However, the rule says "PRIMARY KEY (id) and AUTO_INCREMENT properties MUST 
        // be injected directly into the initial CREATE TABLE execution block."
        
        if (!Schema::hasTable('otps')) {
            Schema::create('otps', function (Blueprint $table) {
                // TiDB signed bigint for IDs
                $table->id(); 
                $table->string('identifier')->index(); // email or phone
                $table->string('otp_code');
                $table->timestamp('expires_at');
                $table->integer('attempts')->default(0);
                $table->boolean('is_used')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
