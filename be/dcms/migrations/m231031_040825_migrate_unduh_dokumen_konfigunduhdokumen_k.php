<?php

use yii\db\Migration;

/**
 * Class m231031_040825_migrate_unduh_dokumen_konfigunduhdokumen_k
 */
class m231031_040825_migrate_unduh_dokumen_konfigunduhdokumen_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
            CREATE TABLE IF NOT EXISTS public.konfigunduhdokumen_k (
                nama_dokumen varchar(255) NULL,
                status_dokumen bool NULL DEFAULT true,
                created_date timestamp(6) NULL,
                created_by int4 NULL,
                is_deleted bool NULL DEFAULT false,
                is_active bool NULL DEFAULT true,
                is_multiple bool NULL DEFAULT false,
                is_reportdesigner bool NULL DEFAULT false,
                konfig_dokumen_id serial4 NOT NULL,
                type varchar NULL,
                doc_url text NULL,
                params text NULL,
                kode_report varchar NULL,
                additional_url text NULL,
                is_bgprocess bool NULL DEFAULT false,
                validation text NULL,
                CONSTRAINT konfigunduhdokumen_k_pk PRIMARY KEY (konfig_dokumen_id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231031_040825_migrate_unduh_dokumen_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231031_040825_migrate_unduh_dokumen_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }
    */
}
