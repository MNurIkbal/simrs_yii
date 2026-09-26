<?php

use yii\db\Migration;

/**
 * Class m211222_092549_migrate_US2561_smh_slotduration
 */
class m211222_092549_migrate_US2561_smh_slotduration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" 
          ADD COLUMN IF NOT EXISTS "spesialisruangan_id" int4;
        ');

        $this->execute('COMMENT ON COLUMN "public"."jadwaldokter_m"."spesialisruangan_id" IS \'ambil dari spesialisruangan_mp\';
        ');

        $this->execute('DROP VIEW if exists public.infojadwaldokter_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infojadwaldokter_v\" AS
            SELECT jadwaldokter_m.jadwaldokter_id,
            jadwaldokter_m.ruangan_id,
            jadwaldokter_m.instalasi_id,
            jadwaldokter_m.pegawai_id,
            ruangan_m.ruangan_nama,
            pegawai_m.nama_pegawai,
            jadwaldokter_m.jadwaldokter_hari,
            concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
            jadwaldokter_m.maximumantrian AS kuota,
            jadwaldokter_m.jadwaldokter_mulai AS waktu_mulai,
            jadwaldokter_m.jadwaldokter_tutup AS waktu_selesai,
            jadwaldoktertambahan_m.kuota_penambahan,
            ((jadwaldokter_m.maximumantrian)::double precision + (jadwaldoktertambahan_m.kuota_penambahan)::double precision) AS total_kuota,
            fgetnamalookup(jadwalbukapoli_m.hari) AS hari,
            jadwalbukapoli_m.hari AS hari_jadwalbuka,
            jadwaldokter_m.kuota_online,
            jadwaldokter_m.jadwaldokter_tgl,
            pegawai_m.dokter_id,
            ruangan_m.poliklinik_id,
            CASE
            WHEN (jadwalbukapoli_m.shift_id IS NULL) THEN 0
            ELSE jadwalbukapoli_m.shift_id
            END AS shift_id,
            shift_m.shift1_id,
            jadwaldokter_m.notifikasi_id,
            notifikasi_m.judul_temp,
            notifikasi_m.notifikasi,
            COALESCE(kuotadokter_r.kuota_tersedia, (0)::real) AS kuota_tersedia,
            jadwaldokter_m.is_active,
            kuotadokter_r.kuotadokter_id,
            jadwaldokter_m.jadwalbukapoli_id,
            jadwaldokter_m.kuota_total,
            pegawai_m.kode_dokter_bpjs,
            ruangan_m.kode_ruangan_bpjs,
            jadwaldokter_m.kuota_bpjs_online,
            jadwaldokter_m.kuota_nonbpjs_online,
            jadwaldokter_m.is_bersedia,
            jadwaldokter_m.is_loaddokter,
            jadwaldokter_m.spesialisruangan_id,
            jadwaldokter_m.jumlah_loaddokter
            FROM ((((((((jadwaldokter_m
            JOIN ruangan_m ON ((jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m ON ((jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN jadwaldoktertambahan_m ON ((jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id)))
            JOIN jadwalbukapoli_m ON (((jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id) AND (jadwalbukapoli_m.is_deleted = false))))
            LEFT JOIN shift_m ON ((jadwalbukapoli_m.shift_id = shift_m.shift_id)))
            LEFT JOIN notifikasi_m ON ((jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id)))
            LEFT JOIN kuotadokter_r ON (((jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id) AND kuotadokter_r.is_online)))
            WHERE ((jadwaldokter_m.is_deleted = false) AND (jadwaldokter_m.is_active = true) AND (pegawai_m.is_deleted = false) AND (pegawai_m.is_active = true))
            ;");
        $this->execute('
            ALTER TABLE public.infojadwaldokter_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211222_092549_migrate_US2561_smh_slotduration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_092549_migrate_US2561_smh_slotduration cannot be reverted.\n";

        return false;
    }
    */
}
