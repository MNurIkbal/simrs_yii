<?php

use yii\db\Migration;

/**
 * Class m220729_075829_migrate_odoo_table_tindakanpelayanan_r
 */
class m220729_074829_migrate_odoo_table_tindakanpelayanan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."tindakanpelayanan_r" ADD IF NOT EXISTS "tarif_subpayer" float8 DEFAULT 0;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_074829_migrate_odoo_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_074829_migrate_odoo_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }
    */
}
