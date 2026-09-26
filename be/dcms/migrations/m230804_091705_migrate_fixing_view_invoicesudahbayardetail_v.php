<?php

use yii\db\Migration;

/**
 * Class m230804_091705_migrate_fixing_view_invoicesudahbayardetail_v
 */
class m230804_091705_migrate_fixing_view_invoicesudahbayardetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sie_invoicesudahbayar");

        $this->execute("DROP VIEW IF EXISTS invoicesudahbayardetail_v");

        $invoicesudahbayardetail_v = file_get_contents(__DIR__ . '/definitions/invoicesudahbayardetail_v.sql');
        $this->execute($invoicesudahbayardetail_v);

        $sie_invoicesudahbayar = file_get_contents(__DIR__ . '/definitions/sie_invoicesudahbayar.sql');
        $this->execute($sie_invoicesudahbayar);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230804_091705_migrate_fixing_view_invoicesudahbayardetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230804_091705_migrate_fixing_view_invoicesudahbayardetail_v cannot be reverted.\n";

        return false;
    }
    */
}
