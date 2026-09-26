<?php

use yii\db\Migration;

/**
 * Class m200711_043412_migrate_mhkn_20200711
 */
class m200711_043412_migrate_mhkn_20200711 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infotagihandetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotagihandetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_cyto
   FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, (0)::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM (((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
          WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
                CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, (0)::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM ((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
          WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
                CASE COALESCE(obatalkespasien_t.tarifcyto, (0)::double precision)
                    WHEN 0 THEN false
                    ELSE true
                END AS is_cyto
           FROM ((pendaftaran_t
             JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
          WHERE (obatalkespasien_t.is_deleted IS FALSE)) tagihan
     LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN carabayar_m ON ((tagihan.carabayar_pelayanan_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id)))
     LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m dokter_dpjp ON ((tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id)));");

        $this->execute('DROP VIEW if exists "public"."rincianpasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rincianpasien_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
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
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_lunas,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN (pendaftaran_t.is_stopakomodasi <> true) THEN false
            WHEN (pendaftaran_t.is_stopakomodasi = true) THEN true
            ELSE NULL::boolean
        END AS is_pulang,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)
        END AS tgl_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pasienadmisi_t.tgl_admisi, 'HH24:MI:SS'::text)
            ELSE to_char(pendaftaran_t.tgl_pendaftaran, 'HH24:MI:SS'::text)
        END AS jam_masuk,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END AS tgl_keluar,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END AS jam_keluar
   FROM ((((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_admisi ON ((pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_admisi ON ((pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_admisi ON ((pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     LEFT JOIN pasienpulang_t pulang_pendaftaran ON ((pendaftaran_t.pasienpulang_id = pulang_pendaftaran.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_admisi ON ((pasienadmisi_t.pasienpulang_id = pulang_admisi.pasienpulang_id)))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama, pasien_m.namadepan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'YYYY-MM-DD'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'YYYY-MM-DD'::text)
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN to_char(pulang_admisi.tglpasienpulang, 'HH24:MI:SS'::text)
            ELSE to_char(pulang_pendaftaran.tglpasienpulang, 'HH24:MI:SS'::text)
        END;");

        $this->execute('DROP VIEW if  exists "public"."rincianpasiendetail_v";');

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
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)));");
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200711_043412_migrate_mhkn_20200711 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200711_043412_migrate_mhkn_20200711 cannot be reverted.\n";

        return false;
    }
    */
}
