<?php

use yii\db\Migration;

/**
 * Class m220112_043609_migrate_hotfix_sy_cetakan
 */
class m220112_043609_migrate_hotfix_sy_cetakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."docmapping_k" 
          ADD COLUMN IF NOT EXISTS "additional_style" text COLLATE "pg_catalog"."default";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_043609_migrate_hotfix_sy_cetakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_043609_migrate_hotfix_sy_cetakan cannot be reverted.\n";

        return false;
    }
    */
}
