<?php

use yii\db\Migration;

/**
 * Class m230926_064728_migrate_DSV324_surat_keterangan_m
 */
class m230926_064728_migrate_DSV324_surat_keterangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.surat_keterangan_m (
            surat_keterangan_id serial4 NOT NULL,
            judul_surat varchar(100) NULL,
            section_1 text NULL,
            section_2 text NULL,
            section_3 text NULL,
            section_hasil text NULL,
            additional_data text NULL,
            created_date timestamp NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp NULL,
            deleted_by int4 NULL,
            urutan int4 NULL,
            kode_surat varchar(3) NULL,
            enable_form_pegawai bool NOT NULL DEFAULT false,
            enable_atas_permintaan bool NOT NULL DEFAULT true,
            show_umur bool NOT NULL DEFAULT false,
            enable_no_surat bool NOT NULL DEFAULT true,
            enable_pemberian_informasi bool NOT NULL DEFAULT false,
            enable_no_rm bool NULL DEFAULT false,
            CONSTRAINT pk_surat_keterangan_m_id PRIMARY KEY (surat_keterangan_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_064728_migrate_DSV324_surat_keterangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_064728_migrate_DSV324_surat_keterangan_m cannot be reverted.\n";

        return false;
    }
    */
}
