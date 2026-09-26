<?php

use yii\db\Migration;

/**
 * Class m230306_075334_create_table_sy_klaim
 */
class m230306_075334_create_table_sy_klaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        
        CREATE TABLE IF NOT EXISTS public.sy_koreksidiagnosa (
            sy_koreksidiagnosa_id serial4 NOT NULL,
            kunjungan_id int4 NOT NULL,
            tgl_koreksidiagnosa timestamp(6) NULL,
            kelompokdiagnosa_id int4 NOT NULL,
            diagnosa_id int4 NOT NULL,
            diagnosaasal_id int4 NULL,
            diag_asal_masuk text NULL,
            diag_asal_utama text NULL,
            diag_asal_penyerta text NULL,
            diag_asal_terapi text NULL,
            is_inacbg bool NULL,
            is_icdprimer bool NULL,
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
            is_diagnosa_baru bool NULL,
            CONSTRAINT koreksidiagnosa_t_copy1_pkey PRIMARY KEY (sy_koreksidiagnosa_id)
        );
        ");

        $this->execute("
        CREATE INDEX koreksidiagnosa_pendaftaran_id_idx_copy1 ON public.sy_koreksidiagnosa USING btree (kunjungan_id);
        ");

        $this->execute("
        
        CREATE TABLE IF NOT EXISTS public.sy_klaimgroup_t (
            sy_klaimgroup_id serial4 NOT NULL,
            sy_klaiminacbg_id int4 NULL,
            group_nama varchar(255) NULL,
            spesial_procedure varchar(255) NULL,
            spesial_prosthesis varchar(255) NULL,
            spesial_investigation varchar(255) NULL,
            spesial_drug varchar(255) NULL,
            total float8 NULL DEFAULT 0,
            tambahan_biaya float8 NULL DEFAULT 0,
            persen_tambahan float8 NULL,
            total_naikkelas float8 NULL,
            total_kelaspelayanan float8 NULL,
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
            add_jenazah text NULL,
            cbg text NULL,
            special_group text NULL,
            group_tarif float8 NULL,
            sp_procedure_kode varchar(100) NULL,
            sp_prosthesis_kode varchar(100) NULL,
            sp_investigation_kode varchar(100) NULL,
            sp_drug_kode varchar(100) NULL,
            sp_procedure_nama varchar(255) NULL,
            sp_prosthesis_nama varchar(255) NULL,
            sp_investigation_nama varchar(255) NULL,
            sp_drug_nama varchar(255) NULL,
            add_episode text NULL,
            CONSTRAINT sy_klaimgroup_t_pkey PRIMARY KEY (sy_klaimgroup_id)
        );
        ");

        $this->execute("
        
        CREATE TABLE IF NOT EXISTS public.sy_klaiminacbg (
            sy_klaiminacbg_id serial4 NOT NULL,
            klaimgroup_id int4 NULL,
            kunjungan_id int4 NOT NULL,
            los int4 NULL,
            adl_subacute varchar(100) NULL,
            adl_cronic varchar(100) NULL,
            total_tarifrs float8 NULL,
            jenis_kelasrawat varchar(100) NULL,
            tarif varchar(100) NULL,
            prosedur_bedah float8 NULL,
            prosedur_nonbedah float8 NULL,
            konsultasi float8 NULL,
            tenaga_ahli float8 NULL,
            keperawatan float8 NULL,
            penunjang float8 NULL,
            radiologi float8 NULL,
            laboratorium float8 NULL,
            pelayanan_darah float8 NULL,
            rehabilitasi float8 NULL,
            kamar_akomodasi float8 NULL,
            rawat_intensif float8 NULL,
            obat float8 NULL,
            alkes float8 NULL,
            bmhp float8 NULL,
            sewa_alat float8 NULL,
            diagnosa_primer text NULL,
            diagnosa_sekunder text NULL,
            no_peserta varchar(255) NULL,
            status_klaim bool NULL DEFAULT false,
            naik_kelas varchar(100) NULL,
            is_naikkelas bool NULL,
            lama_naikkelas int4 NULL,
            is_kelasintensif bool NULL,
            kelas_intensif varchar(100) NULL,
            lama_kelasintensif int4 NULL,
            ventilator int4 NULL,
            tarif_polieksekutif float4 NULL,
            is_rawatintensif bool NULL,
            lama_rawatintensif int2 NULL,
            is_terkirim bool NULL DEFAULT false,
            obat_kronis float8 NULL,
            obat_kemoterapi varchar(52) NULL,
            is_turunkelas bool NOT NULL DEFAULT false,
            klaim_penjamin int2 NULL,
            status_covid varchar(100) NULL,
            is_komplikasi bool NOT NULL DEFAULT false,
            is_pemulasaranjenazah bool NOT NULL DEFAULT false,
            is_kantongjenazah bool NOT NULL DEFAULT false,
            is_petijenazah bool NOT NULL DEFAULT false,
            is_plastikerat bool NOT NULL DEFAULT false,
            is_desinfektanjenazah bool NOT NULL DEFAULT false,
            is_transport bool NOT NULL DEFAULT false,
            is_desinfektanmobil bool NOT NULL DEFAULT false,
            total_episodedijamin int2 NULL,
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
            CONSTRAINT sy_klaiminacbg_pkey PRIMARY KEY (sy_klaiminacbg_id)
        );
        ");

        $this->execute("

        CREATE TABLE IF NOT EXISTS public.sy_klaiminacbgdetail (
            sy_klaiminacbgdetail_id serial4 NOT NULL,
            sy_klaiminacbg_id int4 NOT NULL,
            diagnosa_id int4 NULL,
            kode_diagnosa varchar(255) NULL,
            nama_diagnosa varchar(255) NULL,
            icd_versi varchar(100) NULL,
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
            CONSTRAINT sy_klaiminacbgdetail_pkey PRIMARY KEY (sy_klaiminacbgdetail_id)
        );
        ");

        $this->execute("
        CREATE TABLE IF NOT EXISTS public.sy_kunjungandetail (
            kunjungandetail_id bigserial NOT NULL,
            kunjungan_id int4 NOT NULL,
            no_pendaftaran varchar(150) NULL,
            no_rekammedik varchar(150) NULL,
            kelompok_diagnosa varchar(25) NULL,
            diagnosa_kode varchar(50) NULL,
            diagnosa_nama varchar(255) NULL,
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
            CONSTRAINT sy_kunjungandetail_pkey PRIMARY KEY (kunjungandetail_id)
        );
        ");

        $this->execute("
        CREATE TABLE IF NOT EXISTS public.sy_kunjungantagihan (
            kunjungantagihan_id bigserial NOT NULL,
            kunjungan_id int4 NULL,
            no_pendaftaran varchar(100) NULL,
            no_rekammedik varchar(100) NULL,
            layanan_kode varchar(150) NULL,
            layanan_nama varchar(150) NULL,
            layanan_qty int4 NULL,
            layanan_tarif numeric(10, 2) NULL,
            tindakan_kode varchar(150) NULL,
            additional_data text NULL,
            created_date timestamp(6) NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NULL DEFAULT false,
            is_active bool NULL DEFAULT false,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            ruangan_kode varchar(100) NULL,
            ruangan_nama varchar(100) NULL,
            jasa_rs numeric(10, 2) NULL,
            jasa_dokter numeric(10, 2) NULL,
            dokter_kode varchar(50) NULL,
            dokter_nama varchar(150) NULL,
            kode_nota varchar(100) NULL,
            kel_report varchar(100) NULL,
            tarifrs_akt numeric(10, 2) NULL,
            no_buktitrans varchar(150) NULL,
            tgl_pendaftaran timestamp(0) NULL,
            total_adjust numeric(10, 2) NULL,
            kode_adjust varchar(100) NULL,
            status_bayar varchar(10) NULL,
            kelas_kode varchar(100) NULL,
            groupinacbg_id varchar(20) NULL,
            groupinacbg_nama varchar(150) NULL,
            groupinacbg_kode varchar(20) NULL,
            CONSTRAINT sy_kunjungantagihan_pkey PRIMARY KEY (kunjungantagihan_id)
        );
        ");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_075334_create_table_sy_klaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_075334_create_table_sy_klaim cannot be reverted.\n";

        return false;
    }
    */
}
