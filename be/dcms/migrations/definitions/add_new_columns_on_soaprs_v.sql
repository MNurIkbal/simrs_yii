CREATE VIEW "public"."soaprs_v" AS  SELECT 'RJ'::text AS tipe,
    soaprj_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    soaprj_t.ruangan_id,
    soaprj_t.pegawai_id,
    soaprj_t.tgl_soaprj,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.planning,
    soaprj_t.catatan_dokter,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_id,
    kelompokpegawai_m.kelompokpegawai_nama,
    ruangan_m.ruangan_nama,
    pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
    soaprj_t.soaprj_id,
    NULL::integer AS cppt_id,
    NULL::integer AS referred_id,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    soaprj_t.instruksi,
    true AS is_verifikasi,
    NULL::text AS catatan_perawat,
    soaprj_t.is_deleted,
    pegawai_m.nama_pegawai AS pegawai_verifikasi,
    soaprj_t.tgl_soaprj AS tgl_verifikasi,
    NULL::character varying AS pegawai_cppt,
    NULL::character varying AS pegawai_instruksi,
    NULL::integer AS pemberi_instruksi_id,
    false AS is_verifikasi_verbal,
    NULL::date AS tgl_verif_verbal,
    NULL::integer AS pegawai_verbal_id,
    NULL::character varying AS pegawai_verifikasi_verbal,
    NULL::integer AS dokteradmisi_id,
    NULL::character varying AS dokteradmisi_nama,
    false AS is_instruksi_pulang,
    pendaftaran_t.pasienadmisi_id,
    false AS is_lab,
    false AS is_rad,
    false AS is_reseptur,
    false AS is_konsul,
    false AS is_fisio,
    pendaftaran_t.no_pendaftaran,
    pasien.no_rekam_medik,
    spesialis_m.spesialis_id,
    spesialis_m.spesialis_nama,
    ruangan_m.instalasi_id,
    COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
    peg_updated.nama_pegawai AS pegawai_update_nama,
    soaprj_t.created_date,
    NULL::timestamp without time zone AS tgl_admisi,
    true AS is_icd_x,
    false AS is_verbal_order,
    NULL::json AS diagnosa_fungsi,
    NULL::json AS prosedur_kerja,
    NULL::text AS goal
   FROM soaprj_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pasien_id
           FROM pendaftaran_t a) pendaftaran_t ON soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik
           FROM pasien_m) pasien ON pendaftaran_t.pasien_id = pasien.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id,
            a.pendkualifikasi_id,
            a.spesialis_id
           FROM pegawai_m a) pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) log_updated ON COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_updated ON log_updated.pegawai_id = peg_updated.pegawai_id
UNION ALL
 SELECT 'RD'::text AS tipe,
    cppt_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    cppt_t.ruangan_id,
    cppt_t.pegawai_id,
    cppt_t.tgl_cppt AS tgl_soaprj,
    cppt_t.a_diag_utama,
    cppt_t.a_diag_penyerta,
    cppt_t.subject,
    cppt_t.object,
    cppt_t.planning,
    cppt_t.catatan_dokter,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_id,
    kelompokpegawai_m.kelompokpegawai_nama,
    ruangan_m.ruangan_nama,
    pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
    NULL::integer AS soaprj_id,
    cppt_t.cppt_id,
    cppt_t.referred_id,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    cppt_t.instruksi,
    cppt_t.is_verifikasi,
    cppt_t.catatan_perawat,
    cppt_t.is_deleted,
    pegawai_verif.nama_pegawai AS pegawai_verifikasi,
    cppt_t.tgl_verifikasi,
    pegawai_m.nama_pegawai AS pegawai_cppt,
    pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
    cppt_t.pemberi_instruksi_id,
    cppt_t.is_verifikasi_verbal,
    cppt_t.tgl_verif_verbal,
    cppt_t.pegawai_verbal_id,
    pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
    NULL::integer AS dokteradmisi_id,
    NULL::character varying AS dokteradmisi_nama,
    cppt_t.is_instruksi_pulang,
    pendaftaran_t.pasienadmisi_id,
        CASE COALESCE(lab.ct_lab, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_lab,
        CASE COALESCE(rad.ct_rad, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_rad,
        CASE COALESCE(reseptur.ct_reseptur, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_reseptur,
    false AS is_konsul,
    false AS is_fisio,
    pendaftaran_t.no_pendaftaran,
    pasien.no_rekam_medik,
    spesialis_m.spesialis_id,
    spesialis_m.spesialis_nama,
    ruangan_m.instalasi_id,
    COALESCE(cppt_t.last_modified_by, cppt_t.created_by) AS last_modified_by,
    peg_updated.nama_pegawai AS pegawai_update_nama,
    cppt_t.created_date,
    NULL::timestamp without time zone AS tgl_admisi,
    cppt_t.is_icd_x,
    cppt_t.is_verbal_order,
    NULL::json AS diagnosa_fungsi,
    NULL::json AS prosedur_kerja,
    NULL::text AS goal
   FROM cppt_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pasien_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik
           FROM pasien_m) pasien ON pendaftaran_t.pasien_id = pasien.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id,
            a.pendkualifikasi_id,
            a.spesialis_id
           FROM pegawai_m a) pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_verif ON cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_verif2 ON cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pemberi_instruksi ON cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_lab,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id,
                    a.ruangan_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m_1 ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id
          WHERE ruangan_m_1.instalasi_id = 4
          GROUP BY instruksi_t.cppt_id) lab ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = lab.cppt_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_rad,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id,
                    a.ruangan_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m_1 ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id
          WHERE ruangan_m_1.instalasi_id = 5
          GROUP BY instruksi_t.cppt_id) rad ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = rad.cppt_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_reseptur,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id
                   FROM reseptur_t a) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id
          GROUP BY instruksi_t.cppt_id) reseptur ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = reseptur.cppt_id
     LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) log_updated ON COALESCE(cppt_t.last_modified_by, cppt_t.created_by) = log_updated.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_updated ON log_updated.pegawai_id = peg_updated.pegawai_id
  WHERE cppt_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT 'RI'::text AS tipe,
    cppt_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    cppt_t.ruangan_id,
    cppt_t.pegawai_id,
    cppt_t.tgl_cppt AS tgl_soaprj,
    cppt_t.a_diag_utama,
    cppt_t.a_diag_penyerta,
    cppt_t.subject,
    cppt_t.object,
    cppt_t.planning,
    cppt_t.catatan_dokter,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_id,
    kelompokpegawai_m.kelompokpegawai_nama,
    ruangan_m.ruangan_nama,
    pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
    NULL::integer AS soaprj_id,
    cppt_t.cppt_id,
    cppt_t.referred_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    cppt_t.instruksi,
    cppt_t.is_verifikasi,
    cppt_t.catatan_perawat,
    cppt_t.is_deleted,
    pegawai_verif.nama_pegawai AS pegawai_verifikasi,
    cppt_t.tgl_verifikasi,
    pegawai_m.nama_pegawai AS pegawai_cppt,
    pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
    cppt_t.pemberi_instruksi_id,
    cppt_t.is_verifikasi_verbal,
    cppt_t.tgl_verif_verbal,
    cppt_t.pegawai_verbal_id,
    pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
    pasienadmisi_t.pegawai_id AS dokteradmisi_id,
    dokteradmisi.nama_pegawai AS dokteradmisi_nama,
    cppt_t.is_instruksi_pulang,
    pendaftaran_t.pasienadmisi_id,
        CASE COALESCE(lab.ct_lab, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_lab,
        CASE COALESCE(rad.ct_rad, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_rad,
        CASE COALESCE(reseptur.ct_reseptur, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_reseptur,
        CASE COALESCE(konsul.ct_konsul, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_konsul,
    false AS is_fisio,
    pendaftaran_t.no_pendaftaran,
    pasien.no_rekam_medik,
    spesialis_m.spesialis_id,
    spesialis_m.spesialis_nama,
    ruangan_m.instalasi_id,
    COALESCE(cppt_t.last_modified_by, cppt_t.created_by) AS last_modified_by,
    peg_updated.nama_pegawai AS pegawai_update_nama,
    cppt_t.created_date,
    pasienadmisi_t.tgl_admisi,
    cppt_t.is_icd_x,
    cppt_t.is_verbal_order,
    NULL::json AS diagnosa_fungsi,
    NULL::json AS prosedur_kerja,
    NULL::text AS goal
   FROM cppt_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pasien_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik
           FROM pasien_m) pasien ON pendaftaran_t.pasien_id = pasien.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id,
            a.pendkualifikasi_id,
            a.spesialis_id
           FROM pegawai_m a) pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.pegawai_id,
            a.tgl_admisi
           FROM pasienadmisi_t a) pasienadmisi_t ON cppt_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_verif ON cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_verif2 ON cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokteradmisi ON pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pemberi_instruksi ON cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_lab,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id,
                    a.ruangan_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m_1 ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id
          WHERE ruangan_m_1.instalasi_id = 4
          GROUP BY instruksi_t.cppt_id) lab ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = lab.cppt_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_rad,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id,
                    a.ruangan_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m_1 ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id
          WHERE ruangan_m_1.instalasi_id = 5
          GROUP BY instruksi_t.cppt_id) rad ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = rad.cppt_id
     LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_reseptur,
            instruksi_t.cppt_id
           FROM instruksi_t
             JOIN ( SELECT a.instruksi_id
                   FROM reseptur_t a) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id
          GROUP BY instruksi_t.cppt_id) reseptur ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = reseptur.cppt_id
     LEFT JOIN ( SELECT count(permintaankonsul_t.cppt_id) AS ct_konsul,
            permintaankonsul_t.cppt_id
           FROM permintaankonsul_t
          GROUP BY permintaankonsul_t.cppt_id) konsul ON COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = konsul.cppt_id
     LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) log_updated ON COALESCE(cppt_t.last_modified_by, cppt_t.created_by) = log_updated.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_updated ON log_updated.pegawai_id = peg_updated.pegawai_id
  WHERE cppt_t.pasienadmisi_id IS NOT NULL
UNION ALL
 SELECT 'RI-SOAPFISIO'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    ruangan_m.ruangan_id,
    soapfisioterapi_t.terapis_id AS pegawai_id,
    soapfisioterapi_t.tgl_soapfisioterapi AS tgl_soaprj,
    soapfisioterapi_t.a_diag_utama,
    soapfisioterapi_t.a_diag_penyerta,
    soapfisioterapi_t.subject,
    soapfisioterapi_t.object,
    soapfisioterapi_t.planning,
    soapfisioterapi_t.catatan_dokter,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_id,
    kelompokpegawai_m.kelompokpegawai_nama,
    ruangan_m.ruangan_nama,
    pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
    soapfisioterapi_t.soapfisioterapi_id AS soaprj_id,
    soapfisioterapi_t.soapfisioterapi_id AS cppt_id,
    NULL::integer AS referred_id,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    soapfisioterapi_t.instruksi,
    NULL::boolean AS is_verifikasi,
    NULL::text AS catatan_perawat,
    soapfisioterapi_t.is_edit AS is_deleted,
    NULL::character varying AS pegawai_verifikasi,
    NULL::timestamp without time zone AS tgl_verifikasi,
    NULL::character varying AS pegawai_cppt,
    NULL::character varying AS pegawai_instruksi,
    NULL::integer AS pemberi_instruksi_id,
    NULL::boolean AS is_verifikasi_verbal,
    NULL::timestamp without time zone AS tgl_verif_verbal,
    NULL::integer AS pegawai_verbal_id,
    NULL::character varying AS pegawai_verifikasi_verbal,
    NULL::integer AS dokteradmisi_id,
    NULL::character varying AS dokteradmisi_nama,
    false AS is_instruksi_pulang,
    pendaftaran_t.pasienadmisi_id,
    false AS is_lab,
    false AS is_rad,
    false AS is_reseptur,
    false AS is_konsul,
    true AS is_fisio,
    pendaftaran_t.no_pendaftaran,
    pasien.no_rekam_medik,
    spesialis_m.spesialis_id,
    spesialis_m.spesialis_nama,
    ruangan_m.instalasi_id,
    COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) AS last_modified_by,
    peg_updated.nama_pegawai AS pegawai_update_nama,
    soapfisioterapi_t.created_date,
    pasienadmisi_t.tgl_admisi,
    true AS is_icd_x,
    false AS is_verbal_order,
    soapfisioterapi_t.diagnosa_fungsi,
    soapfisioterapi_t.prosedur_kerja,
    soapfisioterapi_t.goal
   FROM soapfisioterapi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.kamarruangan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.ruangan_id
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON soapfisioterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN ( SELECT a.kamarruangan_id
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ( SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik
           FROM pasien_m) pasien ON pendaftaran_t.pasien_id = pasien.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id,
            a.pendkualifikasi_id,
            a.spesialis_id
           FROM pegawai_m a) pegawai_m ON soapfisioterapi_t.terapis_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id
           FROM pegawai_m a) dokteradmisi ON pendaftaran_t.pegawai_id = dokteradmisi.pegawai_id
     LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) log_updated ON COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) = log_updated.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_updated ON log_updated.pegawai_id = peg_updated.pegawai_id
  WHERE soapfisioterapi_t.tipe_instalasi::text = '3'::text
UNION ALL
 SELECT 'RJ-SOAPFISIO'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.ruangan_id,
    soapfisioterapi_t.terapis_id AS pegawai_id,
    soapfisioterapi_t.tgl_soapfisioterapi AS tgl_soaprj,
    soapfisioterapi_t.a_diag_utama,
    soapfisioterapi_t.a_diag_penyerta,
    soapfisioterapi_t.subject,
    soapfisioterapi_t.object,
    soapfisioterapi_t.planning,
    soapfisioterapi_t.catatan_dokter,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_id,
    kelompokpegawai_m.kelompokpegawai_nama,
    ruangan_m.ruangan_nama,
    pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
    soapfisioterapi_t.soapfisioterapi_id AS soaprj_id,
    soapfisioterapi_t.soapfisioterapi_id AS cppt_id,
    NULL::integer AS referred_id,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    soapfisioterapi_t.instruksi,
    NULL::boolean AS is_verifikasi,
    NULL::text AS catatan_perawat,
    soapfisioterapi_t.is_edit AS is_deleted,
    NULL::character varying AS pegawai_verifikasi,
    NULL::timestamp without time zone AS tgl_verifikasi,
    NULL::character varying AS pegawai_cppt,
    NULL::character varying AS pegawai_instruksi,
    NULL::integer AS pemberi_instruksi_id,
    NULL::boolean AS is_verifikasi_verbal,
    NULL::timestamp without time zone AS tgl_verif_verbal,
    NULL::integer AS pegawai_verbal_id,
    NULL::character varying AS pegawai_verifikasi_verbal,
    NULL::integer AS dokteradmisi_id,
    NULL::character varying AS dokteradmisi_nama,
    false AS is_instruksi_pulang,
    pendaftaran_t.pasienadmisi_id,
    false AS is_lab,
    false AS is_rad,
    false AS is_reseptur,
    false AS is_konsul,
    true AS is_fisio,
    pendaftaran_t.no_pendaftaran,
    pasien.no_rekam_medik,
    spesialis_m.spesialis_id,
    spesialis_m.spesialis_nama,
    ruangan_m.instalasi_id,
    COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) AS last_modified_by,
    peg_updated.nama_pegawai AS pegawai_update_nama,
    soapfisioterapi_t.created_date,
    NULL::timestamp without time zone AS tgl_admisi,
    true AS is_icd_x,
    false AS is_verbal_order,
    soapfisioterapi_t.diagnosa_fungsi,
    soapfisioterapi_t.prosedur_kerja,
    soapfisioterapi_t.goal
   FROM soapfisioterapi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.ruangan_id,
            a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik
           FROM pasien_m) pasien ON pendaftaran_t.pasien_id = pasien.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kelompokpegawai_id,
            a.pendkualifikasi_id,
            a.spesialis_id
           FROM pegawai_m a) pegawai_m ON soapfisioterapi_t.terapis_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id
           FROM pegawai_m a) dokteradmisi ON pendaftaran_t.pegawai_id = dokteradmisi.pegawai_id
     LEFT JOIN ( SELECT a.spesialis_id,
            a.spesialis_nama
           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) log_updated ON COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) = log_updated.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_updated ON log_updated.pegawai_id = peg_updated.pegawai_id
  WHERE soapfisioterapi_t.tipe_instalasi::text = '1'::text AND soapfisioterapi_t.is_deleted = false;
