<?php

use yii\db\Migration;

/**
 * Class m211222_024555_migrate_tindakanpelayanan_t
 */
class m211222_024555_migrate_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" 
  ADD COLUMN if not exists "tindakanpelayananasal_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211222_024555_migrate_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_024555_migrate_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
