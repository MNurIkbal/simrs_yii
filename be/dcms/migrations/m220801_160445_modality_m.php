<?php

use yii\db\Migration;

/**
 * Class m220801_160445_modality_m
 */
class m220801_160445_modality_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."modalitytype_m" (
          "modalitytype_id" serial8,
          "modality_nama" varchar(255) COLLATE "pg_catalog"."default",
          "modality_kode" varchar(255) COLLATE "pg_catalog"."default",
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "modalitytype_m_pkey" PRIMARY KEY ("modalitytype_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."modalitytype_m" 
          OWNER TO "postgres";
        ');

        $this->execute("
            TRUNCATE TABLE modalitytype_m RESTART IDENTITY;
        ");

        $this->execute('
        INSERT INTO "modalitytype_m"("modality_nama", "modality_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date") VALUES
        (\'Computed Radiography\', \'CR\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Computed Tomography\', \'CT\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Magnetic Resonance\', \'MR\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Ultrasound\', \'US\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Other\', \'OT\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Biomagnetic imaging\', \'BI\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Color flow Doppler\', \'CD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Duplex Doppler\', \'DD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Diaphanography\', \'DG\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Endoscopy\', \'ES\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Laser surface scan\', \'LS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Positron emission tomography (PET)\', \'PT\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radiographic imaging (conventional film/screen)\', \'RG\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Single-photon emission computed tomography (SPECT)\', \'ST\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Thermography\', \'TG\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'X-Ray Angiography\', \'XA\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radio Fluoroscopy\', \'RF\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radiotherapy Image\', \'RTIMAGE\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radiotherapy Dose\', \'RTDOSE\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radiotherapy Structure Set\', \'RTSTRUCT\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Radiotherapy Plan\', \'RTPLAN\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'RT Treatment Record\', \'RTRECORD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Hard Copy\', \'HC\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Digital Radiography\', \'DX\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Nuclear Medicine\', \'NM\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Mammography\', \'MG\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Intra-oral Radiography\', \'IO\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Panoramic X-Ray\', \'PX\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'General Microscopy\', \'GM\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Slide Microscopy\', \'SM\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'External-camera Photography\', \'XC\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Presentation State\', \'PR\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Audio ECG\', \'AU\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Cardiac Electrophysiology\', \'EPS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Hemodynamic Waveform\', \'HD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'SR Document\', \'SR\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Intravascular Ultrasound\', \'IVUS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Ophthalmic Photography\', \'OP\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL),
        (\'Stereometric Relationship\', \'SMR\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL)');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_160445_modality_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_160445_modality_m cannot be reverted.\n";

        return false;
    }
    */
}
