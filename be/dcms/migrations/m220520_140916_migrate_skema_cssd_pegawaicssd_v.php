<?php

use yii\db\Migration;

/**
 * Class m220520_140916_migrate_skema_cssd_pegawaicssd_v
 */
class m220520_140916_migrate_skema_cssd_pegawaicssd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pegawaicssd_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pegawaicssd_v\" AS
            SELECT ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
            FROM ((ruanganpegawai_mp
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id
            FROM pegawai_m a
            WHERE (a.kelompokpegawai_id = ANY (ARRAY[1, 2]))) pegawai_m ON ((ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id)))
            WHERE (ruanganpegawai_mp.ruangan_id = ( SELECT lookuptransaksi_m.kode_id
            FROM lookuptransaksi_m
            WHERE ((lookuptransaksi_m.kode_transaksi)::text = 'ruangan_cssd'::text)))
            ;");
        $this->execute('
            ALTER TABLE public.pegawaicssd_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220520_140916_migrate_skema_cssd_pegawaicssd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_140916_migrate_skema_cssd_pegawaicssd_v cannot be reverted.\n";

        return false;
    }
    */
}
