-- public.infojadwaldokter_v source

CREATE OR REPLACE VIEW public.infojadwaldokter_v
AS SELECT jadwaldokter_m.jadwaldokter_id,
    jadwaldokter_m.ruangan_id,
    jadwaldokter_m.instalasi_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai,
    jadwaldokter_m.jadwaldokter_hari,
    concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS "Waktu",
    jadwaldokter_m.maximumantrian AS kuota,
    jadwaldokter_m.jadwaldokter_mulai AS waktu_mulai,
    jadwaldokter_m.jadwaldokter_tutup AS waktu_selesai,
    jadwaldoktertambahan_m.kuota_penambahan,
    jadwaldokter_m.maximumantrian::double precision + jadwaldoktertambahan_m.kuota_penambahan::double precision AS total_kuota,
    look_hari.hari,
    jadwalbukapoli_m.hari AS hari_jadwalbuka,
    jadwaldokter_m.kuota_online,
    jadwaldokter_m.jadwaldokter_tgl,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
        CASE
            WHEN jadwalbukapoli_m.shift_id IS NULL THEN 0
            ELSE jadwalbukapoli_m.shift_id
        END AS shift_id,
    shift_m.shift1_id,
    jadwaldokter_m.notifikasi_id,
    notifikasi_m.judul_temp,
    notifikasi_m.notifikasi,
    COALESCE(kuotadokter_r.kuota_tersedia, 0::real) AS kuota_tersedia,
    jadwaldokter_m.is_active,
    kuotadokter_r.kuotadokter_id,
    jadwaldokter_m.jadwalbukapoli_id,
    jadwaldokter_m.kuota_total,
    pegawai_m.kode_dokter_bpjs,
    ruangan_m.kode_ruangan_bpjs,
    jadwaldokter_m.kuota_bpjs_online,
    jadwaldokter_m.kuota_nonbpjs_online,
    jadwaldokter_m.is_bersedia,
    jadwaldokter_m.is_loaddokter,
    jadwaldokter_m.spesialisruangan_id,
    jadwaldokter_m.jumlah_loaddokter,
    ruangan_m.is_online AS ruangan_online,
    pegawai_m.is_online AS pegawai_online,
    jadwaldokter_m.kuota_bpjs_offline,
    jadwaldokter_m.kuota_nonbpjs_offline,
    ruangan_m.is_executive
   FROM jadwaldokter_m
     JOIN ( SELECT a.ruangan_id,
            a.poliklinik_id,
            a.ruangan_nama,
            a.kode_ruangan_bpjs,
            a.is_online,
            a.is_executive
           FROM ruangan_m a) ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.pegawai_id,
            a.dokter_id,
            a.nama_pegawai,
            a.kode_dokter_bpjs,
            a.is_online,
            a.is_active,
            a.is_deleted
           FROM pegawai_m a) pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.kuota_penambahan
           FROM jadwaldoktertambahan_m a) jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
     JOIN ( SELECT a.jadwalbukapoli_id,
            a.is_deleted,
            a.shift_id,
            a.hari
           FROM jadwalbukapoli_m a) jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN ( SELECT a.shift_id,
            a.shift1_id
           FROM shift_m a) shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
     LEFT JOIN ( SELECT a.notifikasi_id,
            a.judul_temp,
            a.notifikasi
           FROM notifikasi_m a) notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.kuotadokter_id,
            a.is_online,
            a.kuota_tersedia
           FROM kuotadokter_r a) kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS hari
           FROM lookup_m a) look_hari ON jadwalbukapoli_m.hari = look_hari.lookup_id
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true;