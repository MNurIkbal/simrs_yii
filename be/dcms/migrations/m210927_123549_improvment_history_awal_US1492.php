<?php

use yii\db\Migration;

/**
 * Class m210927_123549_improvment_history_awal_US1492
 */
class m210927_123549_improvment_history_awal_US1492 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."asesmenawal_r" (
                "history_asesmenawal_id" serial8 NOT NULL PRIMARY KEY,
                "asesmenawal_id" int4,
                "pendaftaran_id" int4 NOT NULL,
                "pasienadmisi_id" int4,
                "waktu_tiba" timestamp(6),
                "asal_masuk" varchar(100) COLLATE "pg_catalog"."default",
                "tgl_asesmen" timestamp(6),
                "asesmen_diambildari" varchar(100) COLLATE "pg_catalog"."default",
                "diambildari_nama" varchar(255) COLLATE "pg_catalog"."default",
                "diambildari_hub" varchar(255) COLLATE "pg_catalog"."default",
                "masuk_dengan" varchar(100) COLLATE "pg_catalog"."default",
                "masuk_denganlain" varchar(255) COLLATE "pg_catalog"."default",
                "obat_darirumah" bool,
                "obatan_rumah" text COLLATE "pg_catalog"."default",
                "hasil_pemeriksaan" bool,
                "hasil_rad" text COLLATE "pg_catalog"."default",
                "hasil_lab" text COLLATE "pg_catalog"."default",
                "hasil_lainnya" text COLLATE "pg_catalog"."default",
                "keluhan_utama" text COLLATE "pg_catalog"."default",
                "diagnosa_masuk" json,
                "r_kehamilan_g" int4,
                "r_kehamilan_p" int4,
                "r_kehamilan_a" int4,
                "hpht" timestamp(6),
                "haid_teratur" bool,
                "ketergantungan" varchar(255) COLLATE "pg_catalog"."default",
                "r_penyakit_kel" varchar(255) COLLATE "pg_catalog"."default",
                "penyakit_kel_lain" varchar(255) COLLATE "pg_catalog"."default",
                "r_kes_sekarang" text COLLATE "pg_catalog"."default",
                "pernah_dirawat" bool,
                "tgl_dirawat" timestamp(6),
                "alasan_dirawat" varchar(255) COLLATE "pg_catalog"."default",
                "pernah_tindakan" bool,
                "tgl_tindakan" timestamp(6),
                "alasan_tindakan" varchar(255) COLLATE "pg_catalog"."default",
                "jeniskegiatantindakan_id" int4,
                "r_alergi" bool,
                "nama_alergi" varchar(255) COLLATE "pg_catalog"."default",
                "transfusi" bool,
                "reaksi" varchar(255) COLLATE "pg_catalog"."default",
                "asmen_riwayat" text COLLATE "pg_catalog"."default",
                "asmen_periksafisik" text COLLATE "pg_catalog"."default",
                "asmen_keb_dasar" text COLLATE "pg_catalog"."default",
                "asmen_sk_fungsional" text COLLATE "pg_catalog"."default",
                "asmen_sk_gizi" text COLLATE "pg_catalog"."default",
                "asmen_keb_pendidikan" text COLLATE "pg_catalog"."default",
                "asmen_masalahkes" text COLLATE "pg_catalog"."default",
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4
                );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "asesmenawal_history_asesmen_id_idx" ON "public"."asesmenawal_r" USING btree (
            "asesmenawal_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "asesmenawal_history_pasienadmisi_id_idx" ON "public"."asesmenawal_r" USING btree (
            "pasienadmisi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "asesmenawal_history_pendaftaran_id_idx" ON "public"."asesmenawal_r" USING btree (
            "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."asesmenawal_r_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                    
            BEGIN
                INSERT INTO asesmenawal_r(
                    "asesmenawal_id", 
                    "pendaftaran_id", 
                    "pasienadmisi_id", 
                    "waktu_tiba", 
                    "asal_masuk", 
                    "tgl_asesmen", 
                    "asesmen_diambildari", 
                    "diambildari_nama", 
                    "diambildari_hub", 
                    "masuk_dengan", 
                    "masuk_denganlain", 
                    "obat_darirumah", 
                    "obatan_rumah", 
                    "hasil_pemeriksaan", 
                    "hasil_rad", 
                    "hasil_lab", 
                    "hasil_lainnya", 
                    "keluhan_utama", 
                    "diagnosa_masuk", 
                    "r_kehamilan_g", 
                    "r_kehamilan_p", 
                    "r_kehamilan_a", 
                    "hpht", 
                    "haid_teratur", 
                    "ketergantungan", 
                    "r_penyakit_kel", 
                    "penyakit_kel_lain", 
                    "r_kes_sekarang", 
                    "pernah_dirawat", 
                    "tgl_dirawat", 
                    "alasan_dirawat", 
                    "pernah_tindakan", 
                    "tgl_tindakan", 
                    "alasan_tindakan", 
                    "jeniskegiatantindakan_id", 
                    "r_alergi", 
                    "nama_alergi", 
                    "transfusi", 
                    "reaksi", 
                    "asmen_riwayat", 
                    "asmen_periksafisik", 
                    "asmen_keb_dasar", 
                    "asmen_sk_fungsional", 
                    "asmen_sk_gizi", 
                    "asmen_keb_pendidikan", 
                    "asmen_masalahkes", 
                    "additional_data", 
                    "created_date", 
                    "created_by", 
                    "modified_count", 
                    "last_modified_date", 
                    "last_modified_by", 
                    "is_deleted", 
                    "is_active", 
                    "deleted_date", 
                    "deleted_by"
                ) VALUES (
                    NEW.asesmenawal_id, 
                    NEW.pendaftaran_id, 
                    NEW.pasienadmisi_id, 
                    NEW.waktu_tiba, 
                    NEW.asal_masuk, 
                    NEW.tgl_asesmen, 
                    NEW.asesmen_diambildari, 
                    NEW.diambildari_nama, 
                    NEW.diambildari_hub, 
                    NEW.masuk_dengan, 
                    NEW.masuk_denganlain, 
                    NEW.obat_darirumah, 
                    NEW.obatan_rumah, 
                    NEW.hasil_pemeriksaan, 
                    NEW.hasil_rad, 
                    NEW.hasil_lab, 
                    NEW.hasil_lainnya, 
                    NEW.keluhan_utama, 
                    NEW.diagnosa_masuk, 
                    NEW.r_kehamilan_g, 
                    NEW.r_kehamilan_p, 
                    NEW.r_kehamilan_a, 
                    NEW.hpht, 
                    NEW.haid_teratur, 
                    NEW.ketergantungan, 
                    NEW.r_penyakit_kel, 
                    NEW.penyakit_kel_lain, 
                    NEW.r_kes_sekarang, 
                    NEW.pernah_dirawat, 
                    NEW.tgl_dirawat, 
                    NEW.alasan_dirawat, 
                    NEW.pernah_tindakan, 
                    NEW.tgl_tindakan, 
                    NEW.alasan_tindakan, 
                    NEW.jeniskegiatantindakan_id, 
                    NEW.r_alergi, 
                    NEW.nama_alergi, 
                    NEW.transfusi, 
                    NEW.reaksi, 
                    NEW.asmen_riwayat, 
                    NEW.asmen_periksafisik, 
                    NEW.asmen_keb_dasar, 
                    NEW.asmen_sk_fungsional, 
                    NEW.asmen_sk_gizi, 
                    NEW.asmen_keb_pendidikan, 
                    NEW.asmen_masalahkes, 
                    NEW.additional_data, 
                    NEW.created_date, 
                    NEW.created_by, 
                    NEW.modified_count, 
                    NEW.last_modified_date, 
                    NEW.last_modified_by, 
                    NEW.is_deleted, 
                    NEW.is_active, 
                    NEW.deleted_date, 
                    NEW.deleted_by
                ); 
                    
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "insert_history_assesmen_awal" ON "public"."asesmenawal_t";
        ');

        $this->execute('
            CREATE TRIGGER "insert_history_assesmen_awal" AFTER INSERT OR UPDATE ON "public"."asesmenawal_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."asesmenawal_r_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210927_123549_improvment_history_awal_US1492 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210927_123549_improvment_history_awal_US1492 cannot be reverted.\n";

        return false;
    }
    */
}
