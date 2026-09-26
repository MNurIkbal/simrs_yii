CREATE VIEW "public"."soapfisioterapiranap_v" AS  SELECT 'SOAP Fisio-RJ'::text AS tipe,
    soapfisioterapi_t.soapfisioterapi_id,
    soapfisioterapi_t.pendaftaran_id,
    soapfisioterapi_t.pasien_id,
    soapfisioterapi_t.terapis_id,
    soapfisioterapi_t.tgl_soapfisioterapi,
    ruangan_m.ruangan_id,
    pendaftaran_t.instalasi_id,
    array_to_string(array_agg(soapfisioterapidetail_t.pasienmasukpenunjang_id), ','::text) AS pasienmasukpenunjang_id,
    array_to_string(array_agg(soapfisioterapidetail_t.programterapi_id), ','::text) AS programterapi_id,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin_nama,
    pegawai_m.nama_pegawai AS terapis_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    instalasi_m.instalasi_nama,
    soapfisioterapi_t.subject,
    soapfisioterapi_t.object,
    soapfisioterapi_t.assesment,
    soapfisioterapi_t.planning,
    soapfisioterapi_t.a_diag_utama,
    soapfisioterapi_t.a_diag_penyerta,
    soapfisioterapi_t.catatan_dokter,
    soapfisioterapi_t.instruksi,
    soapfisioterapi_t.last_modified_date,
    soapfisioterapi_t.last_modified_by,
    soapfisioterapi_t.is_edit,
    soapfisioterapi_t.diagnosa_fungsi,
    soapfisioterapi_t.prosedur_kerja,
    soapfisioterapi_t.goal,
    peg_edit.nama_pegawai AS last_modified_by_name,
    true AS is_fisio
   FROM soapfisioterapi_t
     LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.programterapi_id,
            a.pasienmasukpenunjang_id
           FROM soapfisioterapidetail_t a) soapfisioterapidetail_t ON soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON soapfisioterapi_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON soapfisioterapi_t.terapis_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.ruangan_id,
            a.instalasi_id,
            a.tgl_pendaftaran,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.programterapi_id,
            a.ruangan_id
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON soapfisioterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN loginpemakai_k pegawai_edit ON soapfisioterapi_t.last_modified_by = pegawai_edit.loginpemakai_id
     LEFT JOIN pegawai_m peg_edit ON pegawai_edit.pegawai_id = peg_edit.pegawai_id
  WHERE soapfisioterapi_t.is_active = true AND soapfisioterapi_t.is_deleted = false AND soapfisioterapi_t.tipe_instalasi::text = '1'::text
  GROUP BY soapfisioterapi_t.soapfisioterapi_id, soapfisioterapi_t.pendaftaran_id, soapfisioterapi_t.pasien_id, soapfisioterapi_t.terapis_id, soapfisioterapi_t.tgl_soapfisioterapi, ruangan_m.ruangan_id, pendaftaran_t.instalasi_id, pasien_m.nama_pasien, pasien_m.tanggal_lahir, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kamarruangan_m.kamarruangan_nokamar, kamartempattidur_m.no_tempattidur, instalasi_m.instalasi_nama, soapfisioterapi_t.subject, soapfisioterapi_t.object, soapfisioterapi_t.assesment, soapfisioterapi_t.planning, (soapfisioterapi_t.a_diag_utama::text), (soapfisioterapi_t.a_diag_penyerta::text), soapfisioterapi_t.catatan_dokter, soapfisioterapi_t.instruksi, soapfisioterapi_t.last_modified_date, soapfisioterapi_t.last_modified_by, soapfisioterapi_t.is_edit, peg_edit.nama_pegawai
UNION ALL
 SELECT 'SOAP Fisio-RI'::text AS tipe,
    soapfisioterapi_t.soapfisioterapi_id,
    soapfisioterapi_t.pendaftaran_id,
    soapfisioterapi_t.pasien_id,
    soapfisioterapi_t.terapis_id,
    soapfisioterapi_t.tgl_soapfisioterapi,
    ruangan_m.ruangan_id,
    pendaftaran_t.instalasi_id,
    array_to_string(array_agg(soapfisioterapidetail_t.pasienmasukpenunjang_id), ','::text) AS pasienmasukpenunjang_id,
    array_to_string(array_agg(soapfisioterapidetail_t.programterapi_id), ','::text) AS programterapi_id,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin_nama,
    pegawai_m.nama_pegawai AS terapis_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    instalasi_m.instalasi_nama,
    soapfisioterapi_t.subject,
    soapfisioterapi_t.object,
    soapfisioterapi_t.assesment,
    soapfisioterapi_t.planning,
    soapfisioterapi_t.a_diag_utama,
    soapfisioterapi_t.a_diag_penyerta,
    soapfisioterapi_t.catatan_dokter,
    soapfisioterapi_t.instruksi,
    soapfisioterapi_t.last_modified_date,
    soapfisioterapi_t.last_modified_by,
    soapfisioterapi_t.is_edit,
    soapfisioterapi_t.diagnosa_fungsi,
    soapfisioterapi_t.prosedur_kerja,
    soapfisioterapi_t.goal,
    peg_edit.nama_pegawai AS last_modified_by_name,
    true AS is_fisio
   FROM soapfisioterapi_t
     LEFT JOIN ( SELECT a.soapfisioterapi_id,
            a.programterapi_id,
            a.pasienmasukpenunjang_id
           FROM soapfisioterapidetail_t a) soapfisioterapidetail_t ON soapfisioterapi_t.soapfisioterapi_id = soapfisioterapidetail_t.soapfisioterapi_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON soapfisioterapi_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON soapfisioterapi_t.terapis_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.ruangan_id,
            a.instalasi_id,
            a.tgl_pendaftaran,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON soapfisioterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienmasukpenunjang_id,
            a.programterapi_id,
            a.ruangan_id
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON soapfisioterapi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN loginpemakai_k pegawai_edit ON soapfisioterapi_t.last_modified_by = pegawai_edit.loginpemakai_id
     LEFT JOIN pegawai_m peg_edit ON pegawai_edit.pegawai_id = peg_edit.pegawai_id
  WHERE soapfisioterapi_t.is_active = true AND soapfisioterapi_t.is_deleted = false AND soapfisioterapi_t.tipe_instalasi::text = '3'::text
  GROUP BY soapfisioterapi_t.soapfisioterapi_id, soapfisioterapi_t.pendaftaran_id, soapfisioterapi_t.pasien_id, soapfisioterapi_t.terapis_id, soapfisioterapi_t.tgl_soapfisioterapi, ruangan_m.ruangan_id, pendaftaran_t.instalasi_id, pasien_m.nama_pasien, pasien_m.tanggal_lahir, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kamarruangan_m.kamarruangan_nokamar, kamartempattidur_m.no_tempattidur, instalasi_m.instalasi_nama, soapfisioterapi_t.subject, soapfisioterapi_t.object, soapfisioterapi_t.assesment, soapfisioterapi_t.planning, (soapfisioterapi_t.a_diag_utama::text), (soapfisioterapi_t.a_diag_penyerta::text), soapfisioterapi_t.catatan_dokter, soapfisioterapi_t.instruksi, soapfisioterapi_t.last_modified_date, soapfisioterapi_t.last_modified_by, soapfisioterapi_t.is_edit, peg_edit.nama_pegawai
UNION ALL
 SELECT 'SOAP RJ'::text AS tipe,
    soaprj_t.soaprj_id AS soapfisioterapi_id,
    soaprj_t.pendaftaran_id,
    soaprj_t.pasien_id,
    soaprj_t.pegawai_id AS terapis_id,
    soaprj_t.tgl_soaprj AS tgl_soapfisioterapi,
    ruangan_m.ruangan_id,
    pendaftaran_t.instalasi_id,
    NULL::text AS pasienmasukpenunjang_id,
    NULL::text AS programterapi_id,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin_nama,
    pegawai_m.nama_pegawai AS terapis_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    instalasi_m.instalasi_nama,
    soaprj_t.subject,
    soaprj_t.object,
    NULL::text AS assesment,
    soaprj_t.planning,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    soaprj_t.catatan_dokter,
    soaprj_t.instruksi,
    soaprj_t.last_modified_date,
    soaprj_t.last_modified_by,
    soaprj_t.is_deleted AS is_edit,
    NULL::json AS diagnosa_fungsi,
    NULL::json AS prosedur_kerja,
    NULL::text AS goal,
    peg_edit.nama_pegawai AS last_modified_by_name,
    false AS is_fisio
   FROM soaprj_t
     JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id,
            a.instalasi_id,
            a.pasien_id,
            a.status_periksa
           FROM pendaftaran_t a) pendaftaran_t ON soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.nama_pasien,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_periksa::integer = look_statuspasien.lookup_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) pegawai_edit ON soaprj_t.last_modified_by = pegawai_edit.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_edit ON pegawai_edit.pegawai_id = peg_edit.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
UNION ALL
 SELECT 'SOAP RD/RI'::text AS tipe,
    cppt_t.cppt_id AS soapfisioterapi_id,
    cppt_t.pendaftaran_id,
    cppt_t.pasien_id,
    cppt_t.pegawai_id AS terapis_id,
    cppt_t.tgl_cppt AS tgl_soapfisioterapi,
    ruangan_m.ruangan_id,
    pendaftaran_t.instalasi_id,
    NULL::text AS pasienmasukpenunjang_id,
    NULL::text AS programterapi_id,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin_nama,
    pegawai_m.nama_pegawai AS terapis_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    instalasi_m.instalasi_nama,
    cppt_t.subject,
    cppt_t.object,
    NULL::text AS assesment,
    cppt_t.planning,
    cppt_t.a_diag_utama,
    cppt_t.a_diag_penyerta,
    cppt_t.catatan_dokter,
    cppt_t.instruksi,
    cppt_t.last_modified_date,
    cppt_t.last_modified_by,
    cppt_t.is_deleted AS is_edit,
    NULL::json AS diagnosa_fungsi,
    NULL::json AS prosedur_kerja,
    NULL::text AS goal,
    peg_edit.nama_pegawai AS last_modified_by_name,
    false AS is_fisio
   FROM cppt_t
     JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id,
            a.instalasi_id,
            a.pasien_id,
            a.status_periksa,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_ranap
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.nama_pasien,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspasien ON COALESCE(pasienadmisi_t.status_ranap, pendaftaran_t.status_periksa::integer) = look_statuspasien.lookup_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) pegawai_edit ON cppt_t.last_modified_by = pegawai_edit.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_edit ON pegawai_edit.pegawai_id = peg_edit.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id;