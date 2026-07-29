<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('status');
            $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }
    public function down(): void {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['accepted_at', 'rejected_at', 'rejection_reason']);
        });
    }
};