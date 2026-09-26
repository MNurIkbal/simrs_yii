-- public.laporanpasienrawatinaptransfusidarah_v source

CREATE OR REPLACE VIEW public.laporanpasienrawatinaptransfusidarah_v
AS SELECT pasien_m.no_rekam_medik AS "No Rekam Medik",
    pendaftaran_t.no_pendaftaran AS "No Pendaftaran",
    pasienadmisi_t.tgl_admisi AS "Tanggal Pendaftaran",
    pasien_m.nama_pasien AS "Nama",
    pasien_m.tanggal_lahir AS "Tanggal Lahir",
    ruangan_m.ruangan_nama AS "Ruangan",
    propinsi_m.propinsi_nama AS "Provinsi",
    kabupaten_m.kabupaten_nama AS "Kabupaten",
    kecamatan_m.kecamatan_nama AS "Kecamatan",
    kelurahan_m.kelurahan_nama AS "Kelurahan",
    jk.lookup_name AS "Jenis Kelamin",
    pasien_m.alamat_pasien AS "Alamat",
        CASE
            WHEN pasien_m.no_telepon_pasien IS NULL THEN '-'::character varying(50)
            ELSE concat('="', pasien_m.no_telepon_pasien, '"')::character varying(50)
        END AS "No Telpone",
    ji.lookup_name AS "Jenis Identitas",
        CASE
            WHEN pasien_m.jenisidentitas::text = '94'::character varying::text THEN concat('="', pasien_m.no_identitas_pasien, '"')::character varying
            ELSE '-'::character varying
        END AS "No Identitas",
    penanggungjawab_m.penanggungjawab_nama AS "Nama Penanggung Jawab"
   FROM pasienadmisi_t
     JOIN tindakanpelayanan_t ON tindakanpelayanan_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     LEFT JOIN lookup_m ji ON pasien_m.jenisidentitas::integer = ji.lookup_id
  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true AND pasienadmisi_t.status_ranap <> 453 AND tindakanpelayanan_t.daftartindakan_id = 3478
  GROUP BY pasien_m.no_rekam_medik, pendaftaran_t.no_pendaftaran, pasienadmisi_t.tgl_admisi, pasien_m.nama_pasien, pasien_m.tanggal_lahir, ruangan_m.ruangan_nama, propinsi_m.propinsi_nama, kabupaten_m.kabupaten_nama, kecamatan_m.kecamatan_nama, kelurahan_m.kelurahan_nama, jk.lookup_name, pasien_m.alamat_pasien, pasien_m.no_telepon_pasien, ji.lookup_name, pasien_m.jenisidentitas, pasien_m.no_identitas_pasien, penanggungjawab_m.penanggungjawab_nama
  ORDER BY pasienadmisi_t.tgl_admisi;