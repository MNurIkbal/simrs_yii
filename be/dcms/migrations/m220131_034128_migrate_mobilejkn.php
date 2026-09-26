<?php

use yii\db\Migration;

/**
 * Class m220131_034128_migrate_mobilejkn
 */
class m220131_034128_migrate_mobilejkn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."ambilantrian_r" (
          "ambilantrian_id" serial4,
          "pendaftaranol_id" int4,
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
          CONSTRAINT "ambilantrian_r_pkey" PRIMARY KEY ("ambilantrian_id")
      )
      ;
        ');

        $this->execute('ALTER TABLE "public"."ambilantrian_r" 
          OWNER TO "postgres";
        ');

        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
            ADD COLUMN IF NOT EXISTS "additional_jkn" text COLLATE "pg_catalog"."default";
          ');


        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "expired_time" int4;
          ');


        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."expired_time" IS \'satuan menit\';
          ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."spesialisruangan_mp" (
          "spesialisruangan_id" serial4,
          "spesialis_id" int4,
          "ruangan_id" int4,
          "is_default" bool,
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
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."spesialisruangan_mp" 
          OWNER TO "postgres";
          ');

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
            concat(to_char((jadwaldokter_m.jadwaldokter_mulai)::interval, 'HH24:MI'::text), '-', to_char((jadwaldokter_m.jadwaldokter_tutup)::interval, 'HH24:MI'::text)) AS jampraktek,
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
            jadwaldokter_m.jadwaldokter_id,
            slotjadwaldokter_m.jam_mulai,
            slotjadwaldokter_m.jam_selesai
            FROM (((((((pendaftaranol_t
            JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
            JOIN jadwaldokter_m ON ((antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id)))
            JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
            JOIN kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND (kuotadokter_r.is_online = true))))
            JOIN antrianjkn_r ON ((pendaftaranol_t.pendaftaranol_id = antrianjkn_r.pendaftaranol_id)))
            JOIN slotjadwaldokter_m ON (((antrian_t.jadwaldokter_id = slotjadwaldokter_m.jadwaldokter_id) AND (antrian_t.slot_sequence = slotjadwaldokter_m.slot_sequence))))
            ;");
        $this->execute('
            ALTER TABLE public.antrianjkn_v OWNER TO postgres;
            ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220131_034128_migrate_mobilejkn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220131_034128_migrate_mobilejkn cannot be reverted.\n";

        return false;
    }
    */
}
