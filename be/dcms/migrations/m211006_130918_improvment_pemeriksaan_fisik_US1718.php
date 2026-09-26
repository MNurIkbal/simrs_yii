<?php

use yii\db\Migration;

/**
 * Class m211006_130918_improvment_pemeriksaan_fisik_US1718
 */
class m211006_130918_improvment_pemeriksaan_fisik_US1718 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS gcs_hasil_metode int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211006_130918_improvment_pemeriksaan_fisik_US1718 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211006_130918_improvment_pemeriksaan_fisik_US1718 cannot be reverted.\n";

        return false;
    }
    */
}
