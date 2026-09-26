<?php

use yii\db\Migration;

/**
 * Class m220609_041020_migrate_skema_fisioterapi_MHG2385_programterapi_t
 */
class m220609_041020_migrate_skema_fisioterapi_MHG2385_programterapi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."programterapi_t" 
            ADD COLUMN IF NOT EXISTS "ketidakhadiran" float4;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220609_041020_migrate_skema_fisioterapi_MHG2385_programterapi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220609_041020_migrate_skema_fisioterapi_MHG2385_programterapi_t cannot be reverted.\n";

        return false;
    }
    */
}
