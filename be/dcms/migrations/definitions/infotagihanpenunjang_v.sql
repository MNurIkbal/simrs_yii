-- public.infotagihanpenunjang_v source

CREATE OR REPLACE VIEW public.infotagihanpenunjang_v
AS SELECT tagihan_penunjang.pendaftaran_id,
    tagihan_penunjang.tgl_pendaftaran,
    tagihan_penunjang.tglmasukpenunjang,
    tagihan_penunjang.no_pendaftaran,
    tagihan_penunjang.jeniskasuspenyakit_nama,
    tagihan_penunjang.kelaspelayanan_nama,
    tagihan_penunjang.dokter,
    tagihan_penunjang.ruang_pendaftaran,
    tagihan_penunjang.instalasi_nama,
    tagihan_penunjang.ruangan_nama,
    tagihan_penunjang.no_rekam_medik,
    tagihan_penunjang.nama_pasien,
    tagihan_penunjang.carabayar_nama,
    tagihan_penunjang.penjamin_nama,
    tagihan_penunjang.jumlah_tagihan,
    tagihan_penunjang.pasienmasukpenunjang_id,
    tagihan_penunjang.jeniskelamin,
    tagihan_penunjang.jenis_kelamin,
    tagihan_penunjang.umur,
    tagihan_penunjang.tanggal_lahir,
    tagihan_penunjang.no_masukpenunjang,
    tagihan_penunjang.ruangan_id,
    tagihan_penunjang.carabayar_kode_warna
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai AS dokter,
            ruang_pendaftaran.ruangan_nama AS ruang_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) + sum(COALESCE(obatalkespasien_t.hargajual_oa, 0::double precision)) AS jumlah_tagihan,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.ruangan_id,
            carabayar_m.carabayar_kode_warna
           FROM pasienmasukpenunjang_t
             JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(COALESCE(x.tarif_tindakan, 0::double precision)) AS tarif_tindakan
                   FROM ( SELECT m.pendaftaran_id,
                            sum(m.tarif_tindakan) AS tarif_tindakan
                           FROM tindakanpelayanan_t m
                          WHERE m.is_deleted IS FALSE AND m.tindakansudahbayar_id IS NULL
                          GROUP BY m.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(m.tarif_tindakan) AS tarif_tindakan
                           FROM tindakanpelayanan_t m
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id
                                   FROM gabungpelayanandetail_t a
                                  WHERE a.is_deleted = false) gabungpelayanandetail_t_1 ON m.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE m.is_deleted IS FALSE AND m.tindakansudahbayar_id IS NULL
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.hargajual_oa) AS hargajual_oa
                   FROM ( SELECT n.pendaftaran_id,
                            sum(COALESCE(n.hargajual_oa, 0::double precision)) AS hargajual_oa
                           FROM obatalkespasien_t n
                          WHERE n.is_deleted = false AND n.obatsudahbayar_id IS NULL
                          GROUP BY n.pendaftaran_id
                        UNION ALL
                         SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                            sum(COALESCE(n.hargajual_oa, 0::double precision)) AS hargajual_oa
                           FROM obatalkespasien_t n
                             JOIN ( SELECT a.pendaftaran_id,
                                    a.ref_pendaftaran_id
                                   FROM gabungpelayanandetail_t a
                                  WHERE a.is_deleted = false) gabungpelayanandetail_t_1 ON n.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                          WHERE n.is_deleted IS FALSE AND n.obatsudahbayar_id IS NULL
                          GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ruangan_m ruang_pendaftaran ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruang_pendaftaran.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE pendaftaran_t.status_bayar = 349 AND (pendaftaran_t.status_periksa::text <> ALL (ARRAY['402'::character varying::text, '628'::character varying::text])) AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted IS FALSE))
          GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang, carabayar_m.carabayar_kode_warna) tagihan_penunjang
  WHERE tagihan_penunjang.jumlah_tagihan > 0::double precision;