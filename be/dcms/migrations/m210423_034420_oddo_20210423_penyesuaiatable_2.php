<?php

use yii\db\Migration;

/**
 * Class m210423_034420_oddo_20210423_penyesuaiatable_2
 */
class m210423_034420_oddo_20210423_penyesuaiatable_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
    $this->execute('ALTER TABLE "public"."int_billing_r" ADD COLUMN if not exists "id_sync_sercon_update" text COLLATE "pg_catalog"."default";');

    $this->execute('ALTER TABLE "public"."int_billing_r" ADD COLUMN if not exists "sync_respon_update" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_034420_oddo_20210423_penyesuaiatable_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_034420_oddo_20210423_penyesuaiatable_2 cannot be reverted.\n";

        return false;
    }
    */
}
