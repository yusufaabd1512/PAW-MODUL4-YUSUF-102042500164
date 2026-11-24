<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('submissions', 'nilai')) {
                    $table->unsignedTinyInteger('nilai')->nullable()->after('jawaban');
                }
                if (!Schema::hasColumn('submissions', 'catatan_dosen')) {
                    $table->text('catatan_dosen')->nullable()->after('nilai');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('submissions', 'nilai')) {
                    $columns[] = 'nilai';
                }
                if (Schema::hasColumn('submissions', 'catatan_dosen')) {
                    $columns[] = 'catatan_dosen';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};

