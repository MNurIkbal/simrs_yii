-- public.laporanpendapatanruangan_v source

CREATE OR REPLACE VIEW public.laporanpendapatanruangan_v
AS SELECT 'GABUNG'::text AS tipe,
    gabung.pendaftaran_id,
    gabung.tgl_pendaftaran,
    gabung.no_pendaftaran,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.carabayar_nama,
    gabung.penjamin_nama,
    gabung.nama_pegawai,
    gabung.kelaspelayanan_nama,
    sum(gabung.jasa_rumahsakit_tarif) AS jasa_rumahsakit_tarif,
    sum(gabung.jasa_rumahsakit_cyto) AS jasa_rumahsakit_cyto,
    sum(gabung.jasa_rumahsakit) AS jasa_rumahsakit,
    sum(gabung.jasa_layanan_tarif) AS jasa_layanan_tarif,
    sum(gabung.jasa_layanan_cyto) AS jasa_layanan_cyto,
    sum(gabung.jasa_layanan) AS jasa_layanan,
    sum(gabung.jasa_rumahsakit) + sum(gabung.jasa_layanan) AS total,
    gabung.instalasi_id,
    gabung.ruangan_id,
    gabung.ruangan_nama,
    gabung.carabayar_id,
    gabung.penjamin_id,
    gabung.instalasi_nama,
    '-'::text AS jenis_pemeriksaan,
    '-'::text AS daftartindakan_nama
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_nama,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarif_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_rumahsakit_tarif,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarifcyto_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_rumahsakit_cyto,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id = 1 THEN tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_rumahsakit,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarif_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_layanan_tarif,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarifcyto_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_layanan_cyto,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id <> 1 THEN tindakankomponen_t.tarif_tindakankomp + tindakankomponen_t.tarifcyto_tindakankomp
                    ELSE 0::double precision
                END) AS jasa_layanan,
            sum(
                CASE
                    WHEN tindakankomponen_t.komponentarif_id IS NOT NULL THEN tindakankomponen_t.tarif_tindakankomp
                    ELSE 0::double precision
                END) AS total,
            tindakanpelayanan_t.instalasi_id,
            tindakanpelayanan_t.ruangan_id,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_id,
            penjamin_m.penjamin_id,
            instalasi_m.instalasi_nama
           FROM tindakankomponen_t
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.ruangan_id,
                    a.pendaftaran_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.is_deleted,
                    a.is_active,
                    a.tindakansudahbayar_id,
                    a.instalasi_id
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.instalasi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pasien_id,
                    a.pegawai_id,
                    a.kelaspelayanan_id,
                    a.is_deleted
                   FROM pendaftaran_t a) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.instalasi_id,
                    ruangan_m_1.ruangan_nama
                   FROM ruangan_m ruangan_m_1) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true AND tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakankomponen_t.is_deleted IS FALSE
          GROUP BY ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.pendaftaran_id, pasien_m.pasien_id, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tindakanpelayanan_t.instalasi_id, tindakanpelayanan_t.ruangan_id, instalasi_m.instalasi_nama
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai,
            kelaspelayanan_m.kelaspelayanan_nama,
            0 AS jasa_rumahsakit_tarif,
            0 AS jasa_rumahsakit_cyto,
            0 AS jasa_rumahsakit,
            sum(COALESCE(obatalkespasien_t.hargajual_oa, 0::double precision)) AS jasa_layanan_tarif,
            0 AS jasa_layanan_cyto,
            sum(COALESCE(obatalkespasien_t.hargajual_oa, 0::double precision)) AS jasa_layanan,
            sum(COALESCE(obatalkespasien_t.hargajual_oa, 0::double precision)) AS total,
            pendaftaran_t.instalasi_id,
            obatalkespasien_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            instalasi_m.instalasi_nama
           FROM obatalkespasien_t
             JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true AND obatalkespasien_t.obatsudahbayar_id IS NOT NULL
          GROUP BY ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pendaftaran_t.pendaftaran_id, pasien_m.pasien_id, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.instalasi_id, obatalkespasien_t.ruangan_id, instalasi_m.instalasi_nama) gabung
  WHERE gabung.instalasi_id <> ALL (ARRAY[4, 5])
  GROUP BY gabung.pendaftaran_id, gabung.tgl_pendaftaran, gabung.no_pendaftaran, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_nama, gabung.penjamin_nama, gabung.nama_pegawai, gabung.kelaspelayanan_nama, gabung.instalasi_id, gabung.ruangan_id, gabung.ruangan_nama, gabung.carabayar_id, gabung.penjamin_id, gabung.instalasi_nama
UNION ALL
 SELECT 'RADIOLOGI NON_PAKET'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    COALESCE(rujukan_t.nama_perujuk, perujuk_m.namaperujuk, dokter_perujuk.nama_pegawai) AS nama_pegawai,
    kelaspelayanan_m.kelaspelayanan_nama,
    tindakanpelayanan_t.jasa_rumahsakit_tarif,
    tindakanpelayanan_t.jasa_rumahsakit_cyto,
    tindakanpelayanan_t.jasa_rumahsakit,
    tindakanpelayanan_t.jasa_layanan_tarif,
    tindakanpelayanan_t.jasa_layanan_cyto,
    tindakanpelayanan_t.jasa_layanan,
    tindakanpelayanan_t.total,
    instalasi_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    carabayar_m.carabayar_id,
    penjamin_m.penjamin_id,
    instalasi_m.instalasi_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis_pemeriksaan,
    daftartindakan_m.daftartindakan_nama
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.rujukan_id,
            a.tgl_pendaftaran,
            COALESCE(pasienadmisi_t.pegawai_id, a.pegawai_id) AS pegawai_id,
            COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) AS carabayar_id,
            COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_id,
            COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                    a_1.pegawai_id,
                    a_1.carabayar_id,
                    a_1.penjamin_id,
                    a_1.kelaspelayanan_id
                   FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.tindakanpelayanan_id,
            a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.dokterpenanggungjawab_id,
            a.tarif_satuan,
            a.qty_tindakan,
            a.tarifcyto_tindakan,
            a.tarif_tindakan,
            a.is_deleted,
            a.instalasi_id,
            a.ruangan_id,
            COALESCE(tarif_rs.jasa_rumahsakit_tarif, 0::double precision) AS jasa_rumahsakit_tarif,
            COALESCE(tarif_rs.jasa_rumahsakit, 0::double precision) AS jasa_rumahsakit,
            COALESCE(tarif_rs.jasa_rumahsakit_cyto, 0::double precision) AS jasa_rumahsakit_cyto,
            COALESCE(tarifnon_rs.jasa_layanan_tarif, 0::double precision) AS jasa_layanan_tarif,
            COALESCE(tarifnon_rs.jasa_layanan, 0::double precision) AS jasa_layanan,
            COALESCE(tarifnon_rs.jasa_layanan_cyto, 0::double precision) AS jasa_layanan_cyto,
            COALESCE(tarif_rs.jasa_rumahsakit, 0::double precision) + COALESCE(tarifnon_rs.jasa_layanan, 0::double precision) AS total
           FROM tindakanpelayanan_t a
             LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
                    sum(tindakankomponen_t.tarif_kompsatuan) AS jasa_rumahsakit_tarif,
                    sum(tindakankomponen_t.tarif_tindakankomp) AS jasa_rumahsakit,
                    sum(tindakankomponen_t.tarifcyto_tindakankomp) AS jasa_rumahsakit_cyto
                   FROM tindakankomponen_t
                  WHERE tindakankomponen_t.komponentarif_id = 1 AND tindakankomponen_t.is_deleted IS FALSE
                  GROUP BY tindakankomponen_t.tindakanpelayanan_id) tarif_rs ON a.tindakanpelayanan_id = tarif_rs.tindakanpelayanan_id
             LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
                    sum(tindakankomponen_t.tarif_kompsatuan) AS jasa_layanan_tarif,
                    sum(tindakankomponen_t.tarif_tindakankomp) AS jasa_layanan,
                    sum(tindakankomponen_t.tarifcyto_tindakankomp) AS jasa_layanan_cyto
                   FROM tindakankomponen_t
                  WHERE tindakankomponen_t.komponentarif_id <> 1 AND tindakankomponen_t.is_deleted IS FALSE
                  GROUP BY tindakankomponen_t.tindakanpelayanan_id) tarifnon_rs ON a.tindakanpelayanan_id = tarifnon_rs.tindakanpelayanan_id
          WHERE a.is_deleted IS FALSE AND a.tindakansudahbayar_id IS NOT NULL) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.kelompokpemeriksaanrad_id,
            a.jenispemeriksaanrad_id,
            a.is_contrast
           FROM pemeriksaanrad_m a) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            max(a.created_date) AS tgl_periksa,
            a.tindakanpelayanan_id,
            max(a.tgl_verifikasi) AS tgl_verifikasi
           FROM hasilpemeriksaanrad_t a
          WHERE a.is_deleted IS FALSE AND a.tgl_verifikasi IS NOT NULL
          GROUP BY a.pasienmasukpenunjang_id, a.tindakanpelayanan_id) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.catatan_dokterpengirim,
            a.pegawai_id
           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.kelompokpemeriksaanrad_id,
            a.nama_kelompok
           FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
     JOIN ( SELECT a.jenispemeriksaanrad_id,
            a.jenispemeriksaanrad_nama
           FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.asalrujukan_id,
            a.rujukandari_id,
            a.nama_perujuk
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT a.perujuk_id,
            a.namaperujuk
           FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
UNION ALL
 SELECT 'LAB NON_PAKET'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    dokter_perujuk.nama_pegawai,
    kelaspelayanan_m.kelaspelayanan_nama,
    tindakanpelayanan_t.jasa_rumahsakit_tarif,
    tindakanpelayanan_t.jasa_rumahsakit_cyto,
    tindakanpelayanan_t.jasa_rumahsakit,
    tindakanpelayanan_t.jasa_layanan_tarif,
    tindakanpelayanan_t.jasa_layanan_cyto,
    tindakanpelayanan_t.jasa_layanan,
    tindakanpelayanan_t.total,
    instalasi_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    carabayar_m.carabayar_id,
    penjamin_m.penjamin_id,
    instalasi_m.instalasi_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis_pemeriksaan,
    daftartindakan_m.daftartindakan_nama
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.pasien_id,
            COALESCE(pasienadmisi_t.pegawai_id, a.pegawai_id) AS pegawai_id,
            COALESCE(pasienadmisi_t.carabayar_id, a.carabayar_id) AS carabayar_id,
            COALESCE(pasienadmisi_t.penjamin_id, a.penjamin_id) AS penjamin_id,
            COALESCE(pasienadmisi_t.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                    a_1.pegawai_id,
                    a_1.carabayar_id,
                    a_1.penjamin_id,
                    a_1.kelaspelayanan_id
                   FROM pasienadmisi_t a_1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.tindakanpelayanan_id,
            a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.dokterpenanggungjawab_id,
            a.tarif_satuan,
            a.qty_tindakan,
            a.tarifcyto_tindakan,
            a.tarif_tindakan,
            a.is_deleted,
            a.instalasi_id,
            a.ruangan_id,
            COALESCE(tarif_rs.jasa_rumahsakit_tarif, 0::double precision) AS jasa_rumahsakit_tarif,
            COALESCE(tarif_rs.jasa_rumahsakit, 0::double precision) AS jasa_rumahsakit,
            COALESCE(tarif_rs.jasa_rumahsakit_cyto, 0::double precision) AS jasa_rumahsakit_cyto,
            COALESCE(tarifnon_rs.jasa_layanan_tarif, 0::double precision) AS jasa_layanan_tarif,
            COALESCE(tarifnon_rs.jasa_layanan, 0::double precision) AS jasa_layanan,
            COALESCE(tarifnon_rs.jasa_layanan_cyto, 0::double precision) AS jasa_layanan_cyto,
            COALESCE(tarif_rs.jasa_rumahsakit, 0::double precision) + COALESCE(tarifnon_rs.jasa_layanan, 0::double precision) AS total
           FROM tindakanpelayanan_t a
             LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
                    sum(tindakankomponen_t.tarif_kompsatuan) AS jasa_rumahsakit_tarif,
                    sum(tindakankomponen_t.tarif_tindakankomp) AS jasa_rumahsakit,
                    sum(tindakankomponen_t.tarifcyto_tindakankomp) AS jasa_rumahsakit_cyto
                   FROM tindakankomponen_t
                  WHERE tindakankomponen_t.komponentarif_id = 1 AND tindakankomponen_t.is_deleted IS FALSE
                  GROUP BY tindakankomponen_t.tindakanpelayanan_id) tarif_rs ON a.tindakanpelayanan_id = tarif_rs.tindakanpelayanan_id
             LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
                    sum(tindakankomponen_t.tarif_kompsatuan) AS jasa_layanan_tarif,
                    sum(tindakankomponen_t.tarif_tindakankomp) AS jasa_layanan,
                    sum(tindakankomponen_t.tarifcyto_tindakankomp) AS jasa_layanan_cyto
                   FROM tindakankomponen_t
                  WHERE tindakankomponen_t.komponentarif_id <> 1 AND tindakankomponen_t.is_deleted IS FALSE
                  GROUP BY tindakankomponen_t.tindakanpelayanan_id) tarifnon_rs ON a.tindakanpelayanan_id = tarifnon_rs.tindakanpelayanan_id
          WHERE a.is_deleted IS FALSE AND a.tindakansudahbayar_id IS NOT NULL) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.jenispemeriksaanlab_id
           FROM pemeriksaanlab_m a) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     LEFT JOIN ( SELECT a.jenispemeriksaanlab_id,
            a.jenispemeriksaanlab_nama
           FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.catatan_dokterpengirim,
            a.pegawai_id
           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id;
