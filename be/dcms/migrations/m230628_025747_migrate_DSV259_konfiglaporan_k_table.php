<?php

use yii\db\Migration;

/**
 * Class m230628_025747_migrate_DSV259_konfiglaporan_k_table
 */
class m230628_025747_migrate_DSV259_konfiglaporan_k_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.konfiglaporan_k (
            konfiglaporan_id serial4 NOT NULL,
            key_laporan varchar(30) NULL,
            jenis_laporan varchar(50) NULL,
            source_view varchar(50) NULL,
            \"filter\" text NULL,
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
            footer text NULL,
            CONSTRAINT konfiglaporan_k_pkey PRIMARY KEY (konfiglaporan_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230628_025747_migrate_DSV259_konfiglaporan_k_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230628_025747_migrate_DSV259_konfiglaporan_k_table cannot be reverted.\n";

        return false;
    }
    */
}
