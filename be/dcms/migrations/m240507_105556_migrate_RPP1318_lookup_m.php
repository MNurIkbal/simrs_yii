<?php

use yii\db\Migration;

/**
 * Class m240507_105556_migrate_RPP1318_lookup_m
 */
class m240507_105556_migrate_RPP1318_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'referral_marketing';");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2175, 'referral_marketing', 'Febriani', 'Febriani', 3, NULL, 'pegawai_m', '2024-04-25 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2176, 'referral_marketing', 'Suhartono Guido', 'Suhartono Guido', 4, NULL, 'pegawai_m', '2024-04-25 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2173, 'referral_marketing', 'Non Referral', 'Non Referral', 1, NULL, NULL, '2024-04-25 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2177, 'referral_marketing', 'Adi Darma', 'Adi Darma', 5, NULL, 'pegawai_m', '2024-04-25 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2174, 'referral_marketing', 'Triyono Bagio', 'Triyono Bagio', 2, NULL, 'pegawai_m', '2024-04-25 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240507_105556_migrate_RPP1318_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_105556_migrate_RPP1318_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
