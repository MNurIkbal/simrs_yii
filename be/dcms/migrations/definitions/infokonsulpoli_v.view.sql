-- public.infokonsulpoli_v source

CREATE OR REPLACE VIEW public.infokonsulpoli_v
AS SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    look_statusperiksa.lookup_name AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    concat(konsulpoli_t.tgl_konsulpoli::date, ' ', jadwaldokter_m.jadwaldokter_mulai) AS tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    look_statuskonsul.lookup_name AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    look_statusapprove.lookup_name AS status_approve,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    konsulpoli_t.tgl_konsulpoli AS tgl_poli,
    konsulpoli_t.tgl_setujui,
    konsulpoli_t.tgl_masukperiksa,
    rencanakontrol.rencanakontrol_id
   FROM konsulpoli_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pegawai_id,
            a.ruangan_id,
            a.tgl_pendaftaran,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON konsulpoli_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dok_mengkonsul ON pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.jadwaldokter_mulai
           FROM jadwaldokter_m a) jadwaldokter_m ON konsulpoli_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperiksa ON konsulpoli_t.status_periksa::integer = look_statusperiksa.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuskonsul ON konsulpoli_t.status_konsul::integer = look_statuskonsul.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusapprove ON konsulpoli_t.status_approve::integer = look_statusapprove.lookup_id
     LEFT JOIN ( SELECT a.rencanakontrol_id,
            a.konsulpoli_id
           FROM rencanakontrol_t a
          WHERE a.is_deleted IS FALSE) rencanakontrol ON rencanakontrol.konsulpoli_id = konsulpoli_t.konsulpoli_id
  WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false AND konsulpoli_t.status_approve = 565
UNION ALL
 SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    look_statusperiksa.lookup_name AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    concat(konsulpoli_t.tgl_konsulpoli::date, ' ', jadwaldokter_m.jadwaldokter_mulai) AS tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    look_statuskonsul.lookup_name AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    look_statusapprove.lookup_name AS status_approve,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    konsulpoli_t.tgl_konsulpoli AS tgl_poli,
    konsulpoli_t.tgl_setujui,
    konsulpoli_t.tgl_masukperiksa,
    rencanakontrol.rencanakontrol_id
   FROM konsulpoli_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pegawai_id,
            a.ruangan_id,
            a.tgl_pendaftaran,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON konsulpoli_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dok_mengkonsul ON pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.jadwaldokter_mulai
           FROM jadwaldokter_m a) jadwaldokter_m ON konsulpoli_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperiksa ON konsulpoli_t.status_periksa::integer = look_statusperiksa.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuskonsul ON konsulpoli_t.status_konsul::integer = look_statuskonsul.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusapprove ON konsulpoli_t.status_approve::integer = look_statusapprove.lookup_id
     LEFT JOIN ( SELECT a.rencanakontrol_id,
            a.konsulpoli_id
           FROM rencanakontrol_t a
          WHERE a.is_deleted IS FALSE) rencanakontrol ON rencanakontrol.konsulpoli_id = konsulpoli_t.konsulpoli_id
  WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false;