-- public.infoapprovaldiskon_v source

CREATE OR REPLACE VIEW public.infoapprovaldiskon_v
AS SELECT approvaldiskon_t.approvaldiskon_id,
    approvaldiskon_t.pembayaran_id,
    approvaldiskon_t.created_date AS tgl_pembayaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    kasir.nama_pegawai AS pegawai_kasir,
    approvaldiskon_t.limit_diskon,
    approvaldiskon_t.jumlah_limit_diskon,
    approvaldiskon_t.diskon,
    approvaldiskon_t.jumlah_diskon,
    approvaldiskon_t.status_approve,
    lkp_status.lookup_name AS status,
    approvaldiskon_t.pegawai_approve_id,
    approval.nama_pegawai AS pegawai_approve_nama,
    approvaldiskon_t.tgl_approve,
    approvaldiskon_t.additional_data,
    pendaftaran_t.no_pendaftaran
   FROM approvaldiskon_t
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.pendaftaran_id,
            a.created_by,
            a.created_date AS tgl_pembayaran
           FROM pembayaran_t a) pembayaran_t ON approvaldiskon_t.pembayaran_id = pembayaran_t.pembayaran_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON approvaldiskon_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.nama_pasien,
            pasien_m_1.no_rekam_medik
           FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON approvaldiskon_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) kasir ON loginpemakai_k.pegawai_id = kasir.pegawai_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) approval ON approvaldiskon_t.pegawai_approve_id = approval.pegawai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_status ON approvaldiskon_t.status_approve = lkp_status.lookup_id;