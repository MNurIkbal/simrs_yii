<?php

use yii\db\Migration;

/**
 * Class m211006_130957_improvment_konfigpelayanan_US1719
 */
class m211006_130957_improvment_konfigpelayanan_US1719 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."konfigpelayanan_k" (
                "konfigpelayanan_id" serial8,
                "instalasi_id" int4,
                "ruanganproses_id" int4,
                "title" varchar(255) COLLATE "pg_catalog"."default",
                "nama_fitur" varchar(100) COLLATE "pg_catalog"."default",
                "is_dokter" bool DEFAULT false,
                "is_perawat" bool DEFAULT false,
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
                CONSTRAINT "konfigpelayanan_k_pkey" PRIMARY KEY ("konfigpelayanan_id")
            );
        ');

        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi IN (
            \'groupjenisobat_perawat_bmhp\',
            \'groupjenisobat_perawat_resep\'
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value") VALUES 
            (\'groupjenisobat_perawat_resep\', 0, \'konfigurasi untuk pengambilan obat perawat di form resep\', \'[620]\'),
            (\'groupjenisobat_perawat_bmhp\', 0, \'konfigurasi untuk pengambilan obat perawat di form bmhp\', \'[620]\');

        ');

        $this->execute('
            TRUNCATE TABLE konfigpelayanan_k RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."konfigpelayanan_k"("konfigpelayanan_id", "instalasi_id", "nama_fitur", "is_dokter", "is_perawat", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by", "ruanganproses_id", "title") VALUES 
            (1, 1, \'reseptur\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/reseptur-modal?id=#pendaftaran_id#","wrapper":"#modal-reseptur .modal-content","icon":"fa-medkit"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Resep\'),
            (2, 1, \'tindakan\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-tindakan-bmhp?id=#pendaftaran_id#","icon":"fa-stethoscope","wrapper":"#modal-form .modal-content"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Tindakan\'),
            (3, 1, \'laboratorium\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&type=laboratorium","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Laboratorium\'),
            (4, 1, \'radiologi\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&type=radiologi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Radiologi\'),
            (5, 1, \'bedah\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&type=bedah","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Penjadwalan\'),
            (6, 1, \'gizi\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal-diet-pasien?id=#pendaftaran_id#&type=gizi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope","width":"65%"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Diet\'),
            (7, 1, \'konsul\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/konsulpoli?id=#pendaftaran_id#&pasien_id=#pasien_id#&is_modal=true","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Konsultasi\'),
            (8, 2, \'verbal-order\', \'t\', \'t\', \'{"icon":"fa-plus","onclick":"showVerbalOrder()"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Verbal Order\'),
            (9, 2, \'reseptur\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal-reseptur?id=#pendaftaran_id#&cppt_id=#cppt_id#","wrapper":"#modal-reseptur .modal-content","icon":"fa-medkit"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Resep\'),
            (10, 2, \'tindakan\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-tindakan-bmhp?id=#pendaftaran_id#&cppt_id=#cppt_id#","icon":"fa-stethoscope","wrapper":"#modal-form .modal-content"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Tindakan\'),
            (11, 2, \'laboratorium\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal?id=#pendaftaran_id#&type=laboratorium&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Laboratorium\'),
            (12, 2, \'radiologi\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal?id=#pendaftaran_id#&type=radiologi&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Radiologi\'),
            (13, 2, \'bedah\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal?id=#pendaftaran_id#&type=bedah&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Penjadwalan\'),
            (14, 2, \'gizi\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal-diet-pasien?id=#pendaftaran_id#&type=gizi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope","width":"65%"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Diet\'),
            (15, 3, \'reseptur\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal-reseptur?id=#pendaftaran_id#&pasien_id=#pasien_id#&cppt_id=#cppt_id#","wrapper":"#modal-reseptur .modal-content","icon":"fa-medkit"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Resep\'),
            (16, 3, \'tindakan\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-tindakan-bmhp?id=#pendaftaran_id#&pasien_id=#pasien_id#&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Tindakan\'),
            (17, 3, \'laboratorium\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal?type=laboratorium&id=#pendaftaran_id#&pasien_id=#pasien_id#&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Laboratorium\'),
            (18, 3, \'radiologi\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal?type=radiologi&id=#pendaftaran_id#&pasien_id=#pasien_id#&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Radiologi\'),
            (19, 3, \'bedah\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal?type=bedah&id=#pendaftaran_id#&pasien_id=#pasien_id#&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Penjadwalan\'),
            (20, 3, \'gizi\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal-diet-pasien?id=#pendaftaran_id#&type=gizi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope","width":"65%"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Diet\'),
            (21, 3, \'konsul\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/permintaan-konsul?id=#pendaftaran_id#&pasien_id=#pasien_id#&is_modal=true&cppt_id=#cppt_id#","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Konsultasi\'),
            (22, 2, \'konsul\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal?id=#pendaftaran_id#&type=bedah","icon":"fa-stethoscope","disabled":true}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL, NULL, \'Konsultasi\'),
            (23, 2, \'darah\', \'t\', \'t\', \'{"url":"/igd/pemeriksaan-igd/form-modal?id=#pendaftaran_id#&type=bedah","icon":"fa-stethoscope","disabled":true}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL, NULL, \'Darah\'),
            (24, 3, \'darah\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/penunjang?id=#pendaftaran_id#","icon":"fa-stethoscope","disabled":true}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL, NULL, \'Darah\'),
            (25, 1, \'fisioterapi\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&type=fisioterapi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Fisioterapi\'),
            (26, 1, \'verbal-order\', \'t\', \'t\', \'{"icon":"fa-plus","url":"/rajal/pemeriksaan/create-verbal-order?id=#pendaftaran_id#","wrapper":"#modal-lab .modal-content","width":"50%"}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Verbal Order\'),
            (27, 1, \'darah\', \'t\', \'t\', \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&type=bedah","icon":"fa-stethoscope","disabled":true}\', \'2021-10-05 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL, NULL, \'Darah\');

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211006_130957_improvment_konfigpelayanan_US1719 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211006_130957_improvment_konfigpelayanan_US1719 cannot be reverted.\n";

        return false;
    }
    */
}
