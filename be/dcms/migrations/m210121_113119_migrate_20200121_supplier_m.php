<?php

use yii\db\Migration;

/**
 * Class m210121_113119_migrate_20200121_supplier_m
 */
class m210121_113119_migrate_20200121_supplier_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."supplier_m" ADD IF NOT EXISTS "persen_ppn" int2 DEFAULT 0;');
        $this->execute('ALTER TABLE "public"."supplier_m" ADD IF NOT EXISTS "pajak_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_113119_migrate_20200121_supplier_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_113119_migrate_20200121_supplier_m cannot be reverted.\n";

        return false;
    }
    */
}
