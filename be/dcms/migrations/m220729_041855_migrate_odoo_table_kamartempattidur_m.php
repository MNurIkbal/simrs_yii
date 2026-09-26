<?php

use yii\db\Migration;

/**
 * Class m220729_041855_migrate_odoo_table_kamartempattidur_m
 */
class m220729_041855_migrate_odoo_table_kamartempattidur_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."kamartempattidur_m" ADD IF NOT EXISTS "kamartempattidur_kode" VARCHAR(30);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_041855_migrate_odoo_table_kamartempattidur_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_041855_migrate_odoo_table_kamartempattidur_m cannot be reverted.\n";

        return false;
    }
    */
}
