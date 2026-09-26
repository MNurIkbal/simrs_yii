<?php

use yii\db\Migration;

/**
 * Class m220624_114945_migrate_cssd_stokobatalkes_t
 */
class m220624_114945_migrate_cssd_stokobatalkes_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokobatalkes_t" 
            ADD COLUMN IF NOT EXISTS "cssdpenerimaanunitdet_id" int4,
            ADD COLUMN IF NOT EXISTS "cssdrusakdet_id" int4,
            ADD COLUMN IF NOT EXISTS "cssddet_id" int4;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_114945_migrate_cssd_stokobatalkes_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_114945_migrate_cssd_stokobatalkes_t cannot be reverted.\n";

        return false;
    }
    */
}
