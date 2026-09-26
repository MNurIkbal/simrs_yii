-- public.laporancatatankeperawatan_v source

CREATE OR REPLACE VIEW public.laporancatatankeperawatan_v
AS  SELECT pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dpjp,
    lkp_jk.lookup_name AS jenis_kelamin,
        CASE
            WHEN catatankeperawatan_t.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
            ELSE
            CASE
                WHEN pendaftaran_t.instalasi_id = 2 THEN 'IGD'::text
                WHEN pendaftaran_t.instalasi_id = 1 THEN 'OPD'::text
                ELSE NULL::text
            END
        END AS unit,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    COALESCE(alergi_asmed.riwayat_alergi, alergi.riwayat_alergi) AS alergi,
    catatankeperawatan_t.tgl_catatan::date AS tanggal,
    catatankeperawatan_t.tgl_catatan::time without time zone AS jam,
    catatankeperawatan_t.kegiatan_perawat,
    catatankeperawatan_t.catatan,
    perawat.nama_pegawai,
    petugas_input.nama_pegawai AS user_input,
    petugas_update.nama_pegawai AS user_update,
    petugas_hapus.nama_pegawai AS user_hapus
   FROM catatankeperawatan_t
     JOIN pendaftaran_t ON catatankeperawatan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pegawai_m ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m perawat ON catatankeperawatan_t.pegawai_id = perawat.pegawai_id AND catatankeperawatan_t.is_deleted IS FALSE
     LEFT JOIN lookup_m lkp_jk ON pasien_m.jeniskelamin::integer = lkp_jk.lookup_id
     LEFT JOIN ruangan_m ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
     LEFT JOIN kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.nama_pegawai,
            a.loginpemakai_id
           FROM loginpemakai_k a
             JOIN pegawai_m b ON a.pegawai_id = b.pegawai_id) petugas_input ON catatankeperawatan_t.created_by = petugas_input.loginpemakai_id
     LEFT JOIN ( SELECT b.nama_pegawai,
            a.loginpemakai_id
           FROM loginpemakai_k a
             JOIN pegawai_m b ON a.pegawai_id = b.pegawai_id) petugas_update ON catatankeperawatan_t.last_modified_by = petugas_update.loginpemakai_id
     LEFT JOIN ( SELECT b.nama_pegawai,
            a.loginpemakai_id
           FROM loginpemakai_k a
             JOIN pegawai_m b ON a.pegawai_id = b.pegawai_id) petugas_hapus ON catatankeperawatan_t.deleted_by = petugas_hapus.loginpemakai_id
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_alergi, ', '::text) AS riwayat_alergi
           FROM ( SELECT pendaftaran_t_1.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenperawatrd_t.alergi_obat, ', ')
                        END,
                        CASE
                            WHEN COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenperawatrd_t.alergi_lainnya, ', ')
                        END) AS riwayat_alergi
                   FROM asesmenperawatrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t_1 ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) <> ''::text OR COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) <> ''::text
                UNION ALL
                 SELECT pendaftaran_t_1.pasien_id,
                    concat(
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ', ')
                        END,
                        CASE
                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text, ''::text) = ''::text THEN ''::text
                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text)
                        END) AS riwayat_alergi
                   FROM asesmenawal_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t_1 ON asesmenawal_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE asesmenawal_t.additional_data IS NOT NULL) x
          GROUP BY x.pasien_id) alergi ON pendaftaran_t.pasien_id = alergi.pasien_id
     LEFT JOIN ( SELECT x.pasien_id,
            string_agg(x.riwayat_alergi, ', '::text) AS riwayat_alergi
           FROM ( SELECT pendaftaran_t_1.pasien_id,
                    asesmenmedisrd_t.alergi AS riwayat_alergi
                   FROM asesmenmedisrd_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t_1 ON asesmenmedisrd_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE COALESCE(asesmenmedisrd_t.alergi, ''::text) <> ''::text
                UNION ALL
                 SELECT pendaftaran_t_1.pasien_id,
                    asesmenmedis_t.r_alergiobat AS riwayat_alergi
                   FROM asesmenmedis_t
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id
                           FROM pendaftaran_t a) pendaftaran_t_1 ON asesmenmedis_t.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE asesmenmedis_t.r_alergiobat IS NOT NULL) x
          GROUP BY x.pasien_id) alergi_asmed ON pendaftaran_t.pasien_id = alergi_asmed.pasien_id
  ORDER BY catatankeperawatan_t.tgl_catatan;