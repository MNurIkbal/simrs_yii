<?php

use yii\db\Migration;

/**
 * Class m220314_123300_migrate_BTS187_BTS188_antrianjkn_r
 */
class m220314_123300_migrate_BTS187_BTS188_antrianjkn_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."antrianjkn_r" 
          ADD COLUMN IF NOT EXISTS "pendaftaran_id" int4,
          ADD COLUMN IF NOT EXISTS "additional_jkn" text;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220314_123300_migrate_BTS187_BTS188_antrianjkn_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_123300_migrate_BTS187_BTS188_antrianjkn_r cannot be reverted.\n";

        return false;
    }
    */
}
