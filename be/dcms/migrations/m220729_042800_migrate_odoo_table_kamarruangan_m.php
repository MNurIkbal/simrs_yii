<?php

use yii\db\Migration;

/**
 * Class m220729_042800_migrate_odoo_table_kamarruangan_m
 */
class m220729_042800_migrate_odoo_table_kamarruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."kamarruangan_m" ADD IF NOT EXISTS "kamarruangan_kode" VARCHAR(30);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_042800_migrate_odoo_table_kamarruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_042800_migrate_odoo_table_kamarruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
