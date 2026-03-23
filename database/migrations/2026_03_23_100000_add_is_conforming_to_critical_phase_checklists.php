<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'pro_cl_cone_llin',
        'pro_cl_cone_irs_bl_treat',
        'pro_cl_cone_irs_bl_test',
        'pro_cl_tunnel_test',
        'pro_cl_llin_washing',
        'pro_cl_llin_exp_huts',
        'pro_cl_irs_treatment',
        'pro_cl_irs_trial',
        'pro_cl_cone_irs_walls',
        'pro_cl_cylinder_bioassay',
        'pro_cl_cdc_bottle_coating',
        'pro_cl_cdc_bottle_test',
        'pro_cl_spatial_repellents',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->boolean('is_conforming')->nullable()->default(null)->after('comments');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('is_conforming');
            });
        }
    }
};
