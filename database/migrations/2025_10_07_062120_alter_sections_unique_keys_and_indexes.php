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
        Schema::table('sections', function (Blueprint $table) {
            // Drop old unique(name, college_id) if it exists
            try {
                $table->dropUnique('sections_name_college_unique');
            } catch (\Throwable $e) {
                // ignore if it doesn't exist
            }

            // Ensure supporting indexes exist for performant lookups
            if (!Schema::hasColumn('sections', 'year')) {
                // Safety: add year column if somehow missing
                $table->unsignedTinyInteger('year')->nullable()->after('name');
            }
            if (!Schema::hasColumn('sections', 'section')) {
                // Safety: add section column if somehow missing
                $table->string('section', 10)->nullable()->after('year');
            }

            // Add composite unique per college on (year, section)
            $table->unique(['college_id', 'year', 'section'], 'sections_college_year_section_unique');

            // Helpful indexes for sorting and filtering
            $table->index(['college_id', 'section'], 'sections_college_section_index');
            $table->index(['college_id', 'year'], 'sections_college_year_index');
        });

        // Add a guarded check constraint for valid year range if supported
        try {
            // MySQL 8+/PostgreSQL support CHECK; MariaDB before 10.2 ignores it
            \DB::statement("ALTER TABLE sections ADD CONSTRAINT chk_sections_year CHECK (year IS NULL OR (year >= 1 AND year <= 4))");
        } catch (\Throwable $e) {
            // Ignore if DB does not support CHECK or it already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop check constraint if possible
        try {
            \DB::statement('ALTER TABLE sections DROP CONSTRAINT chk_sections_year');
        } catch (\Throwable $e) {
            // MySQL uses DROP CHECK for named constraints; attempt alternative
            try { \DB::statement('ALTER TABLE sections DROP CHECK chk_sections_year'); } catch (\Throwable $e2) {}
        }

        Schema::table('sections', function (Blueprint $table) {
            // Avoid dropping indexes that may be referenced by FKs; they will be removed when table is dropped later
            // Optionally drop unique if safe
            try { $table->dropUnique('sections_college_year_section_unique'); } catch (\Throwable $e) {}

            // Restore old unique(name, college_id) best-effort (safe even if table will be dropped later)
            try { $table->unique(['name', 'college_id'], 'sections_name_college_unique'); } catch (\Throwable $e) {}
        });
    }
};
