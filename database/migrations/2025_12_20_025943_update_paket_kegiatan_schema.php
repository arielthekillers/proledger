<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Map old values to new values before changing schema
        \DB::statement("UPDATE paket_kegiatan SET status = 'Kontrak' WHERE status = 'aktif'");
        \DB::statement("UPDATE paket_kegiatan SET status = 'Selesai' WHERE status = 'selesai'");

        // 2. Modify Table Schema
        Schema::table('paket_kegiatan', function (Blueprint $table) {
            // Add new column
            $table->string('perusahaan')->nullable()->after('nama_paket');
            
            // Drop old columns
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });

        // 3. Modify Enum Column (Using Raw SQL because lack of doctrine/dbal)
        // Note: We set default to 'Kontrak'
        \DB::statement("ALTER TABLE paket_kegiatan MODIFY COLUMN status ENUM('Kontrak', 'Pemesanan', 'Distribusi', 'Selesai') DEFAULT 'Kontrak'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Enum
        \DB::statement("ALTER TABLE paket_kegiatan MODIFY COLUMN status ENUM('aktif', 'selesai') DEFAULT 'aktif'");

        // Map values back
        \DB::statement("UPDATE paket_kegiatan SET status = 'aktif' WHERE status IN ('Kontrak', 'Pemesanan', 'Distribusi')");
        \DB::statement("UPDATE paket_kegiatan SET status = 'selesai' WHERE status = 'Selesai'");

        Schema::table('paket_kegiatan', function (Blueprint $table) {
            $table->dropColumn('perusahaan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
        });
    }
};
