-- public.laporanpenyetoranrawatjalan_v source

CREATE OR REPLACE VIEW public.laporanpenyetoranrawatjalan_v
AS WITH rawat_jalan AS (
         SELECT 'Kunjungan'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS konsulpoli_id,
            pendaftaran_t.tgl_pendaftaran::date AS tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.umur,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pegawai_id,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.daftartindakan_id
           FROM pendaftaran_t
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.ruangan_id,
                    a.daftartindakan_id
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted = false AND a.tindakansudahbayar_id IS NOT NULL
                UNION ALL
                 SELECT b.tindakanpelayanan_id,
                    b.pendaftaran_id,
                    pasienmasukpenunjang_t.ruanganasal_id AS ruangan_id,
                    b.daftartindakan_id
                   FROM tindakanpelayanan_t b
                     JOIN ( SELECT c.pasienmasukpenunjang_id,
                            c.ruanganasal_id
                           FROM pasienmasukpenunjang_t c) pasienmasukpenunjang_t ON b.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                  WHERE b.is_deleted = false AND b.tindakansudahbayar_id IS NOT NULL) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND pendaftaran_t.ruangan_id = tindakanpelayanan_t.ruangan_id
          WHERE (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND pendaftaran_t.instalasi_id = 1
        UNION ALL
         SELECT 'Konsul'::text AS tipe,
            konsulpoli_t.pendaftaran_id,
            konsulpoli_t.konsulpoli_id,
            konsulpoli_t.tgl_konsulpoli::date AS tgl_pendaftaran,
            konsulpoli_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.umur,
            konsulpoli_t.ruangan_id,
            konsulpoli_t.pegawai_id,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.daftartindakan_id
           FROM konsulpoli_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.penjamin_id,
                    a.umur
                   FROM pendaftaran_t a
                  WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 628])) AND a.pasienbatalperiksa_id IS NULL AND a.instalasi_id = 1) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.pendaftaran_id,
                    a.ruangan_id,
                    a.daftartindakan_id
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted = false AND a.tindakansudahbayar_id IS NOT NULL
                UNION ALL
                 SELECT b.tindakanpelayanan_id,
                    b.pendaftaran_id,
                    pasienmasukpenunjang_t.ruanganasal_id AS ruangan_id,
                    b.daftartindakan_id
                   FROM tindakanpelayanan_t b
                     JOIN ( SELECT c.pasienmasukpenunjang_id,
                            c.ruanganasal_id
                           FROM pasienmasukpenunjang_t c) pasienmasukpenunjang_t ON b.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                  WHERE b.is_deleted = false AND b.tindakansudahbayar_id IS NOT NULL) tindakanpelayanan_t ON konsulpoli_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND konsulpoli_t.ruangan_id = tindakanpelayanan_t.ruangan_id
          WHERE konsulpoli_t.status_approve = 565 AND (konsulpoli_t.status_periksa::integer <> ALL (ARRAY[402, 628]))
        )
 SELECT rawat_jalan.tgl_pendaftaran AS "Tanggal Kunjungan",
    pasien_m.no_rekam_medik AS "No RM",
    pasien_m.nama_pasien AS "Nama Pasien",
    penjamin_m.penjamin_nama AS "Penjamin",
    rawat_jalan.umur AS "Umur",
    pasien_m.jenis_kelamin AS "Jenis Kelamin",
    pasien_m.alamat_pasien AS "Alamat",
    ruangan_m.ruangan_nama AS "Poli",
    pegawai_m.nama_pegawai AS "Dokter",
    daftartindakan_m.daftartindakan_nama AS "Tindakan",
    surat_keterangan.judul_surat AS "Surat Keterangan",
    COALESCE(jasa_rs.tarif_jasa_rs, 0::double precision) AS "Jasa RS",
    COALESCE(jasa_pelayanan.tarif_jasa_pelayanan, 0::double precision) AS "Jasa Pelayanan",
    COALESCE(jasa_rs.tarif_jasa_rs, 0::double precision) + COALESCE(jasa_pelayanan.tarif_jasa_pelayanan, 0::double precision) AS "Total"
   FROM rawat_jalan
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.alamat_pasien,
            lkp_jeniskelamin.lookup_name AS jenis_kelamin
           FROM pasien_m a
             LEFT JOIN ( SELECT b.lookup_id,
                    b.lookup_name
                   FROM lookup_m b) lkp_jeniskelamin ON a.jeniskelamin::integer = lkp_jeniskelamin.lookup_id) pasien_m ON rawat_jalan.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON rawat_jalan.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON rawat_jalan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON rawat_jalan.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON rawat_jalan.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            string_agg(surat_keterangan_m.judul_surat::text, ', '::text) AS judul_surat
           FROM surat_keterangan_pasien_t a
             JOIN ( SELECT b.surat_keterangan_id,
                    b.judul_surat
                   FROM surat_keterangan_m b) surat_keterangan_m ON a.surat_keterangan_id = surat_keterangan_m.surat_keterangan_id
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id) surat_keterangan ON rawat_jalan.pendaftaran_id = surat_keterangan.pendaftaran_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tarif_tindakankomp AS tarif_jasa_rs
           FROM tindakankomponen_t a
          WHERE a.is_deleted = false AND a.komponentarif_id = 7) jasa_rs ON rawat_jalan.tindakanpelayanan_id = jasa_rs.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            sum(a.tarif_tindakankomp) AS tarif_jasa_pelayanan
           FROM tindakankomponen_t a
          WHERE a.is_deleted = false AND a.komponentarif_id <> 7
          GROUP BY a.tindakanpelayanan_id) jasa_pelayanan ON rawat_jalan.tindakanpelayanan_id = jasa_pelayanan.tindakanpelayanan_id
  ORDER BY rawat_jalan.tgl_pendaftaran;
