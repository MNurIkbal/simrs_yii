<?php

use yii\db\Migration;

/**
 * Class m230614_081010_integrasi_roche_r
 */
class m230614_081010_integrasi_roche_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."integrasi_roche_r" 
            ADD IF NOT EXISTS "state" varchar NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_081010_integrasi_roche_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_081010_integrasi_roche_r cannot be reverted.\n";

        return false;
    }
    */
}
