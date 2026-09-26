<?php

use yii\db\Migration;

/**
 * Class m221203_071412_migrate_asalreservasi_m
 */
class m221203_071412_migrate_asalreservasi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS asalreservasi_id int4 NULL;");
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS dokterperujuk_id int4 NULL;");
        $this->execute("CREATE TABLE IF NOT EXISTS public.asalreservasi_m (
            asalreservasi_id bigserial NOT NULL,
            asalreservasi_nama varchar(50) NULL,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT asalreservasi_m_pkey PRIMARY KEY (asalreservasi_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221203_071412_migrate_asalreservasi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221203_071412_migrate_asalreservasi_m cannot be reverted.\n";

        return false;
    }
    */
}
