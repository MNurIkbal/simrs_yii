-- public.infoprogramterapiapprove_v source

CREATE OR REPLACE VIEW public.infoprogramterapiapprove_v
AS SELECT programterapi_t.programterapi_id,
    max(programterapiapprove_t.created_date) AS tanggal_perubahan,
    programterapiapprove_t.programterapiapprove_id,
    programterapi_t.pendaftaran_id,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    string_agg(DISTINCT daftartindakan_lama.daftartindakan_nama::text, ', '::text) AS tindakan_lama,
    string_agg(DISTINCT daftartindakan_terbaru.daftartindakan_nama::text, ', '::text) AS tindakan_baru,
    programterapiapprove_t.is_approve AS is_approve_edit,
    programterapiapprove_t.is_deleted,
    programterapiapprove_t.is_active,
        CASE
            WHEN programterapiapprove_t.is_approve = true THEN 'disetujui'::text
            WHEN programterapiapprove_t.is_deleted = true AND programterapiapprove_t.is_active THEN 'ditolak'::text
            ELSE 'pengajuan'::text
        END AS status_approval
   FROM programterapi_t
     JOIN ( SELECT a.programterapi_id,
            a.programterapidetail_id,
            a.daftartindakan_id
           FROM programterapidetail_t a) programterapidetail_t ON programterapidetail_t.programterapi_id = programterapi_t.programterapi_id
     JOIN ( SELECT a.programterapiapprove_id,
            a.programterapi_id,
            a.created_date,
            a.is_approve,
            a.is_active,
            a.is_deleted
           FROM programterapiapprove_t a) programterapiapprove_t ON programterapiapprove_t.programterapi_id = programterapi_t.programterapi_id
     JOIN ( SELECT a.programterapiapprove_id,
            a.programterapidetailapprove_id,
            a.daftartindakan_id,
            a.created_date,
            a.is_approve_edit,
            a.is_deleted,
            a.is_active,
            a.programterapi_deleted
           FROM programterapidetailapprove_t a) programterapidetailapprove_t ON programterapidetailapprove_t.programterapiapprove_id = programterapiapprove_t.programterapiapprove_id AND programterapidetailapprove_t.is_active IS TRUE AND programterapidetailapprove_t.programterapi_deleted IS FALSE
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_terbaru ON daftartindakan_terbaru.daftartindakan_id = programterapidetailapprove_t.daftartindakan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_lama ON daftartindakan_lama.daftartindakan_id = programterapidetail_t.daftartindakan_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = programterapi_t.pasien_id
  WHERE programterapi_t.status_program_fisio::integer <> 1172
  GROUP BY programterapi_t.programterapi_id, programterapiapprove_t.programterapiapprove_id, programterapi_t.pendaftaran_id, pasien_m.pasien_id, pasien_m.nama_pasien, pasien_m.no_rekam_medik, programterapiapprove_t.is_approve, programterapiapprove_t.is_deleted, programterapiapprove_t.is_active;