<?php

use yii\db\Migration;

/**
 * Class m220222_065109_migrate_ORDH17_validasipobarang_t
 */
class m220222_065109_migrate_ORDH17_validasipobarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipobarang_t" 
        ADD COLUMN IF NOT EXISTS "peg_penerima_id" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220222_065109_migrate_ORDH17_validasipobarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220222_065109_migrate_ORDH17_validasipobarang_t cannot be reverted.\n";

        return false;
    }
    */
}
