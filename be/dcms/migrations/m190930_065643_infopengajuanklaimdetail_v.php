<?php

use yii\db\Migration;

/**
 * Class m190930_065643_infopengajuanklaimdetail_v
 */
class m190930_065643_infopengajuanklaimdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists public.infopengajuanklaimdetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infopengajuanklaimdetail_v AS 
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
    rincianpasiendetail.total_sdh_bayar AS jumlah_telahbayar,
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
    rincian.total_tarifrs
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
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            rincian_header_tagihan_pasien.total_tagihan,
            klaiminacbg_t.total_tarifrs
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pengajuanklaimdetail_t.pasienadmisi_id IS NULL
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pendaftaran_t.bpjs_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN rincian_header_tagihan_pasien ON pendaftaran_t.pendaftaran_id = rincian_header_tagihan_pasien.pendaftaran_id
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.pasienadmisi_id IS NULL AND klaiminacbg_t.is_deleted = false
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
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            rincian_header_tagihan_pasien.total_tagihan,
            klaiminacbg_t.total_tarifrs
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pengajuanklaimdetail_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN rincian_header_tagihan_pasien ON pendaftaran_t.pendaftaran_id = rincian_header_tagihan_pasien.pendaftaran_id
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = klaiminacbg_t.pasienadmisi_id AND klaiminacbg_t.is_deleted = false) rincian
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            sum(pembayaranpelayanan_t.total_terbayar) AS total_sdh_bayar
           FROM pendaftaran_t
             LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
          GROUP BY pendaftaran_t.pendaftaran_id) rincianpasiendetail ON rincian.pendaftaran_id = rincianpasiendetail.pendaftaran_id;
");

         $this->execute('ALTER TABLE public.infopengajuanklaimdetail_v
  OWNER TO postgres;');
         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190930_065643_infopengajuanklaimdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190930_065643_infopengajuanklaimdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
