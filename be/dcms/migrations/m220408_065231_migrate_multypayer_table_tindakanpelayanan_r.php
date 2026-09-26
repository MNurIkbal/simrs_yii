<?php

use yii\db\Migration;

/**
 * Class m220408_065231_migrate_multypayer_table_tindakanpelayanan_r
 */
class m220408_065231_migrate_multypayer_table_tindakanpelayanan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."tindakanpelayanan_r" ADD COLUMN IF NOT EXISTS "is_penjaminutama" bool; 
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065231_migrate_multypayer_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065231_migrate_multypayer_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }
    */
}
