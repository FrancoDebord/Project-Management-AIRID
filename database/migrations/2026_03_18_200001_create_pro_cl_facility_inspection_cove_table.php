<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Section => number of questions
    private const SECTIONS = [
        'a' =>  1,
        'b' => 24,
        'c' => 22,
        'd' => 16,
        'e' => 13,
        'f' => 13,
        'g' => 13,
        'h' => 25,
        'i' => 12,
    ];

    public function up(): void
    {
        Schema::create('pro_cl_facility_inspection_cove', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id');
            $table->json('sections_done')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_code', 50)->nullable();
            $table->unsignedBigInteger('filled_by')->nullable();

            foreach (self::SECTIONS as $sec => $count) {
                foreach (range(1, $count) as $n) {
                    $table->enum("{$sec}_q{$n}", ['yes', 'no', 'na'])->nullable();
                }
                $table->text("{$sec}_comments")->nullable();
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_facility_inspection_cove');
    }
};
