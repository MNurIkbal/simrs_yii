<?php

use yii\db\Migration;

/**
 * Class m230708_090038_konfigsystem_k_order_bedah_tanpa_tindakan
 */
class m230708_090038_konfigsystem_k_order_bedah_tanpa_tindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS order_bedah_tanpa_tindakan bool NOT NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230708_090038_konfigsystem_k_order_bedah_tanpa_tindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230708_090038_konfigsystem_k_order_bedah_tanpa_tindakan cannot be reverted.\n";

        return false;
    }
    */
}
