<?php

use yii\db\Migration;

/**
 * Class m250416_060014_RPP1927_lookupm_seederjenistransaksi
 */
class m250416_060014_RPP1927_lookupm_seederjenistransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_type = \'metode_transaksi\';
        ');

        $this->execute('
                INSERT INTO lookup_m (lookup_type,lookup_name,lookup_value,lookup_urutan,lookup_kode,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by) VALUES
            (\'metode_transaksi\',\'Penjamin\',\'PENJAMIN\',3,NULL,NULL,\'2025-04-15 00:00:00.000\',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (\'metode_transaksi\',\'Non Tunai\',\'NON TUNAI\',2,NULL,NULL,\'2025-04-15 15:17:06.762\',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (\'metode_transaksi\',\'Tunai\',\'TUNAI\',1,NULL,NULL,\'2025-04-15 00:00:00.000\',NULL,NULL,NULL,NULL,false,true,NULL,NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250416_060014_RPP1927_lookupm_seederjenistransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250416_060014_RPP1927_lookupm_seederjenistransaksi cannot be reverted.\n";

        return false;
    }
    */
}
