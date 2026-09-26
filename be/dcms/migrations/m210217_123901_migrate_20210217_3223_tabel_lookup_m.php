<?php

use yii\db\Migration;

/**
 * Class m210217_123901_migrate_20210217_3223_tabel_lookup_m
 */
class m210217_123901_migrate_20210217_3223_tabel_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_type='rujukan';
        ");
        $this->execute("
            DELETE from lookup_m where lookup_type='jenis_pelayanan_bpjs';
        ");
        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
            (978, 'rujukan', 'Penuh', '0', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (979, 'rujukan', 'Partial', '1', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (980, 'rujukan', 'Rujuk Balik (Non PRB)', '2', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (981, 'jenis_pelayanan_bpjs', 'Rawat Inap', '1', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (982, 'jenis_pelayanan_bpjs', 'Rawat Jalan', '2', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210217_123901_migrate_20210217_3223_tabel_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210217_123901_migrate_20210217_3223_tabel_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
