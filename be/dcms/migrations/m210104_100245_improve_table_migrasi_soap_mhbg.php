<?php

use yii\db\Migration;

/**
 * Class m210104_100245_improve_table_migrasi_soap_mhbg
 */
class m210104_100245_improve_table_migrasi_soap_mhbg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TABLE IF EXISTS riwayatsoap_r;
        ');

        $this->execute('
            DROP SEQUENCE IF EXISTS riwayatsoap_r_riwayatsoap_id_seq;
        ');

        $this->execute('
            CREATE SEQUENCE "public"."riwayatsoap_r_riwayatsoap_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

        $this->execute('
            CREATE TABLE "public"."riwayatsoap_r" (
                "riwayatsoap_id" int4 NOT NULL DEFAULT nextval(\'riwayatsoap_r_riwayatsoap_id_seq\'::regclass),
                pendaftaranold_id int4 NOT NULL,
                tgl_pendaftaran timestamp(6),
                pasien_id int4,
                no_rekam_medik VARCHAR(20),
                nama_pasien VARCHAR(150),
                tipe_pendaftaran VARCHAR(5),
                tgl_soap timestamp(6),
                dokter_id int4,
                nama_dokter VARCHAR(100),
                soap TEXT,
                tgl_dirujuk timestamp(6),
                dokter_perujuk_id int4,
                dokter_perujuk_nama VARCHAR(100),
                resep text,
                is_racikan BOOLEAN,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_riwayatsoap_r" PRIMARY KEY ("riwayatsoap_id"));
        ');

        $this->execute('
            DROP TABLE IF EXISTS riwayathasipemeriksaanlab_r;
        ');

        $this->execute('
            DROP SEQUENCE IF EXISTS riwayathasipemeriksaanlab_r_riwayathasipemeriksaanlab_id_seq;
        ');

        $this->execute('
            CREATE SEQUENCE "public"."riwayathasipemeriksaanlab_r_riwayathasipemeriksaanlab_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

        $this->execute('
            CREATE TABLE "public"."riwayathasipemeriksaanlab_r" (
                "riwayathasipemeriksaanlab_id" int4 NOT NULL DEFAULT nextval(\'riwayathasipemeriksaanlab_r_riwayathasipemeriksaanlab_id_seq\'::regclass),
                pendaftaranold_id int4 NOT NULL,
                tgl_pendaftaran timestamp(6),
                pasien_id int4,
                no_rekam_medik VARCHAR(20),
                nama_pasien VARCHAR(150),
                tipe_pendaftaran VARCHAR(5),
                dokter_id int4,
                nama_dokter VARCHAR(100),
                dokter_perujuk_id int4,
                dokter_perujuk_nama VARCHAR(100),
                tgl_order timestamp(6),
                tgl_hasil timestamp(6),
                keterangan_klinis TEXT,
                kelompokpemeriksaanlab_id int4,
                kelompokpemeriksaanlab_nama VARCHAR(100),
                pemeriksaanlab_id int4,
                pemeriksaanlab_nama VARCHAR(100),
                hasil TEXT,
                nilai_rujukan TEXT,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_riwayathasipemeriksaanlab_r" PRIMARY KEY ("riwayathasipemeriksaanlab_id")
            );
        ');

        $this->execute('
            DROP TABLE IF EXISTS riwayathasipemeriksaanrad_r;
        ');

        $this->execute('
            DROP SEQUENCE IF EXISTS riwayathasipemeriksaanrad_r_riwayathasipemeriksaanrad_id_seq;
        ');

        $this->execute('
            CREATE SEQUENCE "public"."riwayathasipemeriksaanrad_r_riwayathasipemeriksaanrad_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

        $this->execute('
            CREATE TABLE "public"."riwayathasipemeriksaanrad_r" (
                "riwayathasipemeriksaanrad_id" int4 NOT NULL DEFAULT nextval(\'riwayathasipemeriksaanrad_r_riwayathasipemeriksaanrad_id_seq\'::regclass),
                pendaftaranold_id int4 NOT NULL,
                tgl_pendaftaran timestamp(6),
                pasien_id int4,
                no_rekam_medik VARCHAR(20),
                nama_pasien VARCHAR(150),
                tipe_pendaftaran VARCHAR(5),
                dokter_id int4,
                nama_dokter VARCHAR(100),
                dokter_perujuk_id int4,
                dokter_perujuk_nama VARCHAR(100),
                tgl_order timestamp(6),
                tgl_hasil timestamp(6),
                keterangan_klinis TEXT,
                pemeriksaanrad_id int4,
                pemeriksaanrad_nama VARCHAR(100),
                hasil_expertise TEXT,
                kesan TEXT,
                kesimpulan TEXT,
                dokter_perujuk_baru_id int4,
                dokter_perujuk_baru_nama VARCHAR(100),
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_riwayathasipemeriksaanrad_r" PRIMARY KEY ("riwayathasipemeriksaanrad_id")
        );
        ');

        $this->execute('
            DROP TABLE IF EXISTS riwayatpendaftaran_r;
        ');

        $this->execute('
            DROP SEQUENCE IF EXISTS riwayatpendaftaran_r_riwayatpendaftaran_id_seq;
        ');

        $this->execute('
            CREATE SEQUENCE "public"."riwayatpendaftaran_r_riwayatpendaftaran_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

        $this->execute('
            CREATE TABLE "public"."riwayatpendaftaran_r" (
                "riwayatpendaftaran_id" int4 NOT NULL DEFAULT nextval(\'riwayatpendaftaran_r_riwayatpendaftaran_id_seq\'::regclass),
                pendaftaranold_id int4 NOT NULL,
                tgl_pendaftaran timestamp(6),
                pasien_id int4,
                no_rekam_medik VARCHAR(20),
                nama_pasien VARCHAR(150),
                tipe_pendaftaran VARCHAR(5),
                instalasi_id int4,
                instalasi_nama VARCHAR(100),
                ruangan_id int4,
                ruangan_nama VARCHAR(100),
                kelaspelayanan_id int4,
                kelaspelayanan_nama VARCHAR(100),
                dokter_id int4,
                nama_dokter VARCHAR(100),
                dokter_ranap_id int4,
                nama_dokter_ranap VARCHAR(100),
                keterangan_pulang VARCHAR(100),
                keterangan TEXT,
                asal_rujukan VARCHAR(100),
                alamat_rujukan VARCHAR(100),
                penjamin_id int4,
                nama_penjamin VARCHAR(100),
                kamar VARCHAR(30),
                no_tempattidur VARCHAR(30),
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_riwayatpendaftaran_r" PRIMARY KEY ("riwayatpendaftaran_id")
            );
        ');

        $this->execute('
            DROP TABLE IF EXISTS riwayatresep_r;
        ');

        $this->execute('
            DROP SEQUENCE IF EXISTS riwayatresep_r_riwayatresep_id_seq;
        ');

        $this->execute('
            CREATE SEQUENCE "public"."riwayatresep_r_riwayatresep_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        ');

        $this->execute('
            CREATE TABLE "public"."riwayatresep_r" (
                "riwayatresep_id" int4 NOT NULL DEFAULT nextval(\'riwayatresep_r_riwayatresep_id_seq\'::regclass),
                pendaftaranold_id int4 NOT NULL,
                tgl_pendaftaran timestamp(6),
                pasien_id int4,
                no_rekam_medik VARCHAR(20),
                nama_pasien VARCHAR(150),
                tipe_pendaftaran VARCHAR(5),
                resep_id int4,
                resepdetail_id int4,
                obatalkes_kode VARCHAR(30),
                obatalkes_nama VARCHAR(100),
                satuan_kode VARCHAR(20),
                satuan_nama VARCHAR(20),
                qty float8,
                harga_netto float8,
                tuslah float8,
                total_harga float8,
                embalase float8,
                tgl_kadaluarsa timestamp(6),
                supplier_kode VARCHAR(20),
                supplier_nama VARCHAR(20),
                signa1 TEXT,
                signa2 TEXT,
                signa_tambahan TEXT,
                is_farmasi BOOLEAN,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "pk_riwayatresep_r" PRIMARY KEY ("riwayatresep_id")
        );
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210104_100245_improve_table_migrasi_soap_mhbg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210104_100245_improve_table_migrasi_soap_mhbg cannot be reverted.\n";

        return false;
    }
    */
}
