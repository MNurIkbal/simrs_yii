CREATE OR REPLACE VIEW public.dokumeneklaim_v
AS SELECT dokumen.type,
    dokumen.pendaftaran_id,
    dokumen.nama_dokumen,
    dokumen.dokumen_id,
    dokumen.status,
    dokumen.latest_unduh
   FROM ( SELECT 'konfig_dokumen'::text AS jenis,
            dokumeneklaim_r.type_dokumen AS type,
            dokumeneklaim_r.pendaftaran_id,
            konfigunduhdokumen_k.nama_dokumen,
            konfigunduhdokumen_k.konfig_dokumen_id AS dokumen_id,
            dokumeneklaim_r.status,
            dokumeneklaim_r.created_date AS latest_unduh
           FROM konfigunduhdokumen_k
             LEFT JOIN ( SELECT a.dokumen_id,
                    a.type_dokumen,
                    a.pendaftaran_id,
                    a.status,
                    a.created_date
                   FROM dokumeneklaim_r a
                  WHERE a.type_dokumen::text = 'konfig_dokumen'::text AND a.is_deleted = false AND a.is_active = true AND a.status IS NOT NULL) dokumeneklaim_r ON konfigunduhdokumen_k.konfig_dokumen_id = dokumeneklaim_r.dokumen_id
          WHERE konfigunduhdokumen_k.is_deleted = false AND konfigunduhdokumen_k.is_active = true AND konfigunduhdokumen_k.is_eclaim = true
        UNION ALL
         SELECT 'surat_keterangan'::text AS jenis,
            dokumeneklaim_r.type_dokumen AS type,
            dokumeneklaim_r.pendaftaran_id,
            surat_keterangan.judul_surat AS nama_dokumen,
            dokumeneklaim_r.dokumen_id,
            dokumeneklaim_r.status,
            dokumeneklaim_r.created_date AS latest_unduh
           FROM dokumeneklaim_r
             JOIN ( SELECT a.surat_keterangan_id,
                    a.judul_surat
                   FROM surat_keterangan_m a
                     JOIN ( SELECT b.surat_keterangan_id
                           FROM surat_keterangan_pasien_t b
                          WHERE b.is_eklaim = true) keterangan_pasien ON a.surat_keterangan_id = keterangan_pasien.surat_keterangan_id
                  WHERE a.is_deleted = false AND a.is_active = true
                  GROUP BY a.surat_keterangan_id, a.judul_surat) surat_keterangan ON dokumeneklaim_r.dokumen_id = surat_keterangan.surat_keterangan_id
          WHERE dokumeneklaim_r.type_dokumen::text = 'surat_keterangan'::text AND dokumeneklaim_r.is_deleted = false AND dokumeneklaim_r.is_active = true
        UNION ALL
         SELECT 'upload_dokumen'::text AS jenis,
            dokumeneklaim_r.type_dokumen AS type,
            dokumeneklaim_r.pendaftaran_id,
            COALESCE(dokumen_upload.nama_dokumen_freetext::character varying, dokumen_upload.nama_dokumen) AS nama_dokumen,
            dokumeneklaim_r.dokumen_id,
            dokumeneklaim_r.status,
            dokumeneklaim_r.created_date AS latest_unduh
           FROM dokumeneklaim_r
             JOIN ( SELECT a.dokumenupload_id,
                    a.nama_dokumen_freetext,
                    dokumen_m.nama_dokumen
                   FROM dokumenupload_t a
                     LEFT JOIN ( SELECT b.dokumen_id,
                            b.nama_dokumen
                           FROM dokumen_m b) dokumen_m ON a.dokumen_id = dokumen_m.dokumen_id) dokumen_upload ON dokumeneklaim_r.dokumen_id = dokumen_upload.dokumenupload_id
          WHERE dokumeneklaim_r.type_dokumen::text = 'upload_dokumen'::text AND dokumeneklaim_r.is_deleted = false AND dokumeneklaim_r.is_active = true) dokumen;