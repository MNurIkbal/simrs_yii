<?php

use yii\db\Migration;

/**
 * Class m220225_043444_migrate_APD195_infopengajuanklaimdetail_v
 */
class m220225_043444_migrate_APD195_infopengajuanklaimdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopengajuanklaimdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuanklaimdetail_v\" AS
            SELECT rincian.pengajuanklaim_id,
            rincian.pengajuanklaimdetail_id,
            rincian.no_pengajuanklaim,
            rincian.pendaftaran_id,
            rincian.pasienadmisi_id,
            rincian.pasien_id,
            rincian.nama_pasien,
            rincian.no_rekam_medik,
            rincian.jumlah_bayar,
            rincian.jumlah_piutang,
            rincian.total_dibayar AS jumlah_telahbayar,
            rincian.jumlah_sisapiutang,
            rincian.no_pendaftaran,
            rincian.tgl_pendaftaran,
            rincian.tglpasienpulang,
            rincian.nosep,
            rincian.instalasi_id,
            rincian.instalasi_nama,
            rincian.ruangan_id,
            rincian.ruangan_nama,
            rincian.total_tagihan,
            rincian.total_tarifrs,
            rincian.no_pembayaran,
            rincian.total_dijamin,
            rincian.total_dibayar,
            rincian.no_pengajuanklaim AS no_invoice,
            rincian.pembayaran_id
            FROM ( SELECT pengajuanklaim_t.pengajuanklaim_id,
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
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 21]))) THEN pendaftaran_t.tgl_pendaftaran
            ELSE pasienpulang_t.tglpasienpulang
            END AS tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM ((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id IS NULL))))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pendaftaran_t.bpjs_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            a.pembayaran_id
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))
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
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 21]))) THEN pendaftaran_t.tgl_pendaftaran
            ELSE pasienpulang_t.tglpasienpulang
            END AS tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM (((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
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
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            b.pembayaran_id
            FROM (pembayaran_t b
            JOIN pembayaranpelayanan_t ON ((b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (b.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))) rincian
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuanklaimdetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220225_043444_migrate_APD195_infopengajuanklaimdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220225_043444_migrate_APD195_infopengajuanklaimdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
