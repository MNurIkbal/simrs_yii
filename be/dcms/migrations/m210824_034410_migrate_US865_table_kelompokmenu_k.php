<?php

use yii\db\Migration;

/**
 * Class m210824_034410_migrate_US865_table_kelompokmenu_k
 */
class m210824_034410_migrate_US865_table_kelompokmenu_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."kelompokmenu_k" 
            ADD COLUMN IF NOT EXISTS "urutan" int4;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_034410_migrate_US865_table_kelompokmenu_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_034410_migrate_US865_table_kelompokmenu_k cannot be reverted.\n";

        return false;
    }
    */
}
