<?php

use yii\db\Migration;

/**
 * Class m211206_001824_migrate_US2408_rmsenusbulananranap
 */
class m211206_001824_migrate_US2408_rmsenusbulananranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."kamarruangan_m" 
          ADD COLUMN IF NOT EXISTS "is_kamarthruput" bool DEFAULT false;
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211206_001824_migrate_US2408_rmsenusbulananranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211206_001824_migrate_US2408_rmsenusbulananranap cannot be reverted.\n";

        return false;
    }
    */
}
