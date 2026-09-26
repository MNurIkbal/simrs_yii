<?php

use yii\db\Migration;

/**
 * Class m230306_040053_create_table_listerror_r
 */
class m230306_040053_create_table_listerror_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS public.listerror_r (
            listerror_id serial4 NOT NULL,
            nama varchar(255) NULL,
            pesan text NULL,
            tgl_error timestamp(0) NULL,
            CONSTRAINT listerror_r_pkey PRIMARY KEY (listerror_id)
        );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_040053_create_table_listerror_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_040053_create_table_listerror_r cannot be reverted.\n";

        return false;
    }
    */
}
