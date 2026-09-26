<?php

use yii\db\Migration;

/**
 * Class m220209_064052_improvment_konfigurasi_racikan_DHC66
 */
class m220209_064052_improvment_konfigurasi_racikan_DHC66 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS is_freetext BOOLEAN DEFAULT FALSE
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220209_064052_improvment_konfigurasi_racikan_DHC66 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220209_064052_improvment_konfigurasi_racikan_DHC66 cannot be reverted.\n";

        return false;
    }
    */
}
