-- public.antrianjkn_v source

CREATE OR REPLACE VIEW public.antrianjkn_v
AS SELECT 'online'::text AS tipe,
    pendaftaranol_t.pendaftaranol_id,
    antrian_t.pendaftaran_id,
    pendaftaranol_t.no_pendaftaranol AS kodebooking,
        CASE
            WHEN antrianjkn_r.jenis_cara_bayar IS NULL THEN 1100
            ELSE antrianjkn_r.jenis_cara_bayar
        END AS jenis_cara_bayar,
        CASE
            WHEN pendaftaranol_t.carabayar_id = 6 THEN 'JKN'::text
            ELSE 'NON JKN'::text
        END::character varying(200) AS jenispasien,
        CASE
            WHEN antrianjkn_r.nomorkartu IS NULL THEN COALESCE(pasien_m.nopeserta_bpjs::character varying(50), pendaftaranol_t.no_bpjs)
            ELSE antrianjkn_r.nomorkartu
        END AS nomorkartu,
    pendaftaranol_t.jenisidentitas,
    pasien_m.additional_pasien,
    look_jenisidentitas.lookup_name AS jenisidentitas_nama,
    pendaftaranol_t.no_identitas_pasien,
    REPLACE(pendaftaranol_t.no_telepon_pasien::text, ' ', '')::character varying(50) AS no_telepon_pasien,
    ruangan_m.kode_ruangan_bpjs AS kodepoli,
    ruangan_m.ruangan_nama AS namapoli,
        CASE
            WHEN pendaftaranol_t.status_pasien = 311 THEN 0
            ELSE 1
        END AS status_pasien,
    look_statuspasien.lookup_name AS status_pasien_nama,
    COALESCE(antrianjkn_r.no_rekam_medik, pasien_m.no_rekam_medik) AS no_rekam_medik,
    antrianjkn_r.tanggal_periksa,
    pegawai_m.kode_dokter_bpjs AS kodedokter,
    pegawai_m.nama_pegawai AS namadokter,
    concat(to_char(jadwaldokter_m.jadwaldokter_mulai::interval, 'HH24:MI'::text), '-', to_char(jadwaldokter_m.jadwaldokter_tutup::interval, 'HH24:MI'::text)) AS jampraktek,
    COALESCE(look_jeniskunjungan.lookup_name) AS jeniskunjungan,
    COALESCE(antrianjkn_r.nomorreferensi, bpjs_t.norujukan::character varying(100), pendaftaranol_t.no_rujukan) AS nomorreferensi,
    antrian_t.no_antrian AS nomorantrean,
    COALESCE(antrian_t.temp_urutan, "right"(antrian_t.no_antrian::text, 3)::character varying(12)) AS angkaantrean,
    jadwaldokter_m.kuota_bpjs_online + jadwaldokter_m.kuota_bpjs_offline AS kuotajkn,
    jadwaldokter_m.kuota_nonbpjs_online + jadwaldokter_m.kuota_nonbpjs_offline AS kuotanonjkn,
    kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline AS sisakuotajkn,
    kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline AS sisakuotanonjkn,
    antrianjkn_r.keterangan,
    jadwaldokter_m.jumlah_loaddokter AS estimasidilayani,
    antrian_t.antrian_id,
    jadwaldokter_m.jadwaldokter_id,
    slotjadwaldokter_m.jam_mulai,
    slotjadwaldokter_m.jam_selesai,
    antrianjkn_r.is_selesai_periksa,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tglpasienpulang,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaranol_t.jenis_reservasi,
    pasien_m.pasien_id
   FROM pendaftaranol_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_antrian,
            a.temp_urutan,
            a.antrian_id,
            a.jadwaldokter_id,
            a.slot_sequence
           FROM antrian_t a) antrian_t ON pendaftaranol_t.antrian_id = antrian_t.antrian_id
     JOIN ( SELECT a.pasien_id,
            a.nopeserta_bpjs,
            a.additional_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pendaftaranol_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.jadwaldokter_id,
            a.ruangan_id,
            a.pegawai_id,
            a.jadwaldokter_mulai,
            a.jadwaldokter_tutup,
            a.kuota_bpjs_online,
            a.kuota_bpjs_offline,
            a.kuota_nonbpjs_online,
            a.kuota_nonbpjs_offline,
            a.jumlah_loaddokter
           FROM jadwaldokter_m a) jadwaldokter_m ON antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
     JOIN ( SELECT a.ruangan_id,
            a.kode_ruangan_bpjs,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kode_dokter_bpjs
           FROM pegawai_m a) pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kuota_bpjs_online,
            a.kuota_nonbpjs_online,
            a.jadwaldokter_id,
            a.is_online
           FROM kuotadokter_r a) kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
     LEFT JOIN ( SELECT a.kuota_bpjs_offline,
            a.kuota_nonbpjs_offline,
            a.jadwaldokter_id,
            a.is_online
           FROM kuotadokter_r a) kuotadokter_r_offline ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id AND kuotadokter_r_offline.is_online = false
     JOIN ( SELECT a.pendaftaranol_id,
            a.jenis_cara_bayar,
            a.nomorkartu,
            a.no_rekam_medik,
            a.tanggal_periksa,
            a.nomorreferensi,
            a.keterangan,
            a.jeniskunjungan,
            a.is_selesai_periksa
           FROM antrianjkn_r a) antrianjkn_r ON pendaftaranol_t.pendaftaranol_id = antrianjkn_r.pendaftaranol_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.slot_sequence,
            a.jam_mulai,
            a.jam_selesai
           FROM slotjadwaldokter_m a) slotjadwaldokter_m ON antrian_t.jadwaldokter_id = slotjadwaldokter_m.jadwaldokter_id AND antrian_t.slot_sequence = slotjadwaldokter_m.slot_sequence
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenispasien ON antrianjkn_r.jenis_cara_bayar = look_jenispasien.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenisidentitas ON pendaftaranol_t.jenisidentitas::integer = look_jenisidentitas.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspasien ON pendaftaranol_t.status_pasien = look_statuspasien.lookup_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienpulang_id,
            a.bpjs_id,
            a.tgl_pendaftaran,
            a.additional_data,
            a.carabayar_id
           FROM pendaftaran_t a) pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskunjungan ON antrianjkn_r.jeniskunjungan = look_jeniskunjungan.lookup_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.norujukan
           FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT cm.carabayar_id,
            cm.groupcarabayar_id
           FROM carabayar_m cm) carabayar_m ON carabayar_m.carabayar_id = pendaftaran_t.carabayar_id
UNION ALL
 SELECT 'offline'::text AS tipe,
    NULL::integer AS pendaftaranol_id,
    pendaftaran_t.pendaftaran_id,
    concat('RS', pendaftaran_t.pendaftaran_id) AS kodebooking,
        CASE
            WHEN antrianjkn_r.jenis_cara_bayar IS NULL THEN
            CASE
                WHEN carabayar_m.groupcarabayar_id = 418 THEN 1100
                ELSE 1101
            END
            ELSE antrianjkn_r.jenis_cara_bayar
        END AS jenis_cara_bayar,
        CASE
            WHEN pendaftaran_t.carabayar_id = 6 THEN 'JKN'::text
            ELSE 'NON JKN'::text
        END::character varying(200) AS jenispasien,
        CASE
            WHEN antrianjkn_r.nomorkartu IS NULL THEN COALESCE(pasien_m.nopeserta_bpjs::character varying(50), pendaftaranol_t.no_bpjs)
            WHEN antrianjkn_r.nomorkartu::text = ''::text THEN COALESCE(pasien_m.nopeserta_bpjs::character varying(50), pendaftaranol_t.no_bpjs)
            ELSE antrianjkn_r.nomorkartu
        END AS nomorkartu,
    pasien_m.jenisidentitas,
    pasien_m.additional_pasien,
    look_jenisidentitas.lookup_name AS jenisidentitas_nama,
    pasien_m.no_identitas_pasien,
    REPLACE(pasien_m.no_telepon_pasien::text, ' ', '')::character varying(50) AS no_telepon_pasien,
    ruangan_m.kode_ruangan_bpjs AS kodepoli,
    ruangan_m.ruangan_nama AS namapoli,
        CASE
            WHEN pendaftaran_t.status_pasien::text = '311'::text THEN 0
            ELSE 1
        END AS status_pasien,
    look_statuspasien.lookup_name AS status_pasien_nama,
    COALESCE(antrianjkn_r.no_rekam_medik, pasien_m.no_rekam_medik) AS no_rekam_medik,
    antrianjkn_r.tanggal_periksa,
    pegawai_m.kode_dokter_bpjs AS kodedokter,
    pegawai_m.nama_pegawai AS namadokter,
    concat(to_char(jadwaldokter_m.jadwaldokter_mulai::interval, 'HH24:MI'::text), '-', to_char(jadwaldokter_m.jadwaldokter_tutup::interval, 'HH24:MI'::text)) AS jampraktek,
    COALESCE(look_jeniskunjungan.lookup_name) AS jeniskunjungan,
    COALESCE(antrianjkn_r.nomorreferensi, bpjs_t.norujukan::character varying(100), pendaftaranol_t.no_rujukan) AS nomorreferensi,
    antrian_t.no_antrian AS nomorantrean,
    antrian_t.temp_urutan AS angkaantrean,
    jadwaldokter_m.kuota_bpjs_online + jadwaldokter_m.kuota_bpjs_offline AS kuotajkn,
    jadwaldokter_m.kuota_nonbpjs_online + jadwaldokter_m.kuota_nonbpjs_offline AS kuotanonjkn,
    kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline AS sisakuotajkn,
    kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline AS sisakuotanonjkn,
    antrianjkn_r.keterangan,
    jadwaldokter_m.jumlah_loaddokter AS estimasidilayani,
    antrian_t.antrian_id,
    jadwaldokter_m.jadwaldokter_id,
    slotjadwaldokter_m.jam_mulai,
    slotjadwaldokter_m.jam_selesai,
    antrianjkn_r.is_selesai_periksa,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tglpasienpulang,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaranol_t.jenis_reservasi,
    pasien_m.pasien_id
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_rujukan,
            a.jenis_reservasi,
            a.no_bpjs
           FROM pendaftaranol_t a) pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_antrian,
            a.temp_urutan,
            a.antrian_id,
            a.jadwaldokter_id,
            a.slot_sequence
           FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
     JOIN ( SELECT a.pasien_id,
            a.jenisidentitas,
            a.no_identitas_pasien,
            a.no_telepon_pasien,
            a.nopeserta_bpjs,
            a.additional_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.ruangan_id,
            a.pegawai_id,
            a.jadwaldokter_mulai,
            a.jadwaldokter_tutup,
            a.kuota_bpjs_online,
            a.kuota_bpjs_offline,
            a.kuota_nonbpjs_online,
            a.kuota_nonbpjs_offline,
            a.jumlah_loaddokter
           FROM jadwaldokter_m a) jadwaldokter_m ON antrian_t.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.kode_ruangan_bpjs,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.kode_dokter_bpjs
           FROM pegawai_m a) pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kuota_bpjs_online,
            a.kuota_nonbpjs_online,
            a.jadwaldokter_id,
            a.is_online
           FROM kuotadokter_r a) kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
     LEFT JOIN ( SELECT a.kuota_bpjs_offline,
            a.kuota_nonbpjs_offline,
            a.jadwaldokter_id,
            a.is_online
           FROM kuotadokter_r a) kuotadokter_r_offline ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id AND kuotadokter_r_offline.is_online = false
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.jenis_cara_bayar,
            a.nomorkartu,
            a.no_rekam_medik,
            a.tanggal_periksa,
            a.nomorreferensi,
            a.keterangan,
            a.jeniskunjungan,
            a.is_selesai_periksa,
            a.kodebooking
           FROM antrianjkn_r a) antrianjkn_r ON pendaftaran_t.pendaftaran_id = antrianjkn_r.pendaftaran_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.slot_sequence,
            a.jam_mulai,
            a.jam_selesai
           FROM slotjadwaldokter_m a) slotjadwaldokter_m ON antrian_t.jadwaldokter_id = slotjadwaldokter_m.jadwaldokter_id AND antrian_t.slot_sequence = slotjadwaldokter_m.slot_sequence
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenispasien ON antrianjkn_r.jenis_cara_bayar = look_jenispasien.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspasien ON pendaftaran_t.status_pasien::integer = look_statuspasien.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskunjungan ON antrianjkn_r.jeniskunjungan = look_jeniskunjungan.lookup_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.norujukan
           FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT cm.carabayar_id,
            cm.groupcarabayar_id
           FROM carabayar_m cm) carabayar_m ON carabayar_m.carabayar_id = pendaftaran_t.carabayar_id;