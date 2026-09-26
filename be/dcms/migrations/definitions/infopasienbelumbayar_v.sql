-- public.infopasienbelumbayar_v source

CREATE OR REPLACE VIEW public.infopasienbelumbayar_v
AS  SELECT belumbayar.pendaftaran_id,
    belumbayar.pasienadmisi_id,
    belumbayar.no_pendaftaran,
    belumbayar.tgl_pendaftaran,
    belumbayar.nama_pasien,
    belumbayar.no_rekam_medik,
    belumbayar.tanggal_lahir,
    belumbayar.jenis_kelamin,
    belumbayar.carabayar_nama,
    belumbayar.penjamin_nama,
    belumbayar.nama_dokter,
    belumbayar.instalasi_nama,
    belumbayar.ruangan_nama,
    belumbayar.status_periksa_id,
    belumbayar.status_periksa,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM gabungpelayanandetail_t
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = belumbayar.pendaftaran_id
             LIMIT 1)) THEN (COALESCE(belumbayar.total_tindakan, 0::numeric) + COALESCE(belumbayar.total_obat, 0::numeric))::double precision + COALESCE(( SELECT sum(x.tarif) AS tarif_gabung
               FROM ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                        gabungpelayanandetail_t.ref_pendaftaran_id,
                        COALESCE(sum(tindakanpelayanan_t.tarif_tindakan), 0::double precision) AS tarif
                       FROM gabungpelayanandetail_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.tarif_tindakan,
                                a.is_deleted,
                                a.is_active,
                                a.tindakansudahbayar_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON gabungpelayanandetail_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.is_active IS TRUE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND gabungpelayanandetail_t.is_deleted IS FALSE
                      GROUP BY gabungpelayanandetail_t.ref_pendaftaran_id, gabungpelayanandetail_t.pendaftaran_id
                    UNION
                     SELECT gabungpelayanandetail_t.pendaftaran_id,
                        gabungpelayanandetail_t.ref_pendaftaran_id,
                        COALESCE(sum(obatalkespasien_t.hargajual_oa), 0::double precision) AS tarif
                       FROM gabungpelayanandetail_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.hargajual_oa,
                                a.is_deleted,
                                a.is_active,
                                a.obatsudahbayar_id
                               FROM obatalkespasien_t a
                              WHERE a.obatsudahbayar_id IS NULL AND a.is_deleted IS FALSE AND a.is_active IS TRUE) obatalkespasien_t ON gabungpelayanandetail_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.is_active IS TRUE AND gabungpelayanandetail_t.is_deleted IS FALSE
                      GROUP BY gabungpelayanandetail_t.ref_pendaftaran_id, gabungpelayanandetail_t.pendaftaran_id) x
              WHERE x.ref_pendaftaran_id = belumbayar.pendaftaran_id), 0::double precision)
            ELSE (COALESCE(belumbayar.total_tindakan, 0::numeric) + COALESCE(belumbayar.total_obat, 0::numeric))::double precision
        END AS total_tagihan,
    COALESCE(belumbayar.uang_masuk, 0::double precision) AS uang_masuk,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM gabungpelayanandetail_t
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = belumbayar.pendaftaran_id
             LIMIT 1)) THEN (COALESCE(belumbayar.total_tindakan, 0::numeric) + COALESCE(belumbayar.total_obat, 0::numeric))::double precision + COALESCE(( SELECT sum(x.tarif) AS tarif_gabung
               FROM ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                        gabungpelayanandetail_t.ref_pendaftaran_id,
                        COALESCE(sum(tindakanpelayanan_t.tarif_tindakan), 0::double precision) AS tarif
                       FROM gabungpelayanandetail_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.tarif_tindakan,
                                a.is_deleted,
                                a.is_active,
                                a.tindakansudahbayar_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON gabungpelayanandetail_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.is_active IS TRUE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND gabungpelayanandetail_t.is_deleted IS FALSE
                      GROUP BY gabungpelayanandetail_t.ref_pendaftaran_id, gabungpelayanandetail_t.pendaftaran_id
                    UNION
                     SELECT gabungpelayanandetail_t.pendaftaran_id,
                        gabungpelayanandetail_t.ref_pendaftaran_id,
                        COALESCE(sum(obatalkespasien_t.hargajual_oa), 0::double precision) AS tarif
                       FROM gabungpelayanandetail_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.hargajual_oa,
                                a.is_deleted,
                                a.is_active,
                                a.obatsudahbayar_id
                               FROM obatalkespasien_t a
                              WHERE a.obatsudahbayar_id IS NULL AND a.is_deleted IS FALSE AND a.is_active IS TRUE) obatalkespasien_t ON gabungpelayanandetail_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.is_active IS TRUE AND gabungpelayanandetail_t.is_deleted IS FALSE
                      GROUP BY gabungpelayanandetail_t.ref_pendaftaran_id, gabungpelayanandetail_t.pendaftaran_id) x
              WHERE x.ref_pendaftaran_id = belumbayar.pendaftaran_id), 0::double precision) - COALESCE(belumbayar.uang_masuk, 0::double precision)
            ELSE (COALESCE(belumbayar.total_tindakan, 0::numeric) + COALESCE(belumbayar.total_obat, 0::numeric))::double precision - COALESCE(belumbayar.uang_masuk, 0::double precision)
        END AS sisa_tagihan,
    belumbayar.kelola_tagihan,
        CASE
            WHEN (COALESCE(belumbayar.total_tindakan, 0::numeric) + COALESCE(belumbayar.total_obat, 0::numeric)) >= belumbayar.kelola_tagihan::numeric THEN true
            ELSE false
        END AS is_kelola_tagihan,
    belumbayar.instalasi_id,
    belumbayar.ruangan_id,
    belumbayar.limit_tagihan,
    belumbayar.no_sep,
    belumbayar.pasienpulang_id,
    belumbayar.is_stopakomodasi,
    COALESCE(belumbayar.uang_muka, 0::double precision) - COALESCE(belumbayar.penggunaan_uangmuka, 0::double precision) AS uang_muka,
    belumbayar.count_tagihan,
    belumbayar.is_aps,
    belumbayar.penjamin_id,
    belumbayar.kelaspelayanan_id,
    belumbayar.ref_no_pendaftaran,
    belumbayar.is_pasientitipan,
    belumbayar.hak_kelas,
    belumbayar.kelaspelayanan_nama,
    belumbayar.kelas_tagihan,
    belumbayar.is_stoptitipan,
    belumbayar.carabayar_kode_warna,
    belumbayar.carabayar_id,
    belumbayar.is_close_bill,
    belumbayar.status_kamar,
    belumbayar.kamarruangan_nokamar,
    belumbayar.no_tempattidur,
    belumbayar.status_kelas,
    belumbayar.status_approve_id,
    belumbayar.status_approve_nama
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            COALESCE(pasienadmisi_t.tgl_admisi, pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir,
            jk.lookup_name AS jenis_kelamin,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_1.carabayar_nama
                    ELSE cb_2.carabayar_nama
                END AS carabayar_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_1.penjamin_nama
                    ELSE pj_2.penjamin_nama
                END AS penjamin_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dr_1.nama_pegawai
                    ELSE dr_2.nama_pegawai
                END AS nama_dokter,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.is_aps = true THEN pasienmasukpenunjang_t.status_periksa::integer
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.status_periksa::integer
                    ELSE pasienadmisi_t.status_ranap
                END AS status_periksa_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.is_aps = true THEN status_penunjang.lookup_name
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN status_pendaftaran.lookup_name
                    ELSE status_admisi.lookup_name
                END AS status_periksa,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                       FROM tindakanpelayanan_t a
                      WHERE a.is_deleted IS FALSE AND a.tindakansudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS "coalesce"
                       FROM obatalkespasien_t a
                      WHERE a.is_deleted IS FALSE AND a.obatsudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_obat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pembayaran_t
                      WHERE pembayaran_t.is_deleted = false AND pembayaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT sum(pembayaran_t.total_dibayar - pembayaran_t.total_kembalian + pembayaran_t.total_dijamin - pembayaran_t.total_pembulatan + pembayaran_t.total_discountpembayaran) AS total_uangmasuk
                       FROM pembayaran_t
                      WHERE pembayaran_t.is_deleted = false AND pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                      GROUP BY pembayaran_t.pendaftaran_id)
                    ELSE 0::double precision
                END AS uang_masuk,
            konfigsystem_k.kelola_tagihan,
            ruangan_m.instalasi_id,
            ruangan_m.ruangan_id,
            COALESCE(pasienadmisi_t.limit_tagihan, pendaftaran_t.limit_tagihan) AS limit_tagihan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_t.nosep
                    ELSE bpjs_admisi.nosep
                END AS no_sep,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pasienpulang_id
                    ELSE pasienadmisi_t.pasienpulang_id
                END AS pasienpulang_id,
            pendaftaran_t.is_stopakomodasi,
            COALESCE(uang_muka.uang_muka, 0::double precision) AS uang_muka,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM pembayaran_t
                      WHERE pembayaran_t.is_deleted = false AND pembayaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT sum(pembayaran_t.penggunaan_uangmuka) AS sum
                       FROM pembayaran_t
                      WHERE pembayaran_t.is_deleted = false AND pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)
                    ELSE 0::double precision
                END AS penggunaan_uangmuka,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM obatalkespasien_t
                          WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS count_tagihan,
            pendaftaran_t.is_aps,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pj_1.penjamin_id
                    ELSE pj_2.penjamin_id
                END AS penjamin_id,
            COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
            gabungpelayanandetail_t.ref_no_pendaftaran,
            pasienadmisi_t.is_pasientitipan,
                CASE
                    WHEN kelas_ditagihkan.kelaspelayanan_nama IS NULL THEN ('Kelas '::text || (((((bpjs_t.additional_data::json -> 'sep'::text) -> 'klsRawat'::text) ->> 'klsRawatHak'::text)::character varying)::text))::character varying
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS hak_kelas,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelas_admisi.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, '-'::character varying) AS kelas_tagihan,
            pasienadmisi_t.is_stoptitipan,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN cb_1.carabayar_kode_warna
                    ELSE cb_2.carabayar_kode_warna
                END AS carabayar_kode_warna,
            COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) AS carabayar_id,
            pendaftaran_t.is_close_bill,
            ket_kamar.lookup_name AS status_kamar,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
                CASE
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) AND pasienadmisi_t.is_aps = true THEN 'APS / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) AND pasienadmisi_t.is_aps = true THEN 'APS / Naik Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) THEN 'Titipan / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) IS NULL THEN
                    CASE
                        WHEN pasienadmisi_t.is_aps = true THEN 'APS / Naik Kelas'::text
                        WHEN pasienadmisi_t.is_aps = false AND pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                        ELSE 'Sesuai Kelas'::text
                    END
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) THEN
                    CASE
                        WHEN pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                        ELSE NULL::text
                    END
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) THEN 'Titipan / Naik Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelas_admisi.bpjs_kelas) THEN 'Titipan / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND COALESCE(kelas_admisi.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) = bpjs_t.klsrawat THEN 'Sesuai Kelas'::text
                    WHEN COALESCE(kelas_admisi.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN
                    CASE
                        WHEN pasienadmisi_t.is_aps = true AND COALESCE(kelas_admisi.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN 'APS / Naik Kelas'::text
                        WHEN pasienadmisi_t.is_aps = false AND pasienadmisi_t.is_pasientitipan = true AND COALESCE(kelas_admisi.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN 'Titipan / Naik Kelas'::text
                        ELSE 'Sesuai Kelas'::text
                    END
                    WHEN bpjs_t.klsrawat IS NULL THEN 'Non bpjs'::text
                    ELSE '-'::text
                END AS status_kelas,
            pendaftaran_t.status_bayar,
            approval_diskon.status_approve AS status_approve_id,
            approval_diskon.status_approve_nama
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.status_ranap,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.ruangan_id,
                    a.bpjs_id,
                    a.limit_tagihan,
                    a.kelaspelayanan_id,
                    a.tgl_admisi,
                    a.is_pasientitipan,
                    a.kelas_ditagihkan_id,
                    a.is_stoptitipan,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.is_aps
                   FROM pasienadmisi_t a
                  WHERE a.status_ranap <> 453) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama,
                    a.bpjs_kelas
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama,
                    a.bpjs_kelas
                   FROM kelaspelayanan_m a) kelas_admisi ON pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
             JOIN ( SELECT b.pasien_id,
                    b.nama_pasien,
                    b.no_rekam_medik,
                    b.tanggal_lahir,
                    b.jeniskelamin
                   FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT c.carabayar_id,
                    c.carabayar_nama,
                    c.carabayar_kode_warna
                   FROM carabayar_m c) cb_1 ON pendaftaran_t.carabayar_id = cb_1.carabayar_id
             LEFT JOIN ( SELECT d.carabayar_id,
                    d.carabayar_nama,
                    d.carabayar_kode_warna
                   FROM carabayar_m d) cb_2 ON pasienadmisi_t.carabayar_id = cb_2.carabayar_id
             LEFT JOIN ( SELECT e.penjamin_id,
                    e.penjamin_nama
                   FROM penjamin_m e) pj_1 ON pendaftaran_t.penjamin_id = pj_1.penjamin_id
             LEFT JOIN ( SELECT f.penjamin_id,
                    f.penjamin_nama
                   FROM penjamin_m f) pj_2 ON pasienadmisi_t.penjamin_id = pj_2.penjamin_id
             LEFT JOIN ( SELECT g.pegawai_id,
                    g.nama_pegawai
                   FROM pegawai_m g) dr_1 ON pendaftaran_t.pegawai_id = dr_1.pegawai_id
             LEFT JOIN ( SELECT h.pegawai_id,
                    h.nama_pegawai
                   FROM pegawai_m h) dr_2 ON pasienadmisi_t.pegawai_id = dr_2.pegawai_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT p.kelola_tagihan,
                    p.is_deleted
                   FROM konfigsystem_k p) konfigsystem_k ON konfigsystem_k.is_deleted = false
             LEFT JOIN ( SELECT q.bpjs_id,
                    q.nosep,
                    q.klsrawat,
                    q.additional_data
                   FROM bpjs_t q) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT r.bpjs_id,
                    r.nosep,
                    r.klsrawat
                   FROM bpjs_t r) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.uang_muka) AS uang_muka
                   FROM ( SELECT s.pendaftaran_id,
                            sum(s.jumlah_uangmuka) AS uang_muka
                           FROM bayaruangmuka_t s
                          WHERE s.is_deleted = false
                          GROUP BY s.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(s.jumlah_uangmuka) AS uang_muka
                           FROM bayaruangmuka_t s
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id
                                   FROM gabungpelayanandetail_t a
                                  WHERE a.is_deleted = false) gabungpelayanandetail_t_1 ON s.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE s.is_deleted = false
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) uang_muka ON pendaftaran_t.pendaftaran_id = uang_muka.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.status_periksa
                   FROM pasienmasukpenunjang_t a
                  GROUP BY a.pendaftaran_id, a.status_periksa) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pendaftaran_t.is_aps = true
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_penunjang ON pasienmasukpenunjang_t.status_periksa::integer = status_penunjang.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) ket_kamar ON ket_kamar.lookup_id = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar,
                    a.jeniskasuspenyakit_id,
                    a.keterangan_kamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur,
                    a.kettempattidur_id
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.ref_pendaftaran_id,
                    pendaftaran_t_1.no_pendaftaran AS ref_no_pendaftaran
                   FROM gabungpelayanandetail_t a
                     JOIN ( SELECT a_1.pendaftaran_id,
                            a_1.no_pendaftaran
                           FROM pendaftaran_t a_1) pendaftaran_t_1 ON a.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE a.is_deleted = false) gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id
             LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.kelas_ditagihkan_id,
                    kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
                    pindahkamar_t.is_stoptitipan
                   FROM pindahkamar_t
                     JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
                            pk.pasienadmisi_id
                           FROM pindahkamar_t pk
                          GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id
                  WHERE pindahkamar_t.is_deleted = false) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
             LEFT JOIN ( SELECT approvaldiskon_t.pendaftaran_id,
                    approvaldiskon_t.status_approve,
                    st_approve.lookup_name AS status_approve_nama
                   FROM approvaldiskon_t
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) st_approve ON approvaldiskon_t.status_approve = st_approve.lookup_id
                     JOIN ( SELECT approvaldiskon_t_1.pendaftaran_id,
                            max(approvaldiskon_t_1.approvaldiskon_id) AS approvaldiskon_id
                           FROM approvaldiskon_t approvaldiskon_t_1
                          GROUP BY approvaldiskon_t_1.pendaftaran_id) approvaldiskon_max ON approvaldiskon_t.approvaldiskon_id = approvaldiskon_max.approvaldiskon_id
                  WHERE approvaldiskon_t.is_deleted IS FALSE) approval_diskon ON pendaftaran_t.pendaftaran_id = approval_diskon.pendaftaran_id
          WHERE (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[628, 402])) AND pendaftaran_t.status_bayar = 349 AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t_1.pendaftaran_id
                   FROM gabungpelayanandetail_t gabungpelayanandetail_t_1
                  WHERE gabungpelayanandetail_t_1.is_deleted IS FALSE))) belumbayar