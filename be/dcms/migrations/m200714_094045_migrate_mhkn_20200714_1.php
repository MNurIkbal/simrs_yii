<?php

use yii\db\Migration;

/**
 * Class m200714_094045_migrate_mhkn_20200714_1
 */
class m200714_094045_migrate_mhkn_20200714_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infotagihanpasienpulangdetail_v";');
        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasienpulangdetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pasien_id,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.pasienadmisi_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    (tagihan.tarif_satuan)::integer AS tarif_satuan,
    tagihan.qty,
    (tagihan.sub_total)::integer AS sub_total,
    tagihan.ruangan_id,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    tagihan.carabayar_pendaftaran_id,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienpulang_id,
    tagihan.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    carabayar_m.carabayar_nama AS carabayar_pendaftaran,
    penjamin_m.penjamin_nama AS penjamin_pendaftaran,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.instalasi_nama AS instalasi_pelayanan,
    tagihan.ruangan_nama AS ruangan_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_mobile_pasien,
    pasienpulang_t.tglpasienpulang,
    bayaruangmuka_t.jumlah_uangmuka,
    tagihan.pelayanan_id,
    tagihan.cyto_tindakan,
    tagihan.tarifcyto_tindakan
   FROM ((((((((( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienadmisi_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            pendaftaran_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            daftartindakan_m.kelompoktindakan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM ((((((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN carabayar_m carabayar_m_1 ON ((tindakanpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienadmisi_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            pendaftaran_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS kelompoktindakan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM ((((((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN carabayar_m carabayar_m_1 ON ((tindakanpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((tindakanpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienadmisi_id,
            obatalkespasien_t.obatalkes_id,
            obatalkes_m.obatalkes_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
            obatalkespasien_t.qty_oa,
            obatalkespasien_t.hargajual_oa,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan,
            pendaftaran_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            pendaftaran_t.carabayar_id AS carabayar_pendaftaran_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS var,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkespasien_id,
            false AS cyto_tindakan,
            0 AS tarifcyto_tindakan,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM ((((((pendaftaran_t
             JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN carabayar_m carabayar_m_1 ON ((obatalkespasien_t.carabayar_id = carabayar_m_1.carabayar_id)))
             JOIN penjamin_m penjamin_m_1 ON ((obatalkespasien_t.penjamin_id = penjamin_m_1.penjamin_id)))
             JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE (obatalkespasien_t.is_deleted = false)) tagihan
     LEFT JOIN pasienadmisi_t ON ((tagihan.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_pendaftaran_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_pendaftaran_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasienpulang_t ON ((tagihan.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     LEFT JOIN kelompoktindakan_m ON ((tagihan.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
            sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
           FROM bayaruangmuka_t bayaruangmuka_t_1
          WHERE (bayaruangmuka_t_1.is_deleted = false)
          GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON ((tagihan.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
  WHERE (tagihan.tindakansudahbayar_id IS NULL);");

        $this->execute('DROP VIEW if exists "public"."rincianpasiendetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rincianpasiendetail_v\" AS  SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.daftartindakan_kode AS kode
   FROM (((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN instalasi_m instalasi_pelayanan ON ((tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id)))
     LEFT JOIN ruangan_m ruangan_pelayanan ON ((tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id)))
     LEFT JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
     LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
     LEFT JOIN pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
     LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
  WHERE (tindakanpelayanan_t.is_deleted = false)
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi,
    0 AS kelompoktindakan_id,
    'Obat'::character varying AS kelompoktindakan_nama,
    obatalkes_m.obatalkes_kode AS kode
   FROM (((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
  WHERE (obatalkespasien_t.is_deleted = false);
            ");

        $this->execute('DROP VIEW if exists "public"."infopasienbelumbayar_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienbelumbayar_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_1.carabayar_nama
            ELSE cb_2.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_nama
            ELSE pj_2.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_1.nama_pegawai
            ELSE dr_2.nama_pegawai
        END AS nama_dokter,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_1.instalasi_nama
            ELSE ins_1.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_nama
            ELSE ruang_2.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
    (COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) AS total_tagihan,
    COALESCE(uang_masuk.total_uangmasuk, (0)::double precision) AS uang_masuk,
    ((COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) - COALESCE(uang_masuk.total_uangmasuk, (0)::double precision)) AS sisa_tagihan
   FROM (((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN carabayar_m cb_1 ON ((pendaftaran_t.carabayar_id = cb_1.carabayar_id)))
     LEFT JOIN carabayar_m cb_2 ON ((pasienadmisi_t.carabayar_id = cb_2.carabayar_id)))
     LEFT JOIN penjamin_m pj_1 ON ((pendaftaran_t.penjamin_id = pj_1.penjamin_id)))
     LEFT JOIN penjamin_m pj_2 ON ((pasienadmisi_t.penjamin_id = pj_2.penjamin_id)))
     LEFT JOIN pegawai_m dr_1 ON ((pendaftaran_t.pegawai_id = dr_1.pegawai_id)))
     LEFT JOIN pegawai_m dr_2 ON ((pasienadmisi_t.pegawai_id = dr_2.pegawai_id)))
     LEFT JOIN ruangan_m ruang_1 ON ((pendaftaran_t.ruangan_id = ruang_1.ruangan_id)))
     LEFT JOIN ruangan_m ruang_2 ON ((pasienadmisi_t.ruangan_id = ruang_2.ruangan_id)))
     LEFT JOIN instalasi_m ins_1 ON ((ruang_1.instalasi_id = ins_1.instalasi_id)))
     LEFT JOIN instalasi_m ins_2 ON ((ruang_2.instalasi_id = dr_2.pegawai_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan
           FROM tindakanpelayanan_t
          WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON ((pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id)))
     LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat
           FROM obatalkespasien_t
          WHERE (obatalkespasien_t.is_deleted = false)
          GROUP BY obatalkespasien_t.pendaftaran_id) obat ON ((pendaftaran_t.pendaftaran_id = obat.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum((pembayaran_t.total_dibayar + pembayaran_t.penggunaan_uangmuka)) AS total_uangmasuk
           FROM pembayaran_t
          WHERE (pembayaran_t.is_deleted = false)
          GROUP BY pembayaran_t.pendaftaran_id) uang_masuk ON ((pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id)));");
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200714_094045_migrate_mhkn_20200714_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200714_094045_migrate_mhkn_20200714_1 cannot be reverted.\n";

        return false;
    }
    */
}
