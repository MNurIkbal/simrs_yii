-- public.pegawai_master_v source

CREATE OR REPLACE VIEW public.pegawai_master_v
AS SELECT pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    lkp_gelardepan.lookup_name AS gelardepan_nama,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    lkp_gelarbelakang.lookup_name AS gelarbelakang_nama,
    pegawai_m.status_kawin,
    lkp_statuskawin.lookup_name AS status_kawin_nama,
    pegawai_m.jeniskelamin,
    pegawai_m.tempatlahir_pegawai,
    pegawai_m.tgl_lahirpegawai,
    pegawai_m.alamat_pegawai,
    pegawai_m.agama,
    lkp_agama.lookup_name AS agama_nama,
    pegawai_m.golongan_darah,
    lkp_golongandarah.lookup_name AS golongan_darah_nama,
    pegawai_m.warganegara_pegawai AS warganegara,
    lkp_warganegara.lookup_name AS warganegara_nama,
    pegawai_m.suku_id,
    suku_m.suku_nama,
    pegawai_m.propinsi_id,
    propinsi_m.propinsi_nama,
    pegawai_m.kabupaten_id,
    kabupaten_m.kabupaten_nama,
    pegawai_m.kecamatan_id,
    kecamatan_m.kecamatan_nama,
    pegawai_m.kelurahan_id,
    kelurahan_m.kelurahan_nama,
    pegawai_m.alamatemail,
    pegawai_m.notelp_pegawai,
    pegawai_m.nomobile_pegawai,
    pegawai_m.photopegawai,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pendidikankualifikasi_m.pendkualifikasi_id,
    pendidikankualifikasi_m.pendkualifikasi_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.pangkat_id,
    pegawai_m.kelompokpegawai_id,
    pegawai_m.jabatan_id,
    jabatan_m.jabatan_nama,
    pangkat_m.pangkat_nama,
    kelompokpegawai_m.kelompokpegawai_nama,
    kelompokpegawai_m.kelompokpegawai_namalainnya,
    kelompokpegawai_m.kelompokpegawai_fungsi,
    pegawai_m.warna_kulit,
    lkp_warnakulit.lookup_name AS warna_kulit_nama,
    pegawai_m.status_pegawai,
    lkp_statuspegawai.lookup_name AS status_pegawai_nama,
    pegawai_m.bank_id,
    bank_m.nama_bank,
    bank_m.no_rekening,
    pegawai_m.created_date,
    pegawai_m.kemampuan_bahasa,
    pegawai_m.tinggibadan,
    pegawai_m.beratbadan,
    pegawai_m.npwp,
    pegawai_m.suratizinpraktek,
        CASE
            WHEN pegawai_m.gelarbelakang::text = '9999'::text OR pegawai_m.gelarbelakang IS NULL OR pegawai_m.spesialis_id IS NULL THEN false
            ELSE true
        END AS is_dokterumum,
    pegawai_m.tanda_tangan,
    pegawai_m.is_active,
    pegawai_satusehat_m.satusehat_pegawai_id,
    pegawai_m.noidentitas,
    pegawai_m.is_online,
    pegawai_m.kode_dokter_bpjs,
    pegawai_m.nama_dokter_bpjs
   FROM pegawai_m
     LEFT JOIN ( SELECT a.pendidikan_id,
            a.pendidikan_nama
           FROM pendidikan_m a) pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT a.pendkualifikasi_id,
            a.pendkualifikasi_nama
           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN ( SELECT a.jabatan_id,
            a.jabatan_nama
           FROM jabatan_m a) jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN ( SELECT a.kelompokpegawai_id,
            a.kelompokpegawai_nama,
            a.kelompokpegawai_namalainnya,
            a.kelompokpegawai_fungsi
           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN ( SELECT x.lookup_id,
            x.lookup_name
           FROM lookup_m x
          WHERE x.lookup_type::text = 'warna_kulit'::text) lkp_warnakulit ON pegawai_m.warna_kulit::integer = lkp_warnakulit.lookup_id
     LEFT JOIN ( SELECT x.lookup_id,
            x.lookup_name
           FROM lookup_m x
          WHERE x.lookup_type::text = 'kategori_pegawai'::text) lkp_statuspegawai ON pegawai_m.status_pegawai::integer = lkp_statuspegawai.lookup_id
     LEFT JOIN ( SELECT a.pangkat_id,
            a.pangkat_nama
           FROM pangkat_m a) pangkat_m ON pegawai_m.pangkat_id = pangkat_m.pangkat_id
     LEFT JOIN ( SELECT a.suku_id,
            a.suku_nama
           FROM suku_m a) suku_m ON pegawai_m.suku_id = suku_m.suku_id
     LEFT JOIN ( SELECT x.propinsi_id,
            x.propinsi_nama
           FROM propinsi_m x) propinsi_m ON pegawai_m.propinsi_id = propinsi_m.propinsi_id
     LEFT JOIN ( SELECT x.kabupaten_id,
            x.kabupaten_nama
           FROM kabupaten_m x) kabupaten_m ON pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id
     LEFT JOIN ( SELECT x.kecamatan_id,
            x.kecamatan_nama
           FROM kecamatan_m x) kecamatan_m ON pegawai_m.kecamatan_id = kecamatan_m.kecamatan_id
     LEFT JOIN ( SELECT x.kelurahan_id,
            x.kelurahan_nama
           FROM kelurahan_m x) kelurahan_m ON pegawai_m.kelurahan_id = kelurahan_m.kelurahan_id
     LEFT JOIN ( SELECT a.bank_id,
            a.nama_bank,
            a.no_rekening
           FROM bank_m a) bank_m ON pegawai_m.bank_id = bank_m.bank_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.satusehat_pegawai_id
           FROM pegawai_satusehat_m a) pegawai_satusehat_m ON pegawai_m.pegawai_id = pegawai_satusehat_m.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_gelardepan ON pegawai_m.gelardepan::integer = lkp_gelardepan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_gelarbelakang ON pegawai_m.gelarbelakang::integer = lkp_gelarbelakang.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_statuskawin ON pegawai_m.status_kawin = lkp_statuskawin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_agama ON pegawai_m.agama::integer = lkp_agama.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_golongandarah ON pegawai_m.golongan_darah = lkp_golongandarah.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_warganegara ON pegawai_m.warganegara_pegawai::integer = lkp_warganegara.lookup_id
  WHERE pegawai_m.is_deleted = false;