<?php

use yii\db\Migration;

/**
 * Class m220624_164411_migrate_cssd_infopengajuansterilisasidet_v
 */
class m220624_164411_migrate_cssd_infopengajuansterilisasidet_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopengajuansterilisasidet_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuansterilisasidet_v\" AS
            SELECT 'ALKES'::text AS tipe,
            cssd_t.cssd_id,
            cssddet_t.cssddet_id,
            cssd_t.ruanganasal_id,
            ruangan_asal.ruangan_nama AS ruanganasal_nama,
            cssd_t.ruangantujuan_id,
            ruangan_tujuan.ruangan_nama AS ruangantujuan_nama,
            cssd_t.tgl_pengajuan_sterilisasi,
            cssd_t.no_pengajuan_sterilisasi,
            cssd_t.status_cssd,
            look_status_cssd.lookup_name AS status_cssd_nama,
            cssd_t.no_proses_sterilisasi,
            cssd_t.tgl_terima,
            cssd_t.peg_mengetahui_id,
            peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
            cssd_t.peg_menyetujui_id,
            peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
            cssddet_t.barangalkes_id,
            cssddet_t.barangalkes_kode,
            obatalkes_m.obatalkes_nama,
            cssddet_t.satuanunit_id,
            satuanunit_m.satuanunit_nama,
            cssddet_t.stok,
            cssddet_t.qty,
            cssddet_t.catatan,
            cssddet_t.qty_pengiriman,
            cssddet_t.catatan_pengiriman
            FROM ((((((((cssd_t
            JOIN ( SELECT a.cssddet_id,
            a.cssd_id,
            a.barangalkes_id,
            a.satuanunit_id,
            a.stok,
            a.qty,
            cssdpengirimandet_t.qty_pengiriman,
            a.catatan,
            cssdpengirimandet_t.catatan_pengiriman,
            a.is_active,
            a.is_deleted,
            a.barangalkes_kode,
            a.is_alkes
            FROM (cssddet_t a
            LEFT JOIN ( SELECT b.cssddet_id,
            b.qty AS qty_pengiriman,
            b.catatan AS catatan_pengiriman
            FROM cssdpengirimandet_t b) cssdpengirimandet_t ON ((cssdpengirimandet_t.cssddet_id = a.cssddet_id)))) cssddet_t ON ((cssd_t.cssd_id = cssddet_t.cssd_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((cssd_t.ruanganasal_id = ruangan_asal.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_tujuan ON ((cssd_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_cssd ON ((cssd_t.status_cssd = look_status_cssd.lookup_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_mengetahui ON ((cssd_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_menyetujui ON ((cssd_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
            JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_kode
            FROM obatalkes_m a) obatalkes_m ON ((cssddet_t.barangalkes_id = obatalkes_m.obatalkes_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) satuanunit_m ON ((cssddet_t.satuanunit_id = satuanunit_m.satuanunit_id)))
            WHERE ((cssd_t.is_deleted = false) AND (cssddet_t.is_deleted = false) AND (cssddet_t.is_alkes = true))
            UNION ALL
            SELECT 'BARANG'::text AS tipe,
            cssd_t.cssd_id,
            cssddet_t.cssddet_id,
            cssd_t.ruanganasal_id,
            ruangan_asal.ruangan_nama AS ruanganasal_nama,
            cssd_t.ruangantujuan_id,
            ruangan_tujuan.ruangan_nama AS ruangantujuan_nama,
            cssd_t.tgl_pengajuan_sterilisasi,
            cssd_t.no_pengajuan_sterilisasi,
            cssd_t.status_cssd,
            look_status_cssd.lookup_name AS status_cssd_nama,
            cssd_t.no_proses_sterilisasi,
            cssd_t.tgl_terima,
            cssd_t.peg_mengetahui_id,
            peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
            cssd_t.peg_menyetujui_id,
            peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
            cssddet_t.barangalkes_id,
            cssddet_t.barangalkes_kode,
            barang_m.barang_nama AS obatalkes_nama,
            cssddet_t.satuanunit_id,
            satuanunit_m.satuanunit_nama,
            cssddet_t.stok,
            cssddet_t.qty,
            cssddet_t.catatan,
            cssddet_t.qty_pengiriman,
            cssddet_t.catatan_pengiriman
            FROM ((((((((cssd_t
            JOIN ( SELECT a.cssddet_id,
            a.cssd_id,
            a.barangalkes_id,
            a.satuanunit_id,
            a.stok,
            a.qty,
            cssdpengirimandet_t.qty_pengiriman,
            a.catatan,
            cssdpengirimandet_t.catatan_pengiriman,
            a.is_active,
            a.is_deleted,
            a.barangalkes_kode,
            a.is_alkes
            FROM (cssddet_t a
            LEFT JOIN ( SELECT b.cssddet_id,
            b.qty AS qty_pengiriman,
            b.catatan AS catatan_pengiriman
            FROM cssdpengirimandet_t b) cssdpengirimandet_t ON ((cssdpengirimandet_t.cssddet_id = a.cssddet_id)))) cssddet_t ON ((cssd_t.cssd_id = cssddet_t.cssd_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((cssd_t.ruanganasal_id = ruangan_asal.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_tujuan ON ((cssd_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_cssd ON ((cssd_t.status_cssd = look_status_cssd.lookup_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_mengetahui ON ((cssd_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) peg_menyetujui ON ((cssd_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
            JOIN ( SELECT a.barang_id,
            a.barang_nama,
            a.barang_kode
            FROM barang_m a) barang_m ON ((cssddet_t.barangalkes_id = barang_m.barang_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) satuanunit_m ON ((cssddet_t.satuanunit_id = satuanunit_m.satuanunit_id)))
            WHERE ((cssd_t.is_deleted = false) AND (cssddet_t.is_deleted = false) AND (cssddet_t.is_alkes = false))
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuansterilisasidet_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_164411_migrate_cssd_infopengajuansterilisasidet_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_164411_migrate_cssd_infopengajuansterilisasidet_v cannot be reverted.\n";

        return false;
    }
    */
}
