<?php

use yii\db\Migration;

/**
 * Class m220531_065748_migrate_hotfix_lookup_m
 */
class m220531_065748_migrate_hotfix_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1166, 1167, 1168, 1169, 1199);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1199, 'assesmen_pelayanan_bpjs', 'Tujuan Kontrol', 'Tujuan Kontrol', NULL, 5, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1166, 'assesmen_pelayanan_bpjs', 'Poli spesialis tidak tersedia pada hari sebelumnya', 'Poli spesialis tidak tersedia pada hari sebelumnya', NULL, 1, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1167, 'assesmen_pelayanan_bpjs', 'Jam Poli telah berakhir pada hari sebelumnya', 'Jam Poli telah berakhir pada hari sebelumnya', NULL, 2, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1168, 'assesmen_pelayanan_bpjs', ' Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya', ' Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya', NULL, 3, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1169, 'assesmen_pelayanan_bpjs', 'Atas instruksi Rumah Sakit', 'Atas instruksi Rumah Sakit', NULL, 4, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);

            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_065748_migrate_hotfix_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_065748_migrate_hotfix_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
