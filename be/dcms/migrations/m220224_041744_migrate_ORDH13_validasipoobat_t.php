<?php

use yii\db\Migration;

/**
 * Class m220224_041744_migrate_ORDH13_validasipoobat_t
 */
class m220224_041744_migrate_ORDH13_validasipoobat_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipoobat_t" 
        ADD COLUMN IF NOT EXISTS "tgl_tercetak" timestamp;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220224_041744_migrate_ORDH13_validasipoobat_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220224_041744_migrate_ORDH13_validasipoobat_t cannot be reverted.\n";

        return false;
    }
    */
}
