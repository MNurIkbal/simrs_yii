<?php

use yii\db\Migration;

/**
 * Class m251115_064039_migrate_DSV_2158_dokumenrujukanbantaran_t
 */
class m251115_064039_migrate_DSV_2158_dokumenrujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
                CREATE TABLE IF NOT EXISTS public.dokumenrujukanbantaran_t (
                    dokumenrujukanbantaran_id serial8 NOT NULL,
                    rujukanbantaran_id int4 NOT NULL,
                    nama_dokumen varchar(200) NOT NULL,
                    url_dokumen text NOT NULL,
                    created_by int4 NULL,
                    created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
                    last_modified_by int4 NULL,
                    last_modified_date timestamp(6) NULL,
                    deleted_by int4 NULL,
                    deleted_date timestamp(6) NULL,
                    is_deleted bool NOT NULL default false,
                    is_active bool NOT NULL default true,
                    CONSTRAINT dokumenrujukanbantaran_t_pkey PRIMARY KEY (dokumenrujukanbantaran_id)
                );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_064039_migrate_DSV_2158_dokumenrujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_064039_migrate_DSV_2158_dokumenrujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
