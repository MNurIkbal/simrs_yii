-- public.rencanaoperasi_v source

CREATE OR REPLACE VIEW public.rencanaoperasi_v
AS  SELECT rencanaoperasi_t.rencanaoperasi_id,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.pendaftaran_id,
    rencanaoperasi_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin,
    pasien_m.tanggal_lahir,
    pasien_m.photopasien,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    look_jeniskelamin.lookup_name AS j_kelamin,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE COALESCE(kelas_ditagihkan.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id)
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_rj.kelaspelayanan_nama
            ELSE COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelas_ri.kelaspelayanan_nama)
        END AS kelaspelayanan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama,
    rencanaoperasi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rencanaoperasi_t.tgl_permintaan,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pasienkirimkeunitlain_t.status_penunjang,
    look_statuspenunjang.lookup_name AS status,
    look_statuspenunjang.lookup_name AS status_operasi,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dr_pemeriksa.nama_pegawai
            ELSE dr_pemeriksa_admisi.nama_pegawai
        END AS dok_pemeriksa,
    inpostoperasi_t.mulai_operasi,
    inpostoperasi_t.selesai_operasi,
    app.nama_pegawai AS pegawai_approve,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_approve,
    jenis_operasi.kegiatanoperasi_nama,
    pasienmasukpenunjang_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    rencanaoperasi_t.is_deleted,
        CASE
            WHEN rencanaoperasi_t.jenis_operasi_cyto IS TRUE THEN '0'::text
            ELSE '1'::text
        END AS jenis_operasi_cyto,
        CASE
            WHEN rencanaoperasi_t.jenis_operasi_elektif = true THEN '0'::text
            ELSE '1'::text
        END AS jenis_operasi_elektif,
        CASE
            WHEN rencanaoperasi_t.jenis_operasi_odc = true THEN '0'::text
            ELSE '1'::text
        END AS jenis_operasi_odc,
    pasienmasukpenunjang_t.status_periksa,
    look_statusperiksa.lookup_name AS nama_statusperiksa,
    kelas_ditagihkan.kelaspelayanan_id AS kelas_titipan_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_titipan_ditagihkan_nama
   FROM rencanaoperasi_t
     JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.no_orderkeunitlain,
            a.status_penunjang,
            a.pegawai_id,
            a.catatan_dokterpengirim,
            a.pasienmasukpenunjang_id
           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.pendaftaran_id,
            a.tgl_pendaftaran,
            a.no_pendaftaran,
            a.umur,
            a.pasienadmisi_id,
            a.kelaspelayanan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.pasien_id,
            a.jeniskasuspenyakit_id,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.is_pasientitipan,
            a.kelaspelayanan_id,
            a.kelas_ditagihkan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.pegawai_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.photopasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_rj ON pendaftaran_t.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dr_pemeriksa ON pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dr_pemeriksa_admisi ON pasienadmisi_t.pegawai_id = dr_pemeriksa_admisi.pegawai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.mulai_operasi,
            a.selesai_operasi
           FROM inpostoperasi_t a) inpostoperasi_t ON inpostoperasi_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.tglmasukpenunjang,
            a.kamarruangan_id,
            a.status_periksa,
            a.created_by
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) app ON loginpemakai_k.pegawai_id = app.pegawai_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            string_agg(kegiatanoperasi_m.kegiatanoperasi_nama::text, ','::text) AS kegiatanoperasi_nama
           FROM permintaankepenunjang_t
             JOIN ( SELECT a.daftartindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.operasi_id,
                    a.golonganoperasi_id,
                    a.kegiatanoperasi_id
                   FROM operasi_m a) operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
             JOIN ( SELECT a.golonganoperasi_id
                   FROM golonganoperasi_m a) golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
             JOIN ( SELECT a.kegiatanoperasi_id,
                    a.kegiatanoperasi_nama
                   FROM kegiatanoperasi_m a) kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) jenis_operasi ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = jenis_operasi.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statuspenunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = look_statuspenunjang.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperiksa ON pasienmasukpenunjang_t.status_periksa::integer = look_statusperiksa.lookup_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id AND pasienadmisi_t.is_pasientitipan = true;