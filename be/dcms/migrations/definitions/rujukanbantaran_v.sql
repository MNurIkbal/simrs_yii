CREATE OR REPLACE VIEW public.rujukanbantaran_v
AS SELECT rt.rujukanbantaran_id,
    reservasi_bantaran.no_antrian,
    rt.tgl_kunjungan,
    rt.pasien_id,
    rt.nama_pasien,
    rt.no_rekam_medik,
    rt.no_identitas_pasien,
    rt.jenis_kelamin,
    rt.tgl_lahir,
    rt.uptasal_id,
    rt.uptasal_nama,
    rt.instalasi_id,
    COALESCE(rt.instalasi_nama::character varying(50), instalasi.instalasi_nama) AS instalasi_nama,
    rt.ruangan_id,
    COALESCE(rt.ruangan_nama::character varying(50), ruangan.ruangan_nama) AS ruangan_nama,
    dokter.nama_pegawai AS nama_dokter,
    rt.status_verifikasi_bantaran AS status_verifikasi_bantaran_id,
    lookup_verif.lookup_name AS status_verifikasi_bantaran,
    rt.pegawaiverifikasi_id,
    rt.tgl_verifikasi_bantaran,
    lookup_pelayanan.lookup_name AS status_pelayanan_bantaran,
    rt.carabayar_id,
    carabayar.carabayar_nama,
    rt.penjamin_id,
    penjamin.penjamin_nama,
    rt.keterangan_rujukan,
    rt.keterangan_penolakan_rujukan,
    rt.pendaftaranol_id,
    reservasi_bantaran.no_pendaftaranol,
    reservasi_bantaran.status_daftar_ol,
    reservasi_bantaran.status_daftar_ol_nama,
    rt.jam_buka,
    rt.jam_tutup,
    pegawaiverif.nama_pegawai AS nama_pegawaiverifikasi,
    rt.tgl_verifikasi_bantaran AS tgl_verifikasi,
    rt.created_date AS tgl_order,
    rt.no_rujukanbantaran,
    rt.no_tahanan,
    rt.dokter_nama,
    rt.tempat_lahir,
    rt.pendaftaran_id,
    rt.rencana_tindakan,
    rt.tujuan_pemeriksaan,
    rt.status_pelayanan_bantaran as status_pelayanan_bantaran_id,
    rt.pasien_lama
   FROM rujukanbantaran_t rt
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi ON instalasi.instalasi_id = rt.instalasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan ON ruangan.ruangan_id = rt.ruangan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON dokter.pegawai_id = rt.dokter_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar ON carabayar.carabayar_id = rt.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin ON penjamin.penjamin_id = rt.penjamin_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawaiverif ON pegawaiverif.pegawai_id = rt.pegawaiverifikasi_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lookup_verif ON lookup_verif.lookup_id = rt.status_verifikasi_bantaran
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lookup_pelayanan ON lookup_pelayanan.lookup_id = rt.status_pelayanan_bantaran
     LEFT JOIN ( SELECT reservasi.pendaftaranol_id,
            reservasi.no_pendaftaranol,
            antrian.no_antrian,
            reservasi.status_daftar_ol,
            status.lookup_name AS status_daftar_ol_nama
           FROM pendaftaranol_t reservasi
             JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON status.lookup_id = reservasi.status_daftar_ol
             JOIN ( SELECT a.antrian_id,
                    a.no_antrian
                   FROM antrian_t a) antrian ON reservasi.antrian_id = antrian.antrian_id) reservasi_bantaran ON reservasi_bantaran.pendaftaranol_id = rt.pendaftaranol_id;
