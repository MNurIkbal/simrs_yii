-- public.rekap_asuransi_data_v source

CREATE OR REPLACE VIEW public.rekap_asuransi_data_v
AS SELECT collection_table.pendaftaran_id,
    collection_table.asuransi_id,
    collection_table.status_periksa,
    collection_table.status_periksa_nama
   FROM ( SELECT a.asuransi_id,
            pendaftaran_t.pendaftaran_id,
            COALESCE(pasienmasukpenunjang_t.status_periksa::integer, pendaftaran_t.status_periksa::integer) AS status_periksa,
            COALESCE(status_lab.lookup_name, status_pendaftaran.lookup_name) AS status_periksa_nama,
            log_asuransitransaksi_t.is_batal,
            log_asuransitransaksi_t.is_pengesahan
           FROM asuransi_t a
             JOIN pendaftaran_t ON pendaftaran_t.asuransi_id = a.asuransi_id
             JOIN log_asuransitransaksi_t ON log_asuransitransaksi_t.asuransi_id = a.asuransi_id
             JOIN ( SELECT a_1.pendaftaran_id,
                    a_1.resumemedisri_id
                   FROM resumemedisri_t a_1
                  WHERE a_1.is_deleted IS FALSE AND a_1.is_active IS TRUE) resumemedisri_t ON resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a_1.pasienmasukpenunjang_id,
                    a_1.pendaftaran_id,
                    a_1.status_periksa,
                    a_1.no_masukpenunjang
                   FROM pasienmasukpenunjang_t a_1
                  WHERE a_1.is_deleted IS FALSE AND a_1.status_periksa::integer = 473) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND (pendaftaran_t.ruangan_id = ANY (ARRAY[348, 349])) AND pendaftaran_t.is_aps IS TRUE
             LEFT JOIN ( SELECT a_1.lookup_id,
                    a_1.lookup_name
                   FROM lookup_m a_1) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a_1.lookup_id,
                    a_1.lookup_name
                   FROM lookup_m a_1) status_lab ON pasienmasukpenunjang_t.status_periksa::integer = status_lab.lookup_id
          WHERE a.is_deleted IS FALSE AND a.is_active IS TRUE AND pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.pasienpulang_id IS NULL AND pendaftaran_t.pasienbatalperiksa_id IS NULL) collection_table
  WHERE collection_table.is_batal IS FALSE AND collection_table.is_pengesahan IS FALSE;