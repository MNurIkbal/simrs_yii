-- public.laporanrekammedis_v source

CREATE OR REPLACE VIEW public.laporanrekammedis_v
AS WITH header_data AS (
         SELECT 'RJ-RD'::text AS tipe,
            pendaftaran_t.pasien_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.umur,
            pendaftaran_t.golonganumur_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.kelaspelayanan_id,
            NULL::integer AS kamarruangan_id,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.kunjungan
           FROM pendaftaran_t
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL
        UNION ALL
         SELECT 'RI'::text AS tipe,
            pasienadmisi_t.pasien_id,
            pasienadmisi_t.pendaftaran_id,
            pendaftaran_t.umur,
            pendaftaran_t.golonganumur_id,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.tgl_stopakomodasi,
            pasienadmisi_t.pasienpulang_id,
            pasienadmisi_t.carabayar_id,
            pasienadmisi_t.penjamin_id,
            pasienadmisi_t.pegawai_id,
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            pasienadmisi_t.kamarruangan_id,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.kunjungan
           FROM pasienadmisi_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.umur,
                    a.golonganumur_id,
                    a.tgl_stopakomodasi,
                    a.rujukan_id,
                    a.kunjungan
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
        )
 SELECT pasien_m.no_rekam_medik AS "No RM",
    pasien_m.nama_pasien AS "Nama Pasien",
    header_data.umur AS "Umur",
    golonganumur_m.golonganumur_nama AS "Golongan Umur",
    lkp_jeniskelamin.lookup_name AS "Jenis Kelamin",
    header_data.tgl_pendaftaran AS "Tanggal Masuk",
    header_data.tgl_stopakomodasi AS "Tanggal Stop Akomodasi",
    pasienpulang_t.tglpasienpulang AS "Tanggal Pulang",
    carabayar_m.carabayar_nama AS "Cara Bayar",
    penjamin_m.penjamin_nama AS "Penjamin",
    pegawai_m.nama_pegawai AS "DPJP",
    koreksidiagnosa_t.diag_asal_masuk AS "Diagnosa Masuk Dokter",
    koreksidiagnosa_t.diag_asal_utama AS "Diagnosa Utama Dokter",
    koreksidiagnosa_t.diag_asal_penyerta AS "Diagnosa Penyerta Dokter",
    concat(diagnosa_utama.diagnosa_kode, ' - ', diagnosa_utama.diagnosa_nama) AS "Diagnosa Utama Setelah Coding",
    instalasi_m.instalasi_nama AS "Instalasi",
    ruangan_m.ruangan_nama AS "Ruangan",
    kelaspelayanan_m.kelaspelayanan_nama AS "Kelas Pelayanan",
    kamarruangan_m.kamarruangan_nokamar AS "Kamar",
    asalrujukan_m.asalrujukan_nama AS "Asal Rujukan",
    rujukanbpjs_t.dirujukke_nama AS "Rujukan Keluar",
    pendidikan_m.pendidikan_nama AS "Pendidikan",
    lkp_agama.lookup_name AS "Agama",
    carakeluar_m.carakeluar_nama AS "Cara Pulang",
    lkp_kunjungan.lookup_name AS "Kunjungan",
    concat(COALESCE((pasienpulang_t.tglpasienpulang::date - (header_data.tgl_pendaftaran::date + 1))::character varying, '-'::character varying), ' Hari') AS "Hari Rawat",
        CASE
            WHEN header_data.tgl_pendaftaran::date = pasienpulang_t.tglpasienpulang THEN '1 Hari'::text
            ELSE concat(COALESCE((pasienpulang_t.tglpasienpulang::date - header_data.tgl_pendaftaran::date)::character varying, '-'::character varying), ' Hari')
        END AS "Lama Rawat"
   FROM header_data
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin,
            a.pendidikan_id,
            a.agama
           FROM pasien_m a) pasien_m ON header_data.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.golonganumur_id,
            a.golonganumur_nama
           FROM golonganumur_m a) golonganumur_m ON header_data.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_jeniskelamin ON pasien_m.jeniskelamin::integer = lkp_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang,
            a.carakeluar_id
           FROM pasienpulang_t a
          WHERE a.pasienbatalpulang_id IS NULL AND a.is_deleted = false) pasienpulang_t ON header_data.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON header_data.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON header_data.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON header_data.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            string_agg(a.diag_asal_masuk, ', '::text) AS diag_asal_masuk,
            string_agg(a.diag_asal_utama, ', '::text) AS diag_asal_utama,
            string_agg(a.diag_asal_penyerta, ', '::text) AS diag_asal_penyerta
           FROM koreksidiagnosa_t a
          WHERE a.kelompokdiagnosa_id <> 2 AND a.is_deleted = false
          GROUP BY a.pendaftaran_id) koreksidiagnosa_t ON header_data.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama
           FROM koreksidiagnosa_t a
             JOIN ( SELECT b.diagnosa_id,
                    b.diagnosa_kode,
                    b.diagnosa_nama
                   FROM diagnosa_m b) diagnosa_m ON a.diagnosa_id = diagnosa_m.diagnosa_id
          WHERE a.kelompokdiagnosa_id = 2 AND a.is_deleted = false) diagnosa_utama ON header_data.pendaftaran_id = diagnosa_utama.pendaftaran_id
     JOIN ( SELECT a.instalasi_id,
            a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON header_data.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON header_data.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON header_data.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.asalrujukan_id
           FROM rujukan_t a) rujukan_t ON header_data.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.dirujukke_nama
           FROM rujukanbpjs_t a) rujukanbpjs_t ON header_data.pendaftaran_id = rujukanbpjs_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendidikan_id,
            a.pendidikan_nama
           FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_agama ON pasien_m.agama::integer = lkp_agama.lookup_id
     LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_kunjungan ON header_data.kunjungan::integer = lkp_kunjungan.lookup_id;
