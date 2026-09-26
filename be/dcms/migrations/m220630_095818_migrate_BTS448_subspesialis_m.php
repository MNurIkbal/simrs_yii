<?php

use yii\db\Migration;

/**
 * Class m220630_095818_migrate_BTS448_subspesialis_m
 */
class m220630_095818_migrate_BTS448_subspesialis_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."subspesialis_m" 
            ADD COLUMN IF NOT EXISTS "subspesialis_image" varchar(255) COLLATE "pg_catalog"."default";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220630_095818_migrate_BTS448_subspesialis_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220630_095818_migrate_BTS448_subspesialis_m cannot be reverted.\n";

        return false;
    }
    */
}
