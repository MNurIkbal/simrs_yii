<?php

use yii\db\Migration;

/**
 * Class m251115_063922_migrate_DSV_2158_rujukanbantaran_t
 */
class m251115_063922_migrate_DSV_2158_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.rujukanbantaran_t(
                rujukanbantaran_id serial8 NOT NULL,
                no_rujukanbantaran varchar(100) NULL,
                tgl_kunjungan date NULL,
                uptasal_id int4 NULL,
                uptasal_nama varchar(150) NULL,
                instalasi_id int4 NULL,
                ruangan_id int4 NULL,
                dokter_id int4 NULL,
                jadwaldokter_id int4 NULL,
                jam_buka time(6) NULL,
                jam_tutup time(6) NULL,
                carabayar_id int4 NULL,
                penjamin_id int4 NULL,
                pasien_id int4 NULL,
                no_rekam_medik varchar(150) NULL,
                nama_pasien  varchar(150) NOT NULL,
                jenis_identitas varchar(150) NULL,
                no_identitas_pasien varchar(150) NULL,
                tempat_lahir varchar(150) NULL,
                tgl_lahir date NULL,
                jenis_kelamin varchar(15) NULL,
                keterangan_rujukan text NULL,
                status_unduh_dokumen int4 NULL,
                keterangan_penolakan_rujukan text NULL,
                status_verifikasi_bantaran int4 NULL,
                pegawaiverifikasi_id int4 NULL,
                tgl_verifikasi_bantaran timestamp(6) NULL,
                status_pelayanan_bantaran int4 NULL,
                pendaftaran_id int4 NULL,
                created_by int4 NULL,
                created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
                last_modified_by int4 NULL,
                last_modified_date timestamp(6) NULL,
                deleted_by int4 NULL,
                deleted_date timestamp(6) NULL,
                is_deleted bool NOT NULL default false,
                is_active bool NOT NULL default true,
                CONSTRAINT rujukanbantaran_t_pkey PRIMARY KEY (rujukanbantaran_id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_063922_migrate_DSV_2158_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_063922_migrate_DSV_2158_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
