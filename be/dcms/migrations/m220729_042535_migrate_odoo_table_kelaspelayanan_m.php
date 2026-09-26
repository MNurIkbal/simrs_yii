<?php

use yii\db\Migration;

/**
 * Class m220729_042535_migrate_odoo_table_kelaspelayanan_m
 */
class m220729_042535_migrate_odoo_table_kelaspelayanan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."kelaspelayanan_m" ADD IF NOT EXISTS "instalasi_id" int4 DEFAULT 0;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_042535_migrate_odoo_table_kelaspelayanan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_042535_migrate_odoo_table_kelaspelayanan_m cannot be reverted.\n";

        return false;
    }
    */
}
