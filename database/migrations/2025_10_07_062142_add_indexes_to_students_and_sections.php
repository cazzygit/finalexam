<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        try {
            $database = config('database.connections.' . config('database.default') . '.database');
            $result = \DB::select(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$database, $table, $index]
            );
            return !empty($result);
        } catch (\Throwable $e) {
            return false;
        }
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Composite index for common list sorting/search by last and first name
            $table->index(['lname', 'fname'], 'students_lname_fname_index');
            // Do not add explicit indexes for FK columns; MySQL creates them automatically
        });

        Schema::table('sections', function (Blueprint $table) {
            // Additional single-column index for alphabetical section filtering per college
            $table->index('section', 'sections_section_only_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the name index only if it exists (avoid failures during refresh)
        if ($this->indexExists('students', 'students_lname_fname_index')) {
            try { \DB::statement('ALTER TABLE students DROP INDEX students_lname_fname_index'); } catch (\Throwable $e) {}
        }

        if ($this->indexExists('sections', 'sections_section_only_index')) {
            try { \DB::statement('ALTER TABLE sections DROP INDEX sections_section_only_index'); } catch (\Throwable $e) {}
        }
    }
};
