<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dicom_studies', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi ke patients
            |--------------------------------------------------------------------------
            | patients.id = CHAR(36) / UUID
            */

            $table->uuid('patient_id');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Orthanc
            |--------------------------------------------------------------------------
            */

            $table->string('orthanc_study_id')->unique();

            $table->string(
                'study_instance_uid',
                255
            )->unique();


            /*
            |--------------------------------------------------------------------------
            | Informasi pemeriksaan
            |--------------------------------------------------------------------------
            */

            $table->date('study_date')->nullable();

            $table->string(
                'study_time',
                32
            )->nullable();

            $table->string(
                'accession_number'
            )->nullable();

            $table->string(
                'study_description'
            )->nullable();

            $table->string(
                'modality',
                20
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Identitas dari DICOM
            |--------------------------------------------------------------------------
            */

            $table->string(
                'dicom_patient_id'
            )->nullable();

            $table->string(
                'dicom_patient_name'
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Statistik
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger(
                'series_count'
            )->default(0);

            $table->unsignedInteger(
                'instance_count'
            )->default(0);


            $table->timestamps();

            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dicom_studies');
    }
};