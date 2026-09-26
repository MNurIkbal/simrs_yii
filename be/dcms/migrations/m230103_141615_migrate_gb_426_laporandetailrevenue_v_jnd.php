<?php

use yii\db\Migration;

/**
 * Class m230103_141615_migrate_gb_426_laporandetailrevenue_v_jnd
 */
class m230103_141615_migrate_gb_426_laporandetailrevenue_v_jnd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporandetailrevenue_v_jnd";');
        $this->execute("
			CREATE OR REPLACE VIEW public.laporandetailrevenue_v_jnd
            AS   SELECT 'LOB'::text AS tipe,
        CASE
            WHEN ruangan_m.instalasi_id = 1 OR pasienmasukpenunjang_t.instalasiasal_id = 1 OR ruangan_m.instalasi_id = 12 AND pendaftaran_t.instalasi_id = 1 THEN 'OPD'::text
            WHEN ruangan_m.instalasi_id = 2 OR pasienmasukpenunjang_t.instalasiasal_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 OR pasienmasukpenunjang_t.instalasiasal_id = 3 OR ruangan_m.instalasi_id = 12 OR ruangan_m.instalasi_id = 17 AND pendaftaran_t.instalasi_id <> 1 THEN 'IPD'::text
            ELSE NULL::text
        END AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOB'::text AS tipe,
    'MCU'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    tipepaket_m.tipepaket_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'PHARMACY'::text AS unit,
    pasien_m.no_rekam_medik,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS nama_pasien,
    obatalkes_m.obatalkes_nama AS tindakan_obat_paket,
    satuanunit_m.satuanunit_nama,
    obatalkespasien_t.hargasatuan_oa AS harga_satuan,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.tarif_diskon,
    obatalkespasien_t.hargajual_oa::integer AS total,
    obatalkespasien_t.tarif_dijamin,
    obatalkespasien_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    obatalkespasien_t.tglpelayanan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = obatalkespasien_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = obatalkespasien_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE obatalkespasien_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'MEDICAL_REHABILITATION'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'HAEMODIALYSIS'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'LABORATORY'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOS'::text AS tipe,
    'RADIOLOGY'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    tindakanpelayanan_t.tarif_satuan AS harga_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_diskon,
    tindakanpelayanan_t.tarif_tindakan::integer AS total,
    tindakanpelayanan_t.tarif_dijamin,
    tindakanpelayanan_t.tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_pulang,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
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
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = tindakanpelayanan_t.kelaspelayanan_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = tindakanpelayanan_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LOB'::text AS tipe,
    'IPD'::text AS unit,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaran_t.no_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasien_m.nama_pasien,
    'ADMINISTRASI'::character varying AS tindakan_obat_paket,
    ''::character varying AS satuanunit_nama,
    pembayaran_t.total_administrasi AS harga_satuan,
    1 AS qty,
    0 AS tarif_diskon,
    pembayaran_t.total_administrasi AS total,
    0 AS tarif_dijamin,
    0 AS tarif_dibayarkan,
    pegawai1.nama_pegawai AS dpjp,
    pegawai_m.nama_pegawai AS dokter,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang AS tgl_pulang,
    pembayaran_t.created_date::date AS tanggal,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama
   FROM pembayaran_t
     JOIN pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pembayaran_t.is_deleted IS FALSE AND pembayaran_t.total_administrasi > 0::double precision
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pasienadmisi_t.pegawai_id
     LEFT JOIN pegawai_m pegawai1 ON pegawai1.pegawai_id = pasienadmisi_t.pegawai_id
     LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pasienadmisi_t.kelaspelayanan_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = pasienadmisi_t.penjamin_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = pendaftaran_t.instalasi_id
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230103_141615_migrate_gb_426_laporandetailrevenue_v_jnd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230103_141615_migrate_gb_426_laporandetailrevenue_v_jnd cannot be reverted.\n";

        return false;
    }
    */
}
