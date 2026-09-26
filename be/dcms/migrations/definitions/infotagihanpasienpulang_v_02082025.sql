CREATE OR REPLACE VIEW "public"."infotagihanpasienpulang_v" AS  SELECT gabung.pendaftaran_id,
    gabung.pasienpulang_id,
    gabung.tglpasienpulang,
    gabung.no_pendaftaran,
    gabung.instalasi_id,
    gabung.instalasi_nama,
    gabung.ruanganakhir_id AS ruangan_id,
    gabung.ruangan_nama,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.groupcarabayar_id,
    gabung.groupcarabayar_nama,
    gabung.carabayar_id,
    gabung.carabayar_nama,
    gabung.penjamin_id,
    gabung.penjamin_nama,
    gabung.jeniskasuspenyakit_nama,
    gabung.status_bayar,
    gabung.kelaspelayanan_nama,
    gabung.nama_pegawai AS dokter,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM gabungpelayanandetail_t
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = gabung.pendaftaran_id AND gabungpelayanandetail_t.is_deleted IS FALSE
             LIMIT 1)) THEN gabung.total_tindakan::double precision + COALESCE(( SELECT y.tarif_gabung_tindakan
               FROM gabungpelayanandetail_t
                 JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
                        COALESCE(sum(tindakanpelayanan_t.tarif_tindakan)) AS tarif_gabung_tindakan
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.is_active IS TRUE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                      GROUP BY tindakanpelayanan_t.pendaftaran_id) y ON gabungpelayanandetail_t.pendaftaran_id = y.pendaftaran_id
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = gabung.pendaftaran_id AND gabungpelayanandetail_t.is_deleted IS FALSE), 0::double precision)
            ELSE gabung.total_tindakan::double precision
        END AS total_tindakan,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM gabungpelayanandetail_t
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = gabung.pendaftaran_id
             LIMIT 1)) THEN gabung.total_obat::double precision + COALESCE(( SELECT z.tarif_gabung_obat
               FROM gabungpelayanandetail_t
                 LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
                        COALESCE(sum(obatalkespasien_t.hargajual_oa)) AS tarif_gabung_obat
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.is_active IS TRUE AND obatalkespasien_t.obatsudahbayar_id IS NULL
                      GROUP BY obatalkespasien_t.pendaftaran_id) z ON gabungpelayanandetail_t.pendaftaran_id = z.pendaftaran_id
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = gabung.pendaftaran_id AND gabungpelayanandetail_t.is_deleted IS FALSE), 0::double precision)
            ELSE gabung.total_obat::double precision
        END AS total_obat,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM gabungpelayanandetail_t
              WHERE gabungpelayanandetail_t.ref_pendaftaran_id = gabung.pendaftaran_id
             LIMIT 1)) THEN (COALESCE(gabung.total_tindakan, 0::numeric) + COALESCE(gabung.total_obat, 0::numeric))::double precision + COALESCE(( SELECT sum(x.tarif) AS tarif_gabung
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
              WHERE x.ref_pendaftaran_id = gabung.pendaftaran_id), 0::double precision)
            ELSE (COALESCE(gabung.total_tindakan, 0::numeric) + COALESCE(gabung.total_obat, 0::numeric))::double precision
        END AS total_tagihan,
    gabung.pegawai_id,
    gabung.photopasien,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.jeniskelamin,
    gabung.jenis_kelamin,
    gabung.tgl_pendaftaran,
    gabung.is_stopakomodasi,
    gabung.tgl_stopakomodasi,
    gabung.no_sep,
    gabung.status_pulang,
    gabung.carabayar_kode_warna,
    gabung.count_tagihan,
    gabungantagihan.pendaftaran_id AS ref_pendaftaran_id,
    gabungantagihan.no_pendaftaran AS ref_no_pendaftaran,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM infotagihanpasien_r
              WHERE infotagihanpasien_r.pendaftaran_id = gabung.pendaftaran_id
             LIMIT 1)) THEN true
            ELSE false
        END AS is_invoice,
    gabung.is_close_bill,
    gabung.status_kamar,
    gabung.kamarruangan_nokamar,
    gabung.no_tempattidur,
    gabung.pasienadmisi_id,
    gabung.hak_kelas,
    gabung.kelas_tagihan,
    gabung.is_pasientitipan,
    gabung.status_kelas,
    approval_diskon.status_approve AS status_approve_id,
    approval_diskon.status_approve_nama
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pulang_rjrd.tglpasienpulang) AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_ri.instalasi_id, ruangan_rjrd.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_ri.instalasi_nama, instalasi_rjrd.instalasi_nama) AS instalasi_nama,
            COALESCE(ruangan_ri.ruangan_id, ruangan_rjrd.ruangan_id) AS ruanganakhir_id,
            COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            COALESCE(carabayar_ri.groupcarabayar_id, carabayar_rjrd.groupcarabayar_id) AS groupcarabayar_id,
            COALESCE(groupcarabayar_ri.lookup_name, groupcarabayar_rjrd.lookup_name) AS groupcarabayar_nama,
            COALESCE(penjamin_ri.carabayar_id, penjamin_rjrd.carabayar_id) AS carabayar_id,
            COALESCE(carabayar_ri.carabayar_nama, carabayar_rjrd.carabayar_nama) AS carabayar_nama,
            COALESCE(carabayar_ri.carabayar_kode_warna, carabayar_rjrd.carabayar_kode_warna) AS carabayar_kode_warna,
            COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            status_pembayaran.lookup_name AS status_bayar,
            COALESCE(kelas_ditagihkan.kelaspelayanan_nama, kelaspelayanan_rjrd.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(pegawai_ri.nama_pegawai, pegawai_rjrd.nama_pegawai) AS nama_pegawai,
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
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            jk.lookup_name AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            NULL::text AS sudah_bayar,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            COALESCE(status_admisi.lookup_name, status_pendaftaran.lookup_name) AS status_pulang,
            pendaftaran_t.pasienadmisi_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN 1
                    ELSE
                    CASE
                        WHEN (EXISTS ( SELECT 1
                           FROM obatalkespasien_t
                          WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                         LIMIT 1)) THEN 1
                        ELSE 0
                    END
                END AS count_tagihan,
            pendaftaran_t.is_close_bill,
            ket_kamar.lookup_name AS status_kamar,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
                CASE
                    WHEN kelaspelayanan_ri.kelaspelayanan_nama IS NULL THEN ('Kelas '::text || (((((bpjs_ranap.additional_data::json -> 'sep'::text) -> 'klsRawat'::text) ->> 'klsRawatHak'::text)::character varying)::text))::character varying
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS hak_kelas,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_tagihan,
            pasienadmisi_t.is_pasientitipan,
                CASE
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) AND pasienadmisi_t.is_aps = true THEN 'APS / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) AND pasienadmisi_t.is_aps = true THEN 'APS / Naik Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) THEN 'Titipan / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) IS NULL THEN
                    CASE
                        WHEN pasienadmisi_t.is_aps = true THEN 'APS / Naik Kelas'::text
                        WHEN pasienadmisi_t.is_aps = false AND pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                        ELSE 'Sesuai Kelas'::text
                    END
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) THEN
                    CASE
                        WHEN pasienadmisi_t.is_pasientitipan = true THEN 'Titipan / Naik Kelas'::text
                        ELSE NULL::text
                    END
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat > COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) THEN 'Titipan / Naik Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND bpjs_t.klsrawat < COALESCE(kelas_ditagihkan.bpjs_kelas, kelaspelayanan_ri.bpjs_kelas) THEN 'Titipan / Turun Kelas'::text
                    WHEN pindah_kamar.kelas_ditagihkan_id IS NOT NULL AND COALESCE(kelaspelayanan_ri.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) = bpjs_t.klsrawat THEN 'Sesuai Kelas'::text
                    WHEN COALESCE(kelaspelayanan_ri.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN
                    CASE
                        WHEN pasienadmisi_t.is_aps = true AND COALESCE(kelaspelayanan_ri.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN 'APS / Naik Kelas'::text
                        WHEN pasienadmisi_t.is_aps = false AND pasienadmisi_t.is_pasientitipan = true AND COALESCE(kelaspelayanan_ri.bpjs_kelas, kelas_ditagihkan.bpjs_kelas) > bpjs_t.klsrawat THEN 'Titipan / Naik Kelas'::text
                        ELSE 'Sesuai Kelas'::text
                    END
                    WHEN bpjs_t.klsrawat IS NULL THEN 'Non bpjs'::text
                    ELSE '-'::text
                END AS status_kelas
           FROM pendaftaran_t
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.penjamin_id,
                    a.ruangan_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.status_ranap,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.kelas_ditagihkan_id,
                    a.is_pasientitipan,
                    a.bpjs_id,
                    a.is_aps
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id
                   FROM pasienpulang_t a) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id
                   FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.photopasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_rjrd ON pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_rjrd ON ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_rjrd ON pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_rjrd ON penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) groupcarabayar_rjrd ON carabayar_rjrd.groupcarabayar_id = groupcarabayar_rjrd.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) groupcarabayar_ri ON carabayar_ri.groupcarabayar_id = groupcarabayar_ri.lookup_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_rjrd ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_rjrd.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama,
                    a.bpjs_kelas
                   FROM kelaspelayanan_m a) kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama,
                    a.bpjs_kelas
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_rjrd ON pendaftaran_t.pegawai_id = pegawai_rjrd.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep,
                    a.klsrawat
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat,
                    a.additional_data,
                    a.nosep
                   FROM bpjs_t a) bpjs_ranap ON pasienadmisi_t.bpjs_id = bpjs_ranap.bpjs_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pendaftaran ON pendaftaran_t.status_periksa::integer = status_pendaftaran.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_admisi ON pasienadmisi_t.status_ranap = status_admisi.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_pembayaran ON pendaftaran_t.status_bayar = status_pembayaran.lookup_id
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
             JOIN ( SELECT DISTINCT tindakanpelayanan_t.pendaftaran_id
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pendaftaran_id IS NOT NULL
                UNION
                 SELECT DISTINCT obatalkespasien_t.pendaftaran_id
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.pendaftaran_id IS NOT NULL) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id
          WHERE pendaftaran_t.status_periksa::text <> ALL (ARRAY['402'::character varying::text, '628'::character varying::text])) gabung
     LEFT JOIN ( SELECT DISTINCT gabungpelayanandetail_t.ref_pendaftaran_id,
            gabungpelayanandetail_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran
           FROM gabungpelayanandetail_t
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran
                   FROM pendaftaran_t a) pendaftaran_t ON gabungpelayanandetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE gabungpelayanandetail_t.is_deleted IS FALSE) gabungantagihan ON gabung.pendaftaran_id = gabungantagihan.ref_pendaftaran_id
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
          WHERE approvaldiskon_t.is_deleted IS FALSE) approval_diskon ON gabung.pendaftaran_id = approval_diskon.pendaftaran_id
  WHERE (gabung.pasienpulang_id IS NOT NULL AND gabung.pasienadmisi_id IS NULL OR gabung.is_stopakomodasi = true AND gabung.pasienadmisi_id IS NOT NULL) AND NOT (gabung.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
           FROM gabungpelayanandetail_t
          WHERE gabungpelayanandetail_t.is_deleted IS FALSE));