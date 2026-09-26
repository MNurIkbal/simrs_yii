-- public.infokonsul_v source

CREATE OR REPLACE VIEW public.infokonsul_v
AS SELECT NULL::integer AS permintaankonsul_id,
    konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    ruangan_m.instalasi_id,
    konsulpoli_t.ruangan_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.umur,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    dokter_dpjp.nama_pegawai AS nama_dokter,
    dokter_konsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.catatan_dokter_konsul,
    konsulpoli_t.jawaban_konsul,
    dokter_konsul.tanda_tangan,
    konsulpoli_t.created_date,
    NULL::character varying AS jenis_konsul_nama,
    dokter_dpjp.tanda_tangan AS tanda_tangan_dpjp
   FROM konsulpoli_t
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.pegawai_id,
            pendaftaran_t_1.kelaspelayanan_id,
            pendaftaran_t_1.penjamin_id,
            pendaftaran_t_1.umur
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.nama_pasien,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin
           FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.tanda_tangan
           FROM pegawai_m a) dokter_dpjp ON pendaftaran_t.pegawai_id = dokter_dpjp.pegawai_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.tanda_tangan
           FROM pegawai_m a) dokter_konsul ON konsulpoli_t.pegawai_id = dokter_konsul.pegawai_id
  WHERE konsulpoli_t.is_active = true AND konsulpoli_t.is_deleted = false
UNION ALL
 SELECT permintaankonsul_t.permintaankonsul_id,
    NULL::integer AS konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    ruangan_m.instalasi_id,
    pasienadmisi_t.ruangan_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.umur,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    dokter_dpjp.nama_pegawai AS nama_dokter,
    dokter_konsul.nama_pegawai AS dok_mengkonsul,
    permintaankonsul_t.waktu_permintaan AS tgl_konsulpoli,
    COALESCE(permintaankonsul_t.waktu_persetujuan, permintaankonsul_t.waktu_permintaan) AS tgl_selesaikonsul,
    permintaankonsul_t.ket_konsul AS catatan_dokter_konsul,
    permintaankonsul_t.jawaban_konsul,
    dokter_konsul.tanda_tangan,
    permintaankonsul_t.created_date,
    jenis_konsul.lookup_name AS jenis_konsul_nama,
    dokter_dpjp.tanda_tangan AS tanda_tangan_dpjp
   FROM permintaankonsul_t
     JOIN ( SELECT a.pasienadmisi_id,
            a.kelaspelayanan_id,
            a.penjamin_id,
            a.pegawai_id,
            a.ruangan_id,
            a.pendaftaran_id
           FROM pasienadmisi_t a
          WHERE a.status_ranap <> 453) pasienadmisi_t ON permintaankonsul_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.umur,
            pendaftaran_t_1.pasienadmisi_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.nama_pasien,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin
           FROM pasien_m pasien_m_1) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.tanda_tangan
           FROM pegawai_m a) dokter_dpjp ON permintaankonsul_t.dokterdpjpasal_id = dokter_dpjp.pegawai_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.tanda_tangan
           FROM pegawai_m a) dokter_konsul ON permintaankonsul_t.dokter_id = dokter_konsul.pegawai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) jenis_konsul ON jenis_konsul.lookup_id = permintaankonsul_t.jenis_konsul::integer
  WHERE permintaankonsul_t.is_active = true AND permintaankonsul_t.is_deleted = false;