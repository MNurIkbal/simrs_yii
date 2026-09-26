<?php

use yii\db\Migration;

/**
 * Class m220604_135824_migrate_skema_fisioterapi_MHG2388_jadwalterapifisio_t
 */
class m220604_135824_migrate_skema_fisioterapi_MHG2388_jadwalterapifisio_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."jadwalterapifisio_t"
            ADD COLUMN IF NOT EXISTS "keterangan_drop_out" text;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_135824_migrate_skema_fisioterapi_MHG2388_jadwalterapifisio_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_135824_migrate_skema_fisioterapi_MHG2388_jadwalterapifisio_t cannot be reverted.\n";

        return false;
    }
    */
}
