<?php

use yii\db\Migration;

/**
 * Class m220801_023033_migrate_odoo_trigger_tindakansudahbayar_t_discount_insert
 */
class m220801_023033_migrate_odoo_trigger_tindakansudahbayar_t_discount_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "tindakansudahbayar_t_discount_insert" ON "public"."tindakansudahbayar_t";
        ');

        $this->execute('
            CREATE TRIGGER "tindakansudahbayar_t_discount_insert" AFTER INSERT ON "public"."tindakansudahbayar_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."tindakansudahbayar_t_discount_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023033_migrate_odoo_trigger_tindakansudahbayar_t_discount_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023033_migrate_odoo_trigger_tindakansudahbayar_t_discount_insert cannot be reverted.\n";

        return false;
    }
    */
}
