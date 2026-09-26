-- public.infopasienoperasi_v source

CREATE OR REPLACE VIEW public.infopasienoperasi_v
AS SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
    COALESCE(penjamin_ri.penjamin_nama, penjamin_rj.penjamin_nama) AS penjamin_nama,
    COALESCE(penjamin_ri.carabayar_id, penjamin_rj.carabayar_id) AS carabayar_id,
    COALESCE(carabayar_ri.carabayar_nama, carabayar_rj.carabayar_nama) AS carabayar_nama,
    COALESCE(kelas_ditagihkan.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
    COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelas_ri.kelaspelayanan_nama, kelas_rj.kelaspelayanan_nama) AS kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by,
    COALESCE(ruangan_m.kode_ruangan_bpjs, 'BED'::text::character varying) AS kode_poli,
    COALESCE(bpjs_ri.nokartuasuransi, bpjs_rjrd.nokartuasuransi) AS no_peserta,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    rencanaoperasi_t.jam_rencana_mulai AS jam_mulai,
    rencanaoperasi_t.jam_rencana_selesai AS jam_selesai,
    ruang_penunjang.ruangan_nama AS ruangan_operasi,
    COALESCE(verif_operasi.pemeriksaan, order_operasi.pemeriksaan) AS pemeriksaan,
    pendaftaranol_t.no_pendaftaranol AS no_pendaftaran_ol,
    pasienmasukpenunjang_t.last_modified_date,
    pendaftaranol_t.jenis_reservasi,
    COALESCE(carabayar_ri.carabayar_kode_warna, carabayar_rj.carabayar_kode_warna) AS carabayar_kode_warna,
    kamarruangan_m.kelaspelayanan_id AS kelaspelayanankamar_id,
        CASE
            WHEN anestesi_t.pasienmasukpenunjang_id IS NOT NULL THEN 'Sudah Proses'::text
            ELSE 'Belum Proses'::text
        END AS proses_status_anestesi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa::text = '483'::text THEN 1
            ELSE 0
        END AS status_operasi
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.tgl_permintaan,
            a.rencanaoperasi_id,
            a.dr_operator_id,
            a.dr_anastesi_id,
            a.jam_rencana_mulai,
            a.jam_rencana_selesai
           FROM rencanaoperasi_t a) rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
     JOIN ( SELECT a.pasienkirimkeunitlain_id,
            a.tgl_kirimpasien,
            a.no_orderkeunitlain,
            a.status_penunjang,
            a.pegawai_id
           FROM pasienkirimkeunitlain_t a
          WHERE a.instalasi_id = 12) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.pendaftaran_id,
            a.penjamin_id,
            a.jeniskasuspenyakit_id,
            a.kelaspelayanan_id,
            a.bpjs_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.umur,
            a.label_gelang
           FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.kelas_ditagihkan_id,
            a.is_pasientitipan,
            a.pegawai_id,
            a.bpjs_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.photopasien,
            a.jeniskelamin,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.kode_ruangan_bpjs
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN ( SELECT jeniskasuspenyakit_m_1.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m_1.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m jeniskasuspenyakit_m_1) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama
           FROM ruangan_m ruangan_m_1) ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
     LEFT JOIN ( SELECT penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            penjamin_m.carabayar_id
           FROM penjamin_m) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN ( SELECT carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            carabayar_m.carabayar_kode_warna
           FROM carabayar_m) carabayar_rj ON penjamin_rj.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN ( SELECT kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM kelaspelayanan_m) kelas_rj ON pendaftaran_t.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
     LEFT JOIN ( SELECT penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            penjamin_m.carabayar_id
           FROM penjamin_m) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN ( SELECT carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            carabayar_m.carabayar_kode_warna
           FROM carabayar_m) carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN ( SELECT kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM kelaspelayanan_m) kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id, a.pegawai_id) a.pendaftaran_id,
            a.pegawai_id,
            a.a_diag_utama
           FROM cppt_t a
          WHERE a.is_deleted = false AND a.is_active = true AND a.is_instruksi_pulang = false) cppt_t ON pasienmasukpenunjang_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar,
            a.kelaspelayanan_id
           FROM kamarruangan_m a) kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT bpjs_t.bpjs_id,
            bpjs_t.nokartuasuransi
           FROM bpjs_t) bpjs_rjrd ON pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id
     LEFT JOIN ( SELECT bpjs_t.bpjs_id,
            bpjs_t.nokartuasuransi
           FROM bpjs_t) bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
     LEFT JOIN ( SELECT pendaftaranol_t_1.pendaftaran_id,
            pendaftaranol_t_1.no_pendaftaranol,
            pendaftaranol_t_1.jenis_reservasi
           FROM pendaftaranol_t pendaftaranol_t_1) pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            pegawai_login.nama_pegawai
           FROM loginpemakai_k a
             JOIN pegawai_m pegawai_login ON a.pegawai_id = pegawai_login.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT daftartindakan_m.daftartindakan_nama AS tindakan,
                            golonganoperasi_m.golonganoperasi_nama AS golongan_operasi
                           FROM permintaankepenunjang_t
                             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             JOIN operasi_m ON daftartindakan_m.daftartindakan_id = operasi_m.daftartindakan_id
                             JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
                          WHERE permintaankepenunjang_t.is_deleted = false AND permintaankepenunjang_t.pasienkirimkeunitlain_id = a.pasienkirimkeunitlain_id) x) AS pemeriksaan
           FROM pasienkirimkeunitlain_t a) order_operasi ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = order_operasi.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT verifikasibedah_r.operasi_nama AS tindakan,
                            verifikasibedah_r.golonganoperasi_nama AS golongan_operasi
                           FROM verifikasibedah_r
                          WHERE verifikasibedah_r.is_deleted = false AND verifikasibedah_r.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id) x) AS pemeriksaan
           FROM pasienmasukpenunjang_t a) verif_operasi ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = verif_operasi.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id
           FROM anestesi_t a
          WHERE a.is_deleted = false) anestesi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = anestesi_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama
           FROM kelaspelayanan_m) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id AND pasienadmisi_t.is_pasientitipan = true
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id AS penjamin_id,
    carabayar_m.carabayar_nama AS penjamin_nama,
    pendaftaran_t.penjamin_id AS carabayar_id,
    penjamin_m.penjamin_nama AS carabayar_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by,
    COALESCE(ruangan_asal.kode_ruangan_bpjs, 'BED'::text::character varying) AS kode_poli,
    bpjs_rjrd.nokartuasuransi AS no_peserta,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    rencanaoperasi_t.jam_rencana_mulai AS jam_mulai,
    rencanaoperasi_t.jam_rencana_selesai AS jam_selesai,
    ruangan_penunjang.ruangan_nama AS ruangan_operasi,
    NULL::json AS pemeriksaan,
    pendaftaranol_t.no_pendaftaranol AS no_pendaftaran_ol,
    pasienmasukpenunjang_t.last_modified_date,
    pendaftaranol_t.jenis_reservasi,
    carabayar_m.carabayar_kode_warna,
    kamarruangan_m.kelaspelayanan_id AS kelaspelayanankamar_id,
        CASE
            WHEN anestesi_t.pasienmasukpenunjang_id IS NOT NULL THEN 'Sudah Proses'::text
            ELSE 'Belum Proses'::text
        END AS proses_status_anestesi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa::text = '483'::text THEN 1
            ELSE 0
        END AS status_operasi
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ruangan_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN bpjs_t bpjs_rjrd ON pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id
     LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id
           FROM anestesi_t a
          WHERE a.is_deleted = false) anestesi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = anestesi_t.pasienmasukpenunjang_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND ruangan_penunjang.instalasi_id = 12 AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND pendaftaran_t.instalasi_id <> 12;
