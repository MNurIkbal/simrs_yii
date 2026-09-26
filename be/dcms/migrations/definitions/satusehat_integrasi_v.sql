CREATE OR REPLACE VIEW public.satusehat_integrasi_v
AS SELECT satusehat_integrasi_t.id,
    satusehat_integrasi_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    satusehat_integrasi_t.satusehat_id,
    satusehat_integrasi_t.type,
    satusehat_integrasi_t.state,
    satusehat_integrasi_t.is_sent,
    satusehat_integrasi_t.payload,
    satusehat_integrasi_t.id_sync_sercon,
    satusehat_integrasi_t.sync_response,
    satusehat_integrasi_t.created_date AS tgl_sync,
    satusehat_integrasi_t.created_by,
    satusehat_integrasi_t.modified_count,
    satusehat_integrasi_t.last_modified_date,
    satusehat_integrasi_t.last_modified_by,
    satusehat_integrasi_t.is_deleted,
    satusehat_integrasi_t.is_active,
    satusehat_integrasi_t.deleted_date,
    satusehat_integrasi_t.deleted_by,
    satusehat_integrasi_t.additional_id,
        case
            WHEN satusehat_integrasi_t.sync_response::jsonb -> 'issue' @> '[{}]' THEN 'Gagal'::text
            WHEN satusehat_integrasi_t.is_sent = false THEN 'Gagal'::text
            WHEN satusehat_integrasi_t.is_sent = true AND (satusehat_integrasi_t.satusehat_id IS NULL OR satusehat_integrasi_t.satusehat_id = '--'::text) AND satusehat_integrasi_t.sync_response IS NULL OR satusehat_integrasi_t.sync_response = '[]'::text THEN 'Diproses'::text
            WHEN satusehat_integrasi_t.is_sent AND satusehat_integrasi_t.satusehat_id IS NOT NULL AND satusehat_integrasi_t.satusehat_id <> '--'::text THEN 'Selesai'::text
            ELSE 'Gagal'::text
        END AS status_integrasi,
    satusehat_integrasi_t.tgl_resend
   FROM satusehat_integrasi_t
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON satusehat_integrasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id;
 
 