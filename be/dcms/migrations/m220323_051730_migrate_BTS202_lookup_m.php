<?php

use yii\db\Migration;

/**
 * Class m220323_051730_migrate_BTS202_lookup_m
 */
class m220323_051730_migrate_BTS202_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1173,1174,1175,1176,1177,1178,1179);
        ");

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1173, 'kelas_rawat_naik_bpjs', 'VVIP', '1', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1174, 'kelas_rawat_naik_bpjs', 'VIP', '2', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1175, 'kelas_rawat_naik_bpjs', 'Kelas 1', '3', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1176, 'kelas_rawat_naik_bpjs', 'Kelas 2', '4', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1177, 'kelas_rawat_naik_bpjs', 'Kelas 3', '5', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1178, 'kelas_rawat_naik_bpjs', 'ICCU', '6', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1179, 'kelas_rawat_naik_bpjs', 'ICU', '7', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_051730_migrate_BTS202_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_051730_migrate_BTS202_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
