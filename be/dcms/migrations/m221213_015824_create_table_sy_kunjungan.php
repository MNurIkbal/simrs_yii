<?php

use yii\db\Migration;

/**
 * Class m230304_015824_create_table_sy_kunjungan
 */
class m221213_015824_create_table_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS public.sy_kunjungan (
            kunjungan_id bigserial NOT NULL,
            no_pendaftaran varchar(150) NULL,
            no_rekammedik varchar(150) NULL,
            nama_pasien varchar(255) NULL,
            jenis_kelamin varchar(100) NULL,
            tgl_lahir date NULL,
            umur varchar(100) NULL,
            tgl_pendaftaran timestamp(0) NULL,
            tgl_pulang timestamp(0) NULL,
            instalasi_kode varchar(50) NULL,
            instalasi_nama varchar(100) NULL,
            ruangan_kode varchar(50) NULL,
            ruangan_nama varchar(100) NULL,
            carabayar_kode varchar(50) NULL,
            carabayar_nama varchar(100) NULL,
            penjamin_kode varchar(50) NULL,
            penjamin_nama varchar(100) NULL,
            kelas_kode varchar(50) NULL,
            kelas_nama varchar(100) NULL,
            dokter_kode varchar(50) NULL,
            dokter_nama varchar(100) NULL,
            no_sep varchar(150) NULL,
            status_kunjungan int2 NULL,
            no_kamar varchar(100) NULL,
            no_tempattidur varchar(100) NULL,
            hak_kelasbpjs int2 NULL,
            carakeluar_kode varchar(50) NULL,
            lama_rawat varchar(50) NULL,
            no_asuransi varchar(150) NULL,
            is_verifikasi bool NULL DEFAULT false,
            total_verifikasi float8 NULL,
            tgl_verifikasi date NULL,
            identitas_id int4 NULL,
            identitas_nama varchar(100) NULL,
            identitas_value varchar(100) NULL,
            no_klaimcovid varchar(150) NULL,
            pasien_id int4 NULL,
            additional_data text NULL,
            created_date timestamp(6) NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NULL DEFAULT false,
            is_active bool NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            nosep varchar(100) NULL,
            CONSTRAINT sy_kunjungan_pkey PRIMARY KEY (kunjungan_id)
        ); 
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_015824_create_table_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_015824_create_table_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
