<?php

use yii\db\Migration;

/**
 * Class m230510_165619_odoo_cutoff_logodoocutoff_r
 */
class m230510_165619_odoo_cutoff_logodoocutoff_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS public.logodoocutoff_r (
            id serial4 NOT NULL,
            model_name varchar(50) NULL,
            cutoff_date date NULL,
            triggered_by varchar(50) NULL,
            generated_data int4 NULL,
            deleted_data int4 NULL,
            created_date timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            created_by int4 NULL
        );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230510_165619_odoo_cutoff_logodoocutoff_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230510_165619_odoo_cutoff_logodoocutoff_r cannot be reverted.\n";

        return false;
    }
    */
}
