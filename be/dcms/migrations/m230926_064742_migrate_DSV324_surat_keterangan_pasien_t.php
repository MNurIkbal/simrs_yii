<?php

use yii\db\Migration;

/**
 * Class m230926_064742_migrate_DSV324_surat_keterangan_pasien_t
 */
class m230926_064742_migrate_DSV324_surat_keterangan_pasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.surat_keterangan_pasien_t (
            surat_keterangan_pasien_id serial4 NOT NULL,
            pendaftaran_id int4 NOT NULL,
            no_surat varchar(200) NULL,
            nama_pasien varchar(255) NULL,
            tempat_lahir varchar(25) NULL,
            tgl_lahir date NULL,
            jenis_kelamin varchar(20) NULL,
            alamat text NULL,
            atas_permintaan varchar(20) NULL,
            nama_pegawai varchar(100) NULL,
            nip_pegawai varchar(30) NULL,
            jabatan_pegawai varchar(30) NULL,
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
            surat_keterangan_id serial4 NOT NULL,
            pekerjaan varchar(100) NULL,
            no_rekam_medik varchar(50) NULL,
            CONSTRAINT pk_surat_keterangan_id PRIMARY KEY (surat_keterangan_pasien_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_064742_migrate_DSV324_surat_keterangan_pasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_064742_migrate_DSV324_surat_keterangan_pasien_t cannot be reverted.\n";

        return false;
    }
    */
}
