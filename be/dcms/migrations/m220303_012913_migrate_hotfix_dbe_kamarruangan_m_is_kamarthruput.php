<?php

use yii\db\Migration;

/**
 * Class m220303_012913_migrate_hotfix_dbe_kamarruangan_m_is_kamarthruput
 */
class m220303_012913_migrate_hotfix_dbe_kamarruangan_m_is_kamarthruput extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('ALTER TABLE "public"."kamarruangan_m" 
    		ALTER COLUMN "is_kamarthruput" SET DEFAULT true;
    	');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220303_012913_migrate_hotfix_dbe_kamarruangan_m_is_kamarthruput cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220303_012913_migrate_hotfix_dbe_kamarruangan_m_is_kamarthruput cannot be reverted.\n";

        return false;
    }
    */
}
