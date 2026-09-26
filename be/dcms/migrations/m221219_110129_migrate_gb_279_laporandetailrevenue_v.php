<?php

use yii\db\Migration;

/**
 * Class m221219_110129_migrate_gb_279_laporandetailrevenue_v
 */
class m221219_110129_migrate_gb_279_laporandetailrevenue_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporandetailrevenue_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporandetailrevenue_v\" AS  SELECT 'LOS'::text AS tipe,
    'PHARMACY'::text AS unit,
    obatalkespasien_t.tglpelayanan::date AS tanggal,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    pasien_m.no_rekam_medik,
    COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS nama_pasien,
    carabayar_m.carabayar_nama,
    obatalkes_m.obatalkes_nama AS tindakan_obat_paket,
    satuanunit_m.satuanunit_nama,
    obatalkespasien_t.hargasatuan_oa AS harga_satuan,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargajual_oa::integer AS total,
    pembayaran_t.no_pembayaran,
    obatalkespasien_t.tarif_dijamin,
    obatalkespasien_t.tarif_dibayarkan,
    obatalkespasien_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM obatalkespasien_t
     JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON obatalkespasien_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = obatalkespasien_t.pegawai_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE obatalkespasien_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'MEDICAL_REHABILITATION'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 75
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN tipepaket_m ON tipepaket_m.tipepaket_id = tindakanpelayanan_t.tipepaket_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'HAEMODIALYSIS'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT daftartindakan_m_1.daftartindakan_id
           FROM daftartindakan_m daftartindakan_m_1
          WHERE \"substring\"(daftartindakan_m_1.daftartindakan_kode::text, 1, 2) = '34'::text OR daftartindakan_m_1.daftartindakan_nama::text ~~* '%hemodialisa%'::text) med_rehab ON tindakanpelayanan_t.daftartindakan_id = med_rehab.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'LABORATORY'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN tipepaket_m ON tipepaket_m.tipepaket_id = tindakanpelayanan_t.tipepaket_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'RADIOLOGY'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN tipepaket_m ON tipepaket_m.tipepaket_id = tindakanpelayanan_t.tipepaket_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOB'::text AS tipe,
        CASE
            WHEN ruangan_m.instalasi_id = 1 OR pasienmasukpenunjang_t.instalasiasal_id = 1 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id = 1 THEN 'OPD'::text
            WHEN ruangan_m.instalasi_id = 2 OR pasienmasukpenunjang_t.instalasiasal_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 OR pasienmasukpenunjang_t.instalasiasal_id = 3 OR ruangan_m.instalasi_id = 12 OR ruangan_m.instalasi_id = 17 AND pendaftaran_t.instalasi_id <> 1 THEN 'IPD'::text
            ELSE NULL::text
        END AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     LEFT JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN ( SELECT daftartindakan_m_1.daftartindakan_id
           FROM daftartindakan_m daftartindakan_m_1
          WHERE \"substring\"(daftartindakan_m_1.daftartindakan_kode::text, 1, 2) <> '34'::text AND \"substring\"(daftartindakan_m_1.daftartindakan_kode::text, 1, 2) <> '34'::text AND daftartindakan_m_1.daftartindakan_nama::text !~~* '%hemodialisa%'::text) med_rehab ON tindakanpelayanan_t.daftartindakan_id = med_rehab.daftartindakan_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND (ruangan_m.instalasi_id = ANY (ARRAY[1, 2, 3, 12, 17]))
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOB'::text AS tipe,
    'MCU'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    tipepaket_m.tipepaket_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     LEFT JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 21
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN tipepaket_m ON tipepaket_m.tipepaket_id = tindakanpelayanan_t.tipepaket_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    '*     NON_PCR'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    non_pcr.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     JOIN ( SELECT daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama
           FROM daftartindakan_m
          WHERE daftartindakan_m.daftartindakan_nama::text !~~* '%PCR%'::text) non_pcr ON tindakanpelayanan_t.daftartindakan_id = non_pcr.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    '*     PCR_COVID19'::text AS unit,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    pcr.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    pembayaran_t.no_pembayaran,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    tindakanpelayanan_t.tarif_diskon,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     JOIN ( SELECT daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama
           FROM daftartindakan_m
             JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
          WHERE daftartindakan_m.daftartindakan_nama::text ~~* '%PCR%'::text) pcr ON tindakanpelayanan_t.daftartindakan_id = pcr.daftartindakan_id
     LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaran_t ON tindakanpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221219_110129_migrate_gb_279_laporandetailrevenue_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221219_110129_migrate_gb_279_laporandetailrevenue_v cannot be reverted.\n";

        return false;
    }
    */
}
