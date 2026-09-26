<?php

use yii\db\Migration;

/**
 * Class m220225_043629_migrate_APD195_infopembayaranalokasidetail_v
 */
class m220225_043629_migrate_APD195_infopembayaranalokasidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopembayaranalokasidetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopembayaranalokasidetail_v\" AS
            SELECT pengajuan.pengajuanklaim_id,
            pengajuan.pengajuanklaimdetail_id,
            pengajuan.no_pengajuanklaim,
            pengajuan.pendaftaran_id,
            pengajuan.pasienadmisi_id,
            pengajuan.pasien_id,
            pengajuan.nama_pasien,
            pengajuan.no_rekam_medik,
            pengajuan.jumlah_bayar,
            pengajuan.jumlah_piutang,
            pengajuan.jumlah_telahbayar,
            pengajuan.jumlah_sisapiutang,
            pengajuan.tgl_pendaftaran,
            pengajuan.tglpasienpulang,
            pengajuan.no_pendaftaran,
            pengajuan.nosep,
            pengajuan.instalasi_id,
            pengajuan.instalasi_nama,
            pengajuan.ruangan_id,
            pengajuan.ruangan_nama,
            pembayaranalokasidetail_t.jumlah_bayar AS bayar_alokasi,
            pembayaranalokasidetail_t.pembayaranalokasidetail_id,
            pembayaranalokasidetail_t.pembayaranalokasi_id,
            pengajuan.total_tagihan,
            pengajuan.no_pembayaran,
            pengajuan.total_dijamin,
            pengajuan.total_dibayar
            FROM (( SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN (pendaftaran_t.is_aps = true) THEN pendaftaran_t.tgl_pendaftaran
            ELSE pasienpulang_t.tglpasienpulang
            END AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.total_tagihan,
            pembayaran.total_tagihan,
            pembayaran.no_pembayaran,
            pembayaran.total_dijamin,
            pembayaran.total_dibayar
            FROM ((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON ((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pendaftaran_t.bpjs_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id
            FROM (pembayaran_t a
            LEFT JOIN pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran.pembayaranpelayanan_id)))
            WHERE ((pengajuanklaimdetail_t.pasienadmisi_id IS NULL) AND (pengajuanklaimdetail_t.is_deleted = false))
            UNION ALL
            SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.total_tagihan,
            pembayaran.total_tagihan,
            pembayaran.no_pembayaran,
            pembayaran.total_dijamin,
            pembayaran.total_dibayar
            FROM (((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pasienadmisi_t ON ((pengajuanklaimdetail_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            ((b.total_tagihan + b.total_administrasi) + b.total_pembulatan) AS total_tagihan,
            b.total_dibayar,
            b.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            b.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id
            FROM (pembayaran_t b
            LEFT JOIN pembayaranpelayanan_t ON ((b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (b.is_deleted = false)) pembayaran ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran.pembayaranpelayanan_id)))
            WHERE (pengajuanklaimdetail_t.is_deleted = false)) pengajuan(pengajuanklaim_id, pengajuanklaimdetail_id, no_pengajuanklaim, pendaftaran_id, pasienadmisi_id, pasien_id, nama_pasien, no_rekam_medik, jumlah_bayar, jumlah_piutang, jumlah_telahbayar, jumlah_sisapiutang, tgl_pendaftaran, tglpasienpulang, no_pendaftaran, nosep, instalasi_id, instalasi_nama, ruangan_id, ruangan_nama, total_tagihan, total_tagihan_1, no_pembayaran, total_dijamin, total_dibayar)
            JOIN pembayaranalokasidetail_t ON ((pembayaranalokasidetail_t.pengajuanklaimdetail_id = pengajuan.pengajuanklaimdetail_id)))
            WHERE (pembayaranalokasidetail_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infopembayaranalokasidetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220225_043629_migrate_APD195_infopembayaranalokasidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220225_043629_migrate_APD195_infopembayaranalokasidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
