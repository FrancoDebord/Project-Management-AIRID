<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Determine the next level_type (max + 1)
        $maxLevel = DB::table('pro_studies_types')->max('level_type') ?? 0;

        $typeId = DB::table('pro_studies_types')->insertGetId([
            'study_type_name' => 'Implementation Research',
            'level_type'      => $maxLevel + 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Add a generic sub-category so the type is usable immediately
        DB::table('pro_studies_types_sub_categories')->insert([
            'study_sub_category_name' => 'Not Specified',
            'level_sub_category'      => 1,
            'study_type_id'           => $typeId,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    }

    public function down(): void
    {
        $type = DB::table('pro_studies_types')
            ->where('study_type_name', 'Implementation Research')
            ->first();

        if ($type) {
            DB::table('pro_studies_types_sub_categories')
                ->where('study_type_id', $type->id)
                ->delete();
            DB::table('pro_studies_types')->where('id', $type->id)->delete();
        }
    }
};
