<?php

use yii\db\Migration;

/**
 * Class m220218_081437_migrate_BTS135_septujuankunjungan_lookup_m
 */
class m220218_081437_migrate_BTS135_septujuankunjungan_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1154,1155,1156,1157,1158,1159,1160,1161,1162,1163,1164,1165,1166,1167,1168,1169);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1154, 'prosedur_lanjut_bpjs', 'Radioterapi', 'Radioterapi', NULL, '1', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1155, 'prosedur_lanjut_bpjs', 'Kemoterapi', 'Kemoterapi', NULL, '2', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1156, 'prosedur_lanjut_bpjs', 'Rehabilitasi Medik', 'Rehabilitasi Medik', NULL, '3', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1157, 'prosedur_lanjut_bpjs', 'Rehabilitasi Psikososial', 'Rehabilitasi Psikososial', NULL, '4', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1158, 'prosedur_lanjut_bpjs', 'Transfusi Darah', 'Transfusi Darah', NULL, '5', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1159, 'prosedur_lanjut_bpjs', 'Pelayanan Gigi', 'Pelayanan Gigi', NULL, '6', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1160, 'prosedur_lanjut_bpjs', 'Hemodialisa', 'Hemodialisa', NULL, '12', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1161, 'prosedur_tidak_lanjut_bpjs', 'Laboratorium', 'Laboratorium', NULL, '7', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1162, 'prosedur_tidak_lanjut_bpjs', 'USG', 'USG', NULL, '8', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1163, 'prosedur_tidak_lanjut_bpjs', 'Farmasi', 'Farmasi', NULL, '9', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1164, 'prosedur_tidak_lanjut_bpjs', 'Lain-lain', 'Lain-lain', NULL, '10', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1165, 'prosedur_tidak_lanjut_bpjs', 'MRI', 'MRI', NULL, '11', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1166, 'assesmen_pelayanan_bpjs', 'Poli spesialis tidak tersedia pada hari sebelumnya', 'Poli spesialis tidak tersedia pada hari sebelumnya', NULL, '1', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1167, 'assesmen_pelayanan_bpjs', 'Jam Poli telah berakhir pada hari sebelumnya', 'Jam Poli telah berakhir pada hari sebelumnya', NULL, '2', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1168, 'assesmen_pelayanan_bpjs', ' Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya', ' Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya', NULL, '3', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1169, 'assesmen_pelayanan_bpjs', 'Atas instruksi Rumah Sakit', 'Atas instruksi Rumah Sakit', NULL, '4', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220218_081437_migrate_BTS135_septujuankunjungan_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220218_081437_migrate_BTS135_septujuankunjungan_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
