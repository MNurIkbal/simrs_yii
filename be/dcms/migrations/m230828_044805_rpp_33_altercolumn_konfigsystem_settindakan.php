<?php

use yii\db\Migration;

/**
 * Class m230828_044805_rpp_33_altercolumn_konfigsystem_settindakan
 */
class m230828_044805_rpp_33_altercolumn_konfigsystem_settindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN IF NOT exists "is_set_tindakan" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230828_044805_rpp_33_altercolumn_konfigsystem_settindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230828_044805_rpp_33_altercolumn_konfigsystem_settindakan cannot be reverted.\n";

        return false;
    }
    */
}
