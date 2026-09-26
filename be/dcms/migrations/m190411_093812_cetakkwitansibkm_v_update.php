<?php

use yii\db\Migration;

/**
 * Class m190411_093812_cetakkwitansibkm_v_update
 */
class m190411_093812_cetakkwitansibkm_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW cetakkwitansibkm_v;
        ');

        $this->execute("
  CREATE OR REPLACE VIEW cetakkwitansibkm_v AS 
 SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    tandabuktibayar_t.tglbuktibayar AS tgl_pembayaran,
    pulang_pendaftaran.tglpasienpulang AS tglpulang_pendaftaran,
    pulang_ranap.tglpasienpulang AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
        CASE
            WHEN pembayaranpelayanan_t.total_subsidiasuransi = 0::double precision THEN pembayaranpelayanan_t.total_bayartindakan
            ELSE pembayaranpelayanan_t.total_subsidiasuransi
        END AS total_terbayar,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.tandabuktibayar_id,
    0 AS penjualanresep_id
   FROM tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id
     LEFT JOIN pasienpulang_t pulang_ranap ON pasienadmisi_t.pasienpulang_id = pulang_ranap.pasienpulang_id
     JOIN pegawai_m ON tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
UNION ALL
 SELECT NULL::integer AS pembayaranpelayanan_id,
    bayaruangmuka_t.bayaruangmuka_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.instalasi_id,
    bayaruangmuka_t.no_uangmuka AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    tandabuktibayar_t.tglbuktibayar AS tgl_pembayaran,
    pulang_pendaftaran.tglpasienpulang AS tglpulang_pendaftaran,
    pulang_ranap.tglpasienpulang AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tandabuktibayar_t.jmlpembayaran AS total_terbayar,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.tandabuktibayar_id,
    0 AS penjualanresep_id
   FROM tandabuktibayar_t
     JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id
     LEFT JOIN pasienpulang_t pulang_ranap ON pasienadmisi_t.pasienpulang_id = pulang_ranap.pasienpulang_id
     JOIN pegawai_m ON tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
UNION ALL
 SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
    NULL::integer AS bayaruangmuka_id,
    NULL::integer AS pendaftaran_id,
    ruangan_m.instalasi_id,
    pembayaranpelayanan_t.no_pembayaran AS no_kwitansi,
    tandabuktibayar_t.nobuktibayar AS no_bkm,
    penjualanresep_t.noresep AS no_pendaftaran,
    penjualanresep_t.tglresep AS tgl_pendaftaran,
    tandabuktibayar_t.tglbuktibayar AS tgl_pembayaran,
    NULL::timestamp without time zone AS tglpulang_pendaftaran,
    NULL::timestamp without time zone AS tglpulang_ranap,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
        CASE
            WHEN pembayaranpelayanan_t.total_subsidiasuransi = 0::double precision THEN pembayaranpelayanan_t.total_bayartindakan
            ELSE pembayaranpelayanan_t.total_subsidiasuransi
        END AS total_terbayar,
    pegawai_m.nama_pegawai AS kasir,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.tandabuktibayar_id,
    penjualanresep_t.penjualanresep_id
   FROM tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON tandabuktibayar_t.pegawai1_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id;

               ");
        
        $this->execute('
           ALTER TABLE cetakkwitansibkm_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_093812_cetakkwitansibkm_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_093812_cetakkwitansibkm_v_update cannot be reverted.\n";

        return false;
    }
    */
}
