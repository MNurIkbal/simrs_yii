-- public.laporanpenyetoranrawatdarurat_v source

CREATE OR REPLACE VIEW public.laporanpenyetoranrawatdarurat_v
AS WITH rawat_darurat AS (
         SELECT 'Kunjungan'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
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
                  WHERE a.is_deleted = false AND a.pasienadmisi_id IS NULL AND a.tindakansudahbayar_id IS NOT NULL) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
          WHERE (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL AND pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NULL
        )
 SELECT rawat_darurat.tgl_pendaftaran AS "Tanggal Kunjungan",
    pasien_m.no_rekam_medik AS "No RM",
    pasien_m.nama_pasien AS "Nama Pasien",
    penjamin_m.penjamin_nama AS "Penjamin",
    rawat_darurat.umur AS "Umur",
    pasien_m.jenis_kelamin AS "Jenis Kelamin",
    pasien_m.alamat_pasien AS "Alamat",
    ruangan_m.ruangan_nama AS "Poli",
    pegawai_m.nama_pegawai AS "Dokter",
    daftartindakan_m.daftartindakan_nama AS "Tindakan",
    surat_keterangan.judul_surat AS "Surat Keterangan",
    COALESCE(jasa_rs.tarif_jasa_rs, 0::double precision) AS "Jasa RS",
    COALESCE(jasa_pelayanan.tarif_jasa_pelayanan, 0::double precision) AS "Jasa Pelayanan",
    COALESCE(jasa_rs.tarif_jasa_rs, 0::double precision) + COALESCE(jasa_pelayanan.tarif_jasa_pelayanan, 0::double precision) AS "Total"
   FROM rawat_darurat
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.alamat_pasien,
            lkp_jeniskelamin.lookup_name AS jenis_kelamin
           FROM pasien_m a
             LEFT JOIN ( SELECT b.lookup_id,
                    b.lookup_name
                   FROM lookup_m b) lkp_jeniskelamin ON a.jeniskelamin::integer = lkp_jeniskelamin.lookup_id) pasien_m ON rawat_darurat.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON rawat_darurat.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON rawat_darurat.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON rawat_darurat.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON rawat_darurat.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            string_agg(surat_keterangan_m.judul_surat::text, ', '::text) AS judul_surat
           FROM surat_keterangan_pasien_t a
             JOIN ( SELECT b.surat_keterangan_id,
                    b.judul_surat
                   FROM surat_keterangan_m b) surat_keterangan_m ON a.surat_keterangan_id = surat_keterangan_m.surat_keterangan_id
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id) surat_keterangan ON rawat_darurat.pendaftaran_id = surat_keterangan.pendaftaran_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tarif_tindakankomp AS tarif_jasa_rs
           FROM tindakankomponen_t a
          WHERE a.is_deleted = false AND a.komponentarif_id = 7) jasa_rs ON rawat_darurat.tindakanpelayanan_id = jasa_rs.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            sum(a.tarif_tindakankomp) AS tarif_jasa_pelayanan
           FROM tindakankomponen_t a
          WHERE a.is_deleted = false AND a.komponentarif_id <> 7
          GROUP BY a.tindakanpelayanan_id) jasa_pelayanan ON rawat_darurat.tindakanpelayanan_id = jasa_pelayanan.tindakanpelayanan_id
  ORDER BY rawat_darurat.tgl_pendaftaran;
