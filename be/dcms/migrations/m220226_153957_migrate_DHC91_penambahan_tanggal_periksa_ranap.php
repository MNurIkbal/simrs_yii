<?php

use yii\db\Migration;

/**
 * Class m220226_153957_migrate_DHC91_penambahan_tanggal_periksa_ranap
 */
class m220226_153957_migrate_DHC91_penambahan_tanggal_periksa_ranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienadmisi_t ADD IF NOT EXISTS tgl_masukperiksa timestamp(6);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220226_153957_migrate_DHC91_penambahan_tanggal_periksa_ranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220226_153957_migrate_DHC91_penambahan_tanggal_periksa_ranap cannot be reverted.\n";

        return false;
    }
    */
}
