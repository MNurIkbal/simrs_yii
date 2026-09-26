<?php

use yii\db\Migration;

/**
 * Class m211116_114244_migrate_US1942_mobilejkn_ambilantrian
 */
class m211116_114244_migrate_US1942_mobilejkn_ambilantrian extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."antrianjkn_r" (
          "antrianjkn_id" serial4,
          "pendaftaranol_id" int4,
          "antrian_id" int4,
          "tanggal_periksa" timestamp(0),
          "nomorkartu" varchar(50) COLLATE "pg_catalog"."default",
          "jenis_cara_bayar" int4,
          "jeniskunjungan" int4,
          "nomorreferensi" varchar(100) COLLATE "pg_catalog"."default",
          "keterangan" varchar(100) COLLATE "pg_catalog"."default",
          "is_checkin" bool DEFAULT false,
          "is_selesai_periksa" bool DEFAULT false,
          "is_batal_periksa" bool DEFAULT false,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool DEFAULT false,
          "is_active" bool DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "no_rekam_medik" varchar(100) COLLATE "pg_catalog"."default",
          CONSTRAINT "antrianjkn_r_pkey" PRIMARY KEY ("antrianjkn_id")
          );
        ');

        $this->execute('ALTER TABLE "public"."antrianjkn_r" 
          OWNER TO "postgres";
        ');

        $this->execute('COMMENT ON COLUMN "public"."antrianjkn_r"."jenis_cara_bayar" IS \'pakai lookup_type jenispasienjkn\';
        ');

        $this->execute('COMMENT ON COLUMN "public"."antrianjkn_r"."jeniskunjungan" IS \'pakai lookup_type jeniskunjungan\';
        ');

        $this->execute("
            DELETE from lookup_m where lookup_id IN (1096,1097,1098,1099,1100,1101,1102);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1096, 'jeniskunjungan', '1', 'Rujukan FKT', 1, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1097, 'jeniskunjungan', '2', 'Rujukan Internal', 2, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1098, 'jeniskunjungan', '3', 'Kontrol', 3, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1099, 'jeniskunjungan', '4', 'Rujukan Antar RS', 4, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1100, 'jenispasienjkn', 'JKN', 'JKN', NULL, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1101, 'jenispasienjkn', 'NON JKN', 'NON JKN', NULL, NULL, NULL, '2021-11-01 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1102, 'jenis_reservasi', 'Mobile JKN', 'Mobile JKN', NULL, NULL, NULL, '2021-11-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute('DROP VIEW if exists public.antrianjkn_v;');
        $this->execute("
            CREATE VIEW \"public\".\"antrianjkn_v\" AS
            SELECT pendaftaranol_t.pendaftaranol_id,
            antrian_t.pendaftaran_id,
            pendaftaranol_t.no_pendaftaranol AS kodebooking,
            antrianjkn_r.jenis_cara_bayar,
            fgetnamalookup(antrianjkn_r.jenis_cara_bayar) AS jenispasien,
            antrianjkn_r.nomorkartu,
            pendaftaranol_t.jenisidentitas,
            fgetnamalookup((pendaftaranol_t.jenisidentitas)::integer) AS jenisidentitas_nama,
            pendaftaranol_t.no_identitas_pasien,
            pendaftaranol_t.no_telepon_pasien,
            ruangan_m.kode_ruangan_bpjs AS kodepoli,
            ruangan_m.ruangan_nama AS namapoli,
            CASE
            WHEN (pendaftaranol_t.status_pasien = 311) THEN 0
            ELSE 1
            END AS status_pasien,
            fgetnamalookup(pendaftaranol_t.status_pasien) AS status_pasien_nama,
            antrianjkn_r.no_rekam_medik,
            antrianjkn_r.tanggal_periksa,
            pegawai_m.kode_dokter_bpjs AS kodedokter,
            pegawai_m.nama_pegawai AS namadokter,
            concat(to_char((jadwaldokter_m.jadwaldokter_mulai)::interval, 'HH24:MI'::text), ' - ', to_char((jadwaldokter_m.jadwaldokter_tutup)::interval, 'HH24:MI'::text)) AS jampraktek,
            fgetnamalookup(antrianjkn_r.jeniskunjungan) AS jeniskunjungan,
            antrianjkn_r.nomorreferensi,
            antrian_t.no_antrian AS nomorantrean,
            antrian_t.temp_urutan AS angkaantrean,
            jadwaldokter_m.kuota_bpjs_online AS kuotajkn,
            jadwaldokter_m.kuota_nonbpjs_online AS kuotanonjkn,
            kuotadokter_r.kuota_bpjs_online AS sisakuotajkn,
            kuotadokter_r.kuota_nonbpjs_online AS sisakuotanonjkn,
            antrianjkn_r.keterangan,
            jadwaldokter_m.jumlah_loaddokter AS estimasidilayani,
            antrian_t.antrian_id,
            jadwaldokter_m.jadwaldokter_id
            FROM ((((((pendaftaranol_t
            JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
            JOIN jadwaldokter_m ON ((antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id)))
            JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
            JOIN kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND (kuotadokter_r.is_online = true))))
            JOIN antrianjkn_r ON ((pendaftaranol_t.pendaftaranol_id = antrianjkn_r.pendaftaranol_id)))
        ;");
        $this->execute('
            ALTER TABLE public.antrianjkn_v OWNER TO postgres;
        ');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"antrianjkn_r_ins\"()
            RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
            DECLARE
            dataAntrianJkn VARCHAR;
            paramJson VARCHAR;

            BEGIN
            paramJson := NEW.additional_data; 
            dataAntrianJkn := paramJson::json->>'jkn';

            IF(new.jenis_reservasi = 1102) THEN
            INSERT INTO antrianjkn_r (
            pendaftaranol_id,
            antrian_id,
            tanggal_periksa,
            nomorkartu,
            jenis_cara_bayar,
            jeniskunjungan,
            nomorreferensi,
            keterangan,
            no_rekam_medik
            ) VALUES (
            new.pendaftaranol_id,
            new.antrian_id,
            new.tgl_pendaftaranol,
            (dataAntrianJkn::json->>'nomorkartu')::VARCHAR,
            (dataAntrianJkn::json->>'jenis_cara_bayar')::INTEGER,
            (dataAntrianJkn::json->>'jeniskunjungan')::INTEGER,
            (dataAntrianJkn::json->>'nomorreferensi')::VARCHAR,
            (dataAntrianJkn::json->>'keterangan')::VARCHAR,
            (dataAntrianJkn::json->>'no_rekam_medik')::VARCHAR
            );
            END IF;
            RETURN NEW;
            END \$BODY\$
            LANGUAGE plpgsql VOLATILE
            COST 100
        ");

        $this->execute("CREATE TRIGGER \"ins_antrianJkn\" AFTER INSERT ON \"public\".\"pendaftaranol_t\"
            FOR EACH ROW
            EXECUTE PROCEDURE \"public\".\"antrianjkn_r_ins\"();
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_114244_migrate_US1942_mobilejkn_ambilantrian cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_114244_migrate_US1942_mobilejkn_ambilantrian cannot be reverted.\n";

        return false;
    }
    */
}
