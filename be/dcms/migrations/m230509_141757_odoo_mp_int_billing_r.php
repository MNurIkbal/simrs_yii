<?php

use yii\db\Migration;

/**
 * Class m230509_141757_odoo_mp_int_billing_r
 */
class m230509_141757_odoo_mp_int_billing_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.int_billing_r ADD COLUMN IF NOT EXISTS is_bill_multipayer bool NOT NULL DEFAULT FALSE");

        $this->execute("ALTER TABLE public.int_billing_r ADD COLUMN IF NOT EXISTS jumlah_mainpayer float8 NULL;");

        $this->execute("ALTER TABLE public.int_billing_r ADD COLUMN IF NOT EXISTS jumlah_subpayer float8 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_141757_odoo_mp_int_billing_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_141757_odoo_mp_int_billing_r cannot be reverted.\n";

        return false;
    }
    */
}
