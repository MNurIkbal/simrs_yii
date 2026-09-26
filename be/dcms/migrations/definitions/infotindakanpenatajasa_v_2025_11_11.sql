CREATE VIEW "public"."infotindakanpenatajasa_v" AS SELECT 'tindakan'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS obatalkes_id,
    NULL::character varying AS obatalkes_nama,
    NULL::double precision AS qty_oa,
    NULL::double precision AS hargajual_oa,
    NULL::integer AS satuanobat_id,
    NULL::character varying AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    tindakanpelayanan_t.tarifpenyulit_tindakan,
    kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
    daftartindakan_m.is_akomodasi,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    replace(((tindakanpelayanan_t.additional_data::json ->> 'detail_akomodasi'::text)::json) ->> 'persentase'::text, '%'::text, ''::text)::integer AS qty_akomodasi,
    tindakanpelayanan_t.penjamin_id,
    tindakanpelayanan_t.kamarruangan_id,
    tindakanpelayanan_t.kamartempattidur_id,
    tindakanpelayanan_t.perawat1_id AS perawat_id,
    perawat.nama_pegawai AS perawat_nama,
    tindakanpelayanan_t.tgl_tindakan::date AS tgl_pelayanan,
    true AS is_ditagihkan,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    verifikasibedah_r.timoperasi_id,
    verifikasibedah_r.useprice,
    tindakanpelayanan_t.additional_data,
    kelompoktindakan_m.kelompoktindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_operasi.daftartindakan_nama AS tindakanoperasi_nama
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama,
            a.kelompoktindakan_id,
            a.is_akomodasi,
            a.daftartindakan_kode
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.kelompoktindakan_id,
            a.kelompoktindakan_nama
           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang,
            a.is_deleted
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND pasienmasukpenunjang_t.is_deleted = false
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON kamarruangan_m.kamarruangan_id = tindakanpelayanan_t.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON kamartempattidur_m.kamartempattidur_id = tindakanpelayanan_t.kamartempattidur_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) perawat ON tindakanpelayanan_t.perawat1_id = perawat.pegawai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.timoperasi_id,
            a.useprice,
            a.dokter_id,
            a.tindakanpelayanan_id
           FROM verifikasibedah_r a) verifikasibedah_r ON tindakanpelayanan_t.tindakanpelayanan_id = verifikasibedah_r.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.timoperasi_id
           FROM timoperasi_t a) timoperasi_t ON timoperasi_t.timoperasi_id = verifikasibedah_r.timoperasi_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_operasi ON daftartindakan_operasi.daftartindakan_id = timoperasi_t.daftartindakan_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'paket'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
CASE 
		WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN
	FALSE ELSE TRUE 
	END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL AS obatalkespasien_id,
    NULL AS obatalkes_id,
    NULL AS obatalkes_nama,
    NULL AS qty_oa,
    NULL AS hargajual_oa,
    NULL AS satuanobat_id,
    NULL AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    tindakanpelayanan_t.tarifpenyulit_tindakan,
    'PAKET'::character varying AS kelompok,
    false AS is_akomodasi,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    replace(((tindakanpelayanan_t.additional_data::json ->> 'detail_akomodasi'::text)::json) ->> 'persentase'::text, '%'::text, ''::text)::integer AS qty_akomodasi,
    tindakanpelayanan_t.penjamin_id,
    tindakanpelayanan_t.kamarruangan_id,
    tindakanpelayanan_t.kamartempattidur_id,
    tindakanpelayanan_t.perawat1_id AS perawat_id,
    perawat.nama_pegawai AS perawat_nama,
    tindakanpelayanan_t.tgl_tindakan::date AS tgl_pelayanan,
    true AS is_ditagihkan,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    NULL::integer AS timoperasi_id,
    NULL::boolean AS useprice,
    tindakanpelayanan_t.additional_data,
    NULL::integer AS kelompoktindakan_id,
    tipepaket_m.tipepaket_kode AS daftartindakan_kode,
    NULL::character varying AS tindakanoperasi_nama
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama,
            a.tipepaket_kode
           FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang,
            a.is_deleted
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id AND pasienmasukpenunjang_t.is_deleted = false
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON kamarruangan_m.kamarruangan_id = tindakanpelayanan_t.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON kamartempattidur_m.kamartempattidur_id = tindakanpelayanan_t.kamartempattidur_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) perawat ON tindakanpelayanan_t.perawat1_id = perawat.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted IS FALSE
UNION ALL
 SELECT 'obat'::text AS jenis,
    NULL::integer AS tindakanpelayanan_id,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
		 CASE
            WHEN obatalkespasien_t.tindakanpelayanan_id IS NOT NULL THEN ( SELECT tindakanpelayanan_t.daftartindakan_id
               FROM ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t
              WHERE tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id
             LIMIT 1)
            ELSE NULL::integer
        END AS daftartindakan_id,
        CASE
            WHEN obatalkespasien_t.tindakanpelayanan_id IS NOT NULL THEN ( SELECT daftartindakan_m.daftartindakan_nama
               FROM ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t
                 JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
              WHERE tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id
             LIMIT 1)
            ELSE NULL::character varying
        END AS daftartindakan_nama,
        CASE
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE
            WHEN obatalkespasien_t.obatsudahbayar_id IS NULL THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
        CASE
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    obatalkespasien_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_masukpenunjang,
    NULL::double precision AS tarifpenyulit_tindakan,
    'OBAT'::character varying AS kelompok,
    false AS is_akomodasi,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    NULL::integer AS qty_akomodasi,
    obatalkespasien_t.penjamin_id,
    NULL::integer AS kamarruangan_id,
    NULL::integer AS kamartempattidur_id,
    obatalkespasien_t.perawat1_id AS perawat_id,
    perawat.nama_pegawai AS perawat_nama,
    obatalkespasien_t.tglpelayanan::date AS tgl_pelayanan,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
    NULL::integer AS pasienmasukpenunjang_id,
    NULL::integer AS timoperasi_id,
    NULL::boolean AS useprice,
    obatalkespasien_t.additional_data,
    NULL::integer AS kelompoktindakan_id,
    obatalkes_m.obatalkes_kode AS daftartindakan_kode,
    NULL::character varying AS tindakanoperasi_nama
   FROM obatalkespasien_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.jenisobatalkes_id,
            a.obatalkes_kode
           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON obatalkespasien_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
            pegawai_m_1.nama_pegawai
           FROM pegawai_m pegawai_m_1) perawat ON obatalkespasien_t.perawat1_id = perawat.pegawai_id
  WHERE obatalkespasien_t.is_deleted IS FALSE;