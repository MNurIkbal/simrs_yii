-- public.laporankunjunganfisiorj_v source

CREATE OR REPLACE VIEW public.laporankunjunganfisiorj_v
AS SELECT row_number() OVER (ORDER BY fisio.tgl_pendaftaran) AS "No",
    fisio.tgl_pendaftaran AS "Tanggal Pendaftaran",
    concat(fisio.no_rekam_medik, ' / ', fisio.jeniskelamin_kode, ' / ', fisio.nama_pasien) AS "Data Pasien",
    concat(fisio.tanggal_lahir, ' / ', fisio.umur) AS "Tanggal Lahir",
    fisio.alamat_pasien AS "Alamat",
    concat(fisio.carabayar_nama, ' / ', fisio.penjamin_nama) AS "Cara Bayar / Penjamin",
    concat(fisio.instalasi_nama, ' / ', fisio.ruangan_nama) AS "Instalasi / Ruangan",
    fisio.dokterdpjp_nama AS "Dokter",
    fisio.no_telepon_pasien AS "No Telepon Pasien",
    pegawai_terapi.nama_pegawai AS "Terapis",
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS "Jenis Pemeriksaan",
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS "Nama Pemeriksaan",
    fisio.status_periksa_nama AS "Status Periksa"
   FROM lapkunjunganpasienfisiorj_v fisio
     JOIN pendaftaran_t ON fisio.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN tindakanpelayanan_t ON fisio.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN pemeriksaanfisio_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id
     LEFT JOIN soapfisioterapi_t ON fisio.pendaftaran_id = soapfisioterapi_t.pendaftaran_id
     LEFT JOIN pegawai_m pegawai_terapi ON soapfisioterapi_t.terapis_id = pegawai_terapi.pegawai_id
  ORDER BY fisio.tgl_pendaftaran;