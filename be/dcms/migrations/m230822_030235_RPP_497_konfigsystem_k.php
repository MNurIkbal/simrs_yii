<?php

use yii\db\Migration;

/**
 * Class m230822_030235_RPP_497_konfigsystem_k
 */
class m230822_030235_RPP_497_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigsystem_k ADD COLUMN IF NOT EXISTS show_referal_pendaftaran BOOL NOT NULL DEFAULT TRUE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_030235_RPP_497_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_030235_RPP_497_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
