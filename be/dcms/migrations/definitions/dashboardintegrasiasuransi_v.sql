CREATE OR REPLACE VIEW public.dashboardintegrasiasuransi_v
AS SELECT 'RJ/RD'::text AS tipe,
    a.asuransi_id,
    a.provider_id,
    a.penjamin_id,
    a.no_klaim,
    pasien_m.nama_pasien,
    dokterdpjp.nama_pegawai,
    a.no_kartu,
    a.no_polis,
    a.no_sep,
    a.is_cob,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_dpjp_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_kode,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.jeniskelamin,
        CASE pasien_m.jeniskelamin
            WHEN '15'::text THEN 'L'::text
            WHEN '16'::text THEN 'P'::text
            ELSE 'U'::text
        END AS jk_nama,
    pasien_m.alamat_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    COALESCE(pasienmasukpenunjang_t.status_periksa::integer, pendaftaran_t.status_periksa::integer) AS status_periksa,
    COALESCE(status_lab.lookup_name, status_pendaftaran.lookup_name) AS status_periksa_nama,
    COALESCE(pasienpulang_t.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi) AS tgl_pulang,
    petugas.nama_pegawai AS petugas,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.no_masukpenunjang,
        CASE
            WHEN log_asuransitransaksi_t.is_batal = true THEN 'Batal'::text
            WHEN log_asuransitransaksi_t.is_pengesahan = true THEN 'Pengesahan'::text
            WHEN log_asuransitransaksi_t.is_batal = false AND log_asuransitransaksi_t.is_pengesahan = false THEN 'Diproses'::text
            WHEN log_asuransitransaksi_t.asuransi_id IS NULL THEN 'Belum Diproses'::text
            ELSE NULL::text
        END AS status_klaim
   FROM asuransi_t a
     JOIN pendaftaran_t ON pendaftaran_t.asuransi_id = a.asuransi_id
     LEFT JOIN ( SELECT log_asuransitransaksi_t_1.is_batal,
            log_asuransitransaksi_t_1.is_pengesahan,
            log_asuransitransaksi_t_1.asuransi_id
           FROM log_asuransitransaksi_t log_asuransitransaksi_t_1) log_asuransitransaksi_t ON log_asuransitransaksi_t.asuransi_id = a.asuransi_id
     LEFT JOIN ( SELECT a_1.pasienmasukpenunjang_id,
            a_1.pendaftaran_id,
            a_1.status_periksa,
            a_1.no_masukpenunjang
           FROM pasienmasukpenunjang_t a_1
          WHERE a_1.is_deleted IS FALSE) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND (pendaftaran_t.ruangan_id = ANY (ARRAY[348, 349])) AND pendaftaran_t.is_aps IS TRUE
     JOIN ( SELECT a_1.ruangan_id,
            a_1.ruangan_nama,
            a_1.ruangan_singkatan
           FROM ruangan_m a_1) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.nama_pasien,
            pasien_m_1.alamat_pasien,
            pasien_m_1.jeniskelamin
           FROM pasien_m pasien_m_1) pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokterdpjp ON dokterdpjp.pegawai_id = pendaftaran_t.pegawai_id
     JOIN ( SELECT a_1.kelaspelayanan_id,
            a_1.kelaspelayanan_kode,
            a_1.kelaspelayanan_nama
           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a_1.carabayar_id,
            a_1.carabayar_nama
           FROM carabayar_m a_1) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a_1.penjamin_id,
            a_1.penjamin_nama
           FROM penjamin_m a_1) penjamin_m ON a.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a_1.jeniskasuspenyakit_id,
            a_1.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a_1) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a_1.pegawai_id,
            a_1.nama_pegawai
           FROM pegawai_m a_1) petugas ON petugas.pegawai_id = a.created_by
     LEFT JOIN ( SELECT a_1.pasienpulang_id,
            a_1.tglpasienpulang
           FROM pasienpulang_t a_1
          WHERE a_1.is_deleted IS FALSE) pasienpulang_t ON pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id
     LEFT JOIN ( SELECT a_1.lookup_id,
            a_1.lookup_name
           FROM lookup_m a_1) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
     LEFT JOIN ( SELECT a_1.lookup_id,
            a_1.lookup_name
           FROM lookup_m a_1) status_lab ON pasienmasukpenunjang_t.status_periksa::integer = status_lab.lookup_id
  WHERE a.is_deleted IS FALSE AND a.is_active IS TRUE AND pendaftaran_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT 'RI'::text AS tipe,
    a.asuransi_id,
    a.provider_id,
    a.penjamin_id,
    a.no_klaim,
    pasien_m.nama_pasien,
    dokterdpjp.nama_pegawai,
    a.no_kartu,
    a.no_polis,
    a.no_sep,
    a.is_cob,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_dpjp_id,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_kode,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.jeniskelamin,
        CASE pasien_m.jeniskelamin
            WHEN '15'::text THEN 'L'::text
            WHEN '16'::text THEN 'P'::text
            ELSE 'U'::text
        END AS jk_nama,
    pasien_m.alamat_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap AS status_periksa,
    status_admisi.lookup_name AS status_periksa_nama,
    COALESCE(pasienpulang_t.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi) AS tgl_pulang,
    petugas.nama_pegawai AS petugas,
    NULL::integer AS pasienmasukpenunjang_id,
    NULL::text AS no_masukpenunjang,
        CASE
            WHEN log_asuransitransaksi_t.is_batal = true THEN 'Batal'::text
            WHEN log_asuransitransaksi_t.is_pengesahan = true THEN 'Pengesahan'::text
            WHEN log_asuransitransaksi_t.is_batal = false AND log_asuransitransaksi_t.is_pengesahan = false THEN 'Diproses'::text
            WHEN log_asuransitransaksi_t.asuransi_id IS NULL THEN 'Belum Diproses'::text
            ELSE NULL::text
        END AS status_klaim
   FROM asuransi_t a
     JOIN pendaftaran_t ON pendaftaran_t.asuransi_id = a.asuransi_id
     JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     LEFT JOIN ( SELECT log_asuransitransaksi_t_1.is_batal,
            log_asuransitransaksi_t_1.is_pengesahan,
            log_asuransitransaksi_t_1.asuransi_id
           FROM log_asuransitransaksi_t log_asuransitransaksi_t_1) log_asuransitransaksi_t ON log_asuransitransaksi_t.asuransi_id = a.asuransi_id
     JOIN ( SELECT a_1.ruangan_id,
            a_1.ruangan_nama,
            a_1.ruangan_singkatan
           FROM ruangan_m a_1) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.nama_pasien,
            pasien_m_1.alamat_pasien,
            pasien_m_1.jeniskelamin
           FROM pasien_m pasien_m_1) pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokterdpjp ON dokterdpjp.pegawai_id = pasienadmisi_t.pegawai_id
     JOIN ( SELECT a_1.kelaspelayanan_id,
            a_1.kelaspelayanan_kode,
            a_1.kelaspelayanan_nama
           FROM kelaspelayanan_m a_1) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a_1.carabayar_id,
            a_1.carabayar_nama
           FROM carabayar_m a_1) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a_1.penjamin_id,
            a_1.penjamin_nama
           FROM penjamin_m a_1) penjamin_m ON a.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a_1.jeniskasuspenyakit_id,
            a_1.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a_1) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a_1.pegawai_id,
            a_1.nama_pegawai
           FROM pegawai_m a_1) petugas ON petugas.pegawai_id = a.created_by
     LEFT JOIN ( SELECT a_1.pasienpulang_id,
            a_1.tglpasienpulang
           FROM pasienpulang_t a_1
          WHERE a_1.is_deleted IS FALSE) pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
     LEFT JOIN ( SELECT a_1.lookup_id,
            a_1.lookup_name
           FROM lookup_m a_1) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
  WHERE a.is_deleted IS FALSE AND a.is_active IS TRUE AND pendaftaran_t.pasienadmisi_id IS NOT NULL;