-- public.infotagihanpenunjang_v source

CREATE OR REPLACE VIEW public.infotagihanpenunjang_v
AS SELECT pendaftaran_t.pendaftaran_id,
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
    carabayar_m.carabayar_kode_warna,
    pendaftaran_t.is_close_bill
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t_1.tarif_tindakan, 0::double precision)) AS tarif_tindakan
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
          WHERE tindakanpelayanan_t_1.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t_1.is_deleted = false
          GROUP BY tindakanpelayanan_t_1.tindakanpelayanan_id, tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT obatalkespasien_t_1.pasienmasukpenunjang_id,
            sum(COALESCE(obatalkespasien_t_1.hargajual_oa, 0::double precision)) AS hargajual_oa
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE obatalkespasien_t_1.obatsudahbayar_id IS NULL AND obatalkespasien_t_1.is_deleted = false
          GROUP BY obatalkespasien_t_1.pasienmasukpenunjang_id) obatalkespasien_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = obatalkespasien_t.pasienmasukpenunjang_id
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
  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang, carabayar_m.carabayar_kode_warna, pendaftaran_t.is_close_bill;