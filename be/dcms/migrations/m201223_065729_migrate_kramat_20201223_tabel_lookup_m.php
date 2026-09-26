<?php

use yii\db\Migration;

/**
 * Class m201223_065729_migrate_kramat_20201223_tabel_lookup_m
 */
class m201223_065729_migrate_kramat_20201223_tabel_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DELETE FROM lookup_m WHERE lookup_id = 732;');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
(732, 'title_pendaftaran', 'karcis', 'Karcis', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201223_065729_migrate_kramat_20201223_tabel_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201223_065729_migrate_kramat_20201223_tabel_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
