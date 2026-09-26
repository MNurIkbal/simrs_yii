<?php

use yii\db\Migration;

/**
 * Class m210314_054606_migrate_20200314_adjusmenbarangkeluar_t
 */
class m210314_054606_migrate_20200314_adjusmenbarangkeluar_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."adjusmenbarangkeluar_t" ADD COLUMN IF NOT EXISTS "no_batch" varchar(100) COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210314_054606_migrate_20200314_adjusmenbarangkeluar_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210314_054606_migrate_20200314_adjusmenbarangkeluar_t cannot be reverted.\n";

        return false;
    }
    */
}
