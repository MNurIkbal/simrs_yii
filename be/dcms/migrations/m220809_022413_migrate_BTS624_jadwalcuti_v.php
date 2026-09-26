<?php

use yii\db\Migration;

/**
 * Class m220809_022413_migrate_BTS624_jadwalcuti_v
 */
class m220809_022413_migrate_BTS624_jadwalcuti_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.jadwalcuti_v;');
        $this->execute("
            CREATE VIEW \"public\".\"jadwalcuti_v\" AS
            SELECT jadwalcuti_m.jadwalcuti_id,
            jadwalcuti_m.pegawai_id AS dokter_id,
            jadwalcuti_m.spesialis_id,
            jadwalcuti_m.ruangan_id,
            pegawai_m.nama_pegawai AS dokter_nama,
            spesialis_m.spesialis_nama,
            ruangan_m.ruangan_nama,
            jadwalcuti_m.tgl_cuti_awal,
            jadwalcuti_m.tgl_cuti_akhir,
            (((jadwalcuti_m.tgl_cuti_akhir)::date - (jadwalcuti_m.tgl_cuti_awal)::date) + 1) AS lama_cuti,
            pegawai_m.nomorindukpegawai,
            jadwalcuti_m.alasan_cuti
            FROM (((jadwalcuti_m
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.nomorindukpegawai
            FROM pegawai_m a) pegawai_m ON ((jadwalcuti_m.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
            FROM spesialis_m a) spesialis_m ON ((jadwalcuti_m.spesialis_id = spesialis_m.spesialis_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((jadwalcuti_m.ruangan_id = ruangan_m.ruangan_id)))
            WHERE ((jadwalcuti_m.is_active = true) AND (jadwalcuti_m.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.jadwalcuti_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_022413_migrate_BTS624_jadwalcuti_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_022413_migrate_BTS624_jadwalcuti_v cannot be reverted.\n";

        return false;
    }
    */
}
