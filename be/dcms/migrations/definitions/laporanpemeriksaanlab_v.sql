-- public.laporanpemeriksaanlab_v source

CREATE OR REPLACE VIEW public.laporanpemeriksaanlab_v
AS SELECT 'NON_PAKET'::text AS tipe,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    COALESCE(pasienmasukpenunjang_t.pegawai_id, dokter.pegawai_id) AS pegawai_id,
    COALESCE(dokter2.nama_pegawai, dokter.nama_pegawai) AS dokter,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    COALESCE(pasienadmisi_t.pegawai_id, dokter.pegawai_id) AS dokter_dpjp_id,
    COALESCE(dokter_dpjp.nama_pegawai, dokter.nama_pegawai) AS dokter_dpjp_nama,
    COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
    COALESCE(kelaspelayanan_admisi.kelaspelayanan_nama, kelaspelayanan_pendaftaran.kelaspelayanan_nama) AS kelaspelayanan_nama,
    pasienmasukpenunjang_t.ruangan_id,
    ruangan_m.ruangan_nama
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.kelaspelayanan_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter2 ON pasienmasukpenunjang_t.pegawai_id = dokter2.pegawai_id
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.tarif_satuan,
            a.qty_tindakan,
            a.tarifcyto_tindakan,
            a.tarif_tindakan,
            a.instalasi_id,
            a.parent_id,
            a.harga_origin,
            a.dokterpenanggungjawab_id,
            a.tindakanpelayanan_id
           FROM tindakanpelayanan_t a
          WHERE a.is_deleted = false) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.kelompokpemeriksaanlab_id,
            a.jenispemeriksaanlab_id
           FROM pemeriksaanlab_m a
          WHERE a.is_deleted = false) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN ( SELECT a.kelompokpemeriksaanlab_id,
            a.nama_kelompok
           FROM kelompokpemeriksaanlab_m a
          WHERE a.is_deleted = false) kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
     JOIN ( SELECT a.jenispemeriksaanlab_id,
            a.jenispemeriksaanlab_nama
           FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pegawai_id,
            a.kelaspelayanan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_dpjp ON pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_admisi ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_admisi.kelaspelayanan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
  WHERE tindakanpelayanan_t.instalasi_id = 4 AND pasienmasukpenunjang_t.status_periksa::integer <> 476
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.pasien_id, pasien_m.nama_pasien, pasien_m.no_rekam_medik, pasien_m.tanggal_lahir, dokter.pegawai_id, dokter2.nama_pegawai, dokter.nama_pegawai, pemeriksaanlab_m.kelompokpemeriksaanlab_id, kelompokpemeriksaanlab_m.nama_kelompok, pemeriksaanlab_m.jenispemeriksaanlab_id, jenispemeriksaanlab_m.jenispemeriksaanlab_nama, tindakanpelayanan_t.daftartindakan_id, daftartindakan_m.daftartindakan_nama, tindakanpelayanan_t.tarif_satuan, tindakanpelayanan_t.qty_tindakan, tindakanpelayanan_t.tarifcyto_tindakan, tindakanpelayanan_t.tarif_tindakan, pasienadmisi_t.pegawai_id, dokter_dpjp.nama_pegawai, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_admisi.kelaspelayanan_nama, kelaspelayanan_pendaftaran.kelaspelayanan_nama, tindakanpelayanan_t.tindakanpelayanan_id, pasienmasukpenunjang_t.ruangan_id, ruangan_m.ruangan_nama
UNION ALL
 SELECT 'PAKET'::text AS tipe,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    COALESCE(pasienmasukpenunjang_t.pegawai_id, dokter.pegawai_id) AS pegawai_id,
    COALESCE(dokter2.nama_pegawai, dokter.nama_pegawai) AS dokter,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
        CASE
            WHEN tindakanpelayanan_t.parent_id IS NOT NULL THEN tindakanpelayanan_t.harga_origin
            WHEN tindakanpelayanan_t.parent_id IS NULL THEN tindakanpelayanan_t.tarif_tindakan
            ELSE NULL::double precision
        END AS tarif_tindakan,
    COALESCE(pasienadmisi_t.pegawai_id, dokter.pegawai_id) AS dokter_dpjp_id,
    COALESCE(dokter_dpjp.nama_pegawai, dokter.nama_pegawai) AS dokter_dpjp_nama,
    COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
    COALESCE(kelaspelayanan_admisi.kelaspelayanan_nama, kelaspelayanan_pendaftaran.kelaspelayanan_nama) AS kelaspelayanan_nama,
    pasienmasukpenunjang_t.ruangan_id,
    ruangan_m.ruangan_nama
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.kelaspelayanan_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter2 ON pasienmasukpenunjang_t.pegawai_id = dokter2.pegawai_id
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.tipepaket_id,
            a.tarif_satuan,
            a.qty_tindakan,
            a.tarifcyto_tindakan,
            a.tarif_tindakan,
            a.harga_origin,
            a.parent_id,
            a.is_deleted,
            a.dokterpenanggungjawab_id,
            a.tindakanpelayanan_id
           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama
           FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT a.tipepaket_id,
            a.daftartindakan_id
           FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.kelompokpemeriksaanlab_id,
            a.jenispemeriksaanlab_id
           FROM pemeriksaanlab_m a
          WHERE a.is_deleted = false) pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     JOIN ( SELECT a.kelompokpemeriksaanlab_id,
            a.nama_kelompok
           FROM kelompokpemeriksaanlab_m a
          WHERE a.is_deleted = false) kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
     JOIN ( SELECT a.jenispemeriksaanlab_id,
            a.jenispemeriksaanlab_nama
           FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pegawai_id,
            a.kelaspelayanan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_dpjp ON pasienadmisi_t.pegawai_id = dokter_dpjp.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_admisi ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_admisi.kelaspelayanan_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
  WHERE pasienmasukpenunjang_t.status_periksa::integer <> 476;