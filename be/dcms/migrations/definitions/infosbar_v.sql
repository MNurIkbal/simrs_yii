CREATE VIEW public.infosbar_v AS  
SELECT sbar_t.sbar_id,
    sbar_t.tgl_sbar,
    sbar_t.pendaftaran_id,
    sbar_t.pasienadmisi_id,
    dokter_tujuan.nama_pegawai AS dokter_tujuan,
    pegawai_m.nama_pegawai AS pegawai_input,
    sbar_t.sistol,
    sbar_t.diastol,
    sbar_t.nadi,
    sbar_t.respirasi,
    sbar_t.tinggi_badan,
    sbar_t.berat_badan,
    sbar_t.spo2,
    sbar_t.suhu,
    sbar_t.lingkar_kepala,
    sbar_t.asesmen,
    sbar_t.rekomendasi,
    sbar_t.is_verifikasi,
    sbar_t.is_ttv,
    pegawai_verif.nama_pegawai AS pegawai_verifikasi,
    sbar_t.tanggal_verifikasi,
    sbar_t.is_deleted,
    sbar_t.situasi,
    sbar_t.dokter_tujuan_id,
    sbar_t.created_by,
    pegawai_m.pegawai_id AS pegawai_input_id
FROM sbar_t
    LEFT JOIN ( SELECT a.loginpemakai_id,
           a.pegawai_id
          FROM loginpemakai_k a) loginpemakai_k ON sbar_t.created_by = loginpemakai_k.loginpemakai_id
    LEFT JOIN ( SELECT a.pegawai_id,
           a.nama_pegawai
          FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
    LEFT JOIN ( SELECT a.pegawai_id,
           a.nama_pegawai
          FROM pegawai_m a) pegawai_verif ON sbar_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id
    LEFT JOIN ( SELECT a.pegawai_id,
           a.nama_pegawai
          FROM pegawai_m a) dokter_tujuan ON sbar_t.dokter_tujuan_id = dokter_tujuan.pegawai_id;