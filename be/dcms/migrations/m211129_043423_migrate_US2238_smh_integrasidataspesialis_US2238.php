<?php

use yii\db\Migration;

/**
 * Class m211129_043423_migrate_US2238_smh_integrasidataspesialis_US2238
 */
class m211129_043423_migrate_US2238_smh_integrasidataspesialis_US2238 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."spesialis_m" 
          ADD COLUMN IF NOT EXISTS "spesialis_namalainnya" varchar(255) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211129_043423_migrate_US2238_smh_integrasidataspesialis_US2238 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211129_043423_migrate_US2238_smh_integrasidataspesialis_US2238 cannot be reverted.\n";

        return false;
    }
    */
}
