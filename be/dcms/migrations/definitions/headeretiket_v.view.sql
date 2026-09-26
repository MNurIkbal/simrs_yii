-- public.headeretiket_v source

CREATE OR REPLACE VIEW public.headeretiket_v
AS SELECT 'a'::text AS tipe,
    penjualanresep_t.noresep AS no_resep,
    reseptur_t.noresep AS no_reseptur,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE NULL::text
        END AS jeniskelamin
   FROM reseptur_t
     JOIN ( SELECT a.penjualanresep_id,
            a.reseptur_id,
            a.pegawai_id,
            a.noresep,
            a.tglpenjualan,
            a.status_bayar,
            a.status_reseptur,
            a.additional_data,
            a.log_user,
            a.pegawai_menyerahkan_id,
            a.tgl_menyerahkan,
            pembayaranpelayanan_t.pembayaran_id
           FROM penjualanresep_t a
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.penjualanresep_id = a.penjualanresep_id AND pembayaranpelayanan_t.is_deleted IS FALSE
          WHERE a.is_deleted IS TRUE AND a.status_reseptur = 432 OR a.is_deleted IS FALSE) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN a.pasienadmisi_id IS NULL THEN a.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a1.pasienadmisi_id,
                    a1.penjamin_id,
                    a1.kamarruangan_id,
                    a1.kamartempattidur_id
                   FROM pasienadmisi_t a1) pasienadmisi_t ON a.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a1.kamarruangan_id,
                    a1.kamarruangan_nokamar
                   FROM kamarruangan_m a1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT a1.kamartempattidur_id,
                    a1.no_tempattidur
                   FROM kamartempattidur_m a1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.namadepan,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON ruangan_m.ruangan_id = reseptur_t.ruanganreseptur_id AND ruangan_m.instalasi_id = 1
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
UNION ALL
 SELECT 'b'::text AS tipe,
    penjualanresep_t.noresep AS no_resep,
    reseptur_t.noresep AS no_reseptur,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE '-'::text
        END AS jeniskelamin
   FROM reseptur_t
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id
     JOIN ( SELECT b.pendaftaran_id,
            b.pasienadmisi_id,
            b.pasien_id,
            b.no_pendaftaran,
            b.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN b.pasienadmisi_id IS NULL THEN b.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
           FROM pendaftaran_t b
             LEFT JOIN ( SELECT b1.pasienadmisi_id,
                    b1.penjamin_id,
                    b1.kamarruangan_id,
                    b1.kamartempattidur_id
                   FROM pasienadmisi_t b1) pasienadmisi_t ON b.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT b1.kamarruangan_id,
                    b1.kamarruangan_nokamar
                   FROM kamarruangan_m b1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT b1.kamartempattidur_id,
                    b1.no_tempattidur
                   FROM kamartempattidur_m b1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.tanggal_lahir,
            b.no_rekam_medik,
            b.namadepan,
            b.jeniskelamin
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON ruangan_m.ruangan_id = reseptur_t.ruanganreseptur_id AND ruangan_m.instalasi_id <> 1
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
UNION ALL
 SELECT 'c'::text AS tipe,
    penjualanresep_t.noresep AS no_resep,
    NULL::character varying AS no_reseptur,
    pasien_m.no_rekam_medik AS no_rm,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien)::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', karyawan.nama_pegawai)::character varying
            ELSE NULL::character varying
        END AS nama_pasien,
        CASE
            WHEN penjualanresep_t.pasien_id IS NOT NULL THEN pasien_m.tanggal_lahir
            ELSE NULL::date
        END AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
        CASE
            WHEN pasien_m.jeniskelamin::text = '15'::text THEN 'M'::text
            WHEN pasien_m.jeniskelamin::text = '16'::text THEN 'F'::text
            ELSE '-'::text
        END AS jeniskelamin
   FROM penjualanresep_t
     LEFT JOIN ( SELECT c.pendaftaran_id,
            c.pasienadmisi_id,
            c.pasien_id,
            c.no_pendaftaran,
            c.instalasi_id,
            c.umur,
            kamar.kamarruangan_nokamar,
            bed.no_tempattidur,
                CASE
                    WHEN c.pasienadmisi_id IS NULL THEN c.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id,
                CASE
                    WHEN c.pasienadmisi_id IS NULL THEN c.ruangan_id
                    ELSE pasienadmisi_t.ruangan_id
                END AS ruangan_id
           FROM pendaftaran_t c
             LEFT JOIN ( SELECT c1.pasienadmisi_id,
                    c1.penjamin_id,
                    c1.kamarruangan_id,
                    c1.kamartempattidur_id,
                    c1.ruangan_id
                   FROM pasienadmisi_t c1) pasienadmisi_t ON c.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT c1.kamarruangan_id,
                    c1.kamarruangan_nokamar
                   FROM kamarruangan_m c1) kamar ON pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id
             LEFT JOIN ( SELECT c1.kamartempattidur_id,
                    c1.no_tempattidur
                   FROM kamartempattidur_m c1) bed ON pasienadmisi_t.kamartempattidur_id = bed.kamartempattidur_id) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT c.pasien_id,
            c.nama_pasien,
            c.tanggal_lahir,
            c.no_rekam_medik,
            c.namadepan,
            c.jeniskelamin
           FROM pasien_m c) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai,
            c.gelardepan
           FROM pegawai_m c) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
  WHERE penjualanresep_t.reseptur_id IS NULL;