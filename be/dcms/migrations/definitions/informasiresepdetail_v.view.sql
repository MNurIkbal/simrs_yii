-- public.informasiresepdetail_v source

CREATE OR REPLACE VIEW public.informasiresepdetail_v
AS SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    resepturdetail_t.is_kronis,
        CASE
            WHEN resepturdetail_t.signa_id IS NULL THEN resepturdetail_t.signa ->> 'id'::text
            ELSE resepturdetail_t.signa_id::text
        END AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    reseptur_t.noresep,
    reseptur_t.tglreseptur,
    racikan_m.racikan_nama,
    resepturdetail_t.r,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    resepturdetail_t.hargasatuan_reseptur AS hargajual_satuan,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    resepturdetail_t.etiket,
    resepturdetail_t.iter,
        CASE
            WHEN resepturdetail_t.signa IS NOT NULL THEN resepturdetail_t.signa ->> 'text'::text
            ELSE NULL::text
        END AS signa_nama,
    resepturdetail_t.signa,
    signaobat_m.qty_obat AS qty_obat_signa,
    signaobat_m.iterasi AS iterasi_signa,
    reseptur_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    rotd_t.interaksi,
    rotd_t.duplikasi,
    rotd_t.dosisi AS dosis,
    rotd_t.alergi,
    rotd_t.kontradiksi,
    rotd_t.review_note,
    rotd_t.wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (resepturdetail_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (resepturdetail_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (resepturdetail_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (resepturdetail_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (resepturdetail_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    0::double precision AS biayaadministrasiresep,
    0::double precision AS totalhargajualresep,
    0::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa,
    reseptur_t.ruanganreseptur_id AS ruanganasal_id,
    ruangan_asal.instalasi_id AS instalasiasal_id,
    resepturdetail_t.det,
    resepturdetail_t.det_konversi,
    0::double precision AS qty_tersedia,
        CASE
            WHEN resepturdetail_t.qty_medis IS NOT NULL THEN resepturdetail_t.qty_medis
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_transaksi,
    resepturdetail_t.nama_racikan,
    resepturdetail_t.qty_racikan,
    resepturdetail_t.satuan_racikan_id,
    resepturdetail_t.det_medis AS det_transaksi,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama,
    COALESCE(resepturdetail_t.last_modified_date, resepturdetail_t.created_date) AS tgl_update_resep,
    COALESCE(resepturdetail_t.last_modified_by, resepturdetail_t.created_by) AS user_update_resep,
    COALESCE(user_update.kelompokpegawai_id, user_create.kelompokpegawai_id) AS kelompokpegawai_user_resep
   FROM resepturdetail_t
     JOIN ( SELECT a.reseptur_id,
            a.pendaftaran_id,
            a.pasien_id,
            a.ruangan_id,
            a.ruanganreseptur_id,
            a.noresep,
            a.tglreseptur,
            a.status_reseptur
           FROM reseptur_t a) reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.status_periksa
           FROM pendaftaran_t a) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON resepturdetail_t.satuan_racikan_id = satuan_racikan.satuanunit_id
     JOIN ( SELECT a.racikan_id,
            a.racikan_nama
           FROM racikan_m a) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT a.signa_id,
            a.signa_nama,
            a.qty_obat,
            a.iterasi
           FROM signaobat_m a) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.resepturdetail_id,
            a.pegawairotd_id,
            a.interaksi,
            a.duplikasi,
            a.dosisi,
            a.alergi,
            a.kontradiksi,
            a.review_note,
            a.wkt_review
           FROM rotd_t a) rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON rotd_t.pegawairotd_id = rotd_t.pegawairotd_id
     LEFT JOIN ( SELECT a.resepturdetail_id,
            a.additional_data,
            a.hargajual_oa,
            a.hargasatuan_oa
           FROM obatalkespasien_t a) obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
     JOIN ( SELECT a.is_deleted,
            a.persen_diskon,
            a.persenppn
           FROM konfigfarmasi_k a) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.nama_pemakai,
            pegawai.pegawai_id,
            pegawai.nama_pegawai,
            jenis_pegawai.kelompokpegawai_id
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m_1.pegawai_id,
                    pegawai_m_1.nama_pegawai,
                    pegawai_m_1.kelompokpegawai_id
                   FROM pegawai_m pegawai_m_1) pegawai ON pegawai.pegawai_id = a.pegawai_id
             JOIN ( SELECT kelompokpegawai_m.kelompokpegawai_id,
                    kelompokpegawai_m.kelompokpegawai_nama
                   FROM kelompokpegawai_m) jenis_pegawai ON jenis_pegawai.kelompokpegawai_id = pegawai.kelompokpegawai_id) user_update ON user_update.loginpemakai_id = resepturdetail_t.last_modified_by
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.nama_pemakai,
            pegawai.pegawai_id,
            pegawai.nama_pegawai,
            jenis_pegawai.kelompokpegawai_id
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m_1.pegawai_id,
                    pegawai_m_1.nama_pegawai,
                    pegawai_m_1.kelompokpegawai_id
                   FROM pegawai_m pegawai_m_1) pegawai ON pegawai.pegawai_id = a.pegawai_id
             JOIN ( SELECT kelompokpegawai_m.kelompokpegawai_id,
                    kelompokpegawai_m.kelompokpegawai_nama
                   FROM kelompokpegawai_m) jenis_pegawai ON jenis_pegawai.kelompokpegawai_id = pegawai.kelompokpegawai_id) user_create ON user_create.loginpemakai_id = resepturdetail_t.created_by
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true
UNION ALL
 SELECT 'resep1'::text AS jenis,
    obatalkespasien_t.resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    obatalkespasien_t.is_kronis,
        CASE
            WHEN obatalkespasien_t.signa_oa IS NULL THEN obatalkespasien_t.signa ->> 'id'::text
            ELSE
            CASE
                WHEN obatalkespasien_t.signa_oa::text = '0'::text THEN NULL::text
                ELSE obatalkespasien_t.signa_oa::text
            END
        END AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
        CASE
            WHEN obatalkespasien_t.signa IS NOT NULL THEN obatalkespasien_t.signa ->> 'text'::text
            ELSE signaobat_m.signa_nama::text
        END AS signa_nama,
    obatalkespasien_t.signa,
    signaobat_m.qty_obat AS qty_obat_signa,
    signaobat_m.iterasi AS iterasi_signa,
    penjualanresep_t.ruangan_id AS ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    obatalkes_m.harganetto,
    NULL::character varying AS interaksi,
    NULL::character varying AS duplikasi,
    NULL::character varying AS dosis,
    NULL::character varying AS alergi,
    NULL::character varying AS kontradiksi,
    NULL::character varying AS review_note,
    NULL::timestamp without time zone AS wkt_review,
    pegawai_m.nama_pegawai,
    obatalkes_m.harganetto AS harga_netto,
    obatalkespasien_t.hargasatuan_oa AS harga_jual,
    round(obatalkespasien_t.hargasatuan_oa - obatalkes_m.harganetto) AS margin,
    obatalkespasien_t.hargasatuan_oa AS hn_margin,
    obatalkespasien_t.hargasatuan_oa * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkespasien_t.hargasatuan_oa - obatalkespasien_t.hargasatuan_oa * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    obatalkespasien_t.hargasatuan_oa * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkespasien_t.hargasatuan_oa + obatalkespasien_t.hargasatuan_oa * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    lkp_status_periksa.lookup_name AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    lkp_status_reseptur.lookup_name AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (obatalkespasien_t.additional_data::json ->> 'satuaninput_id'::text)::character varying AS satuaninput_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    (obatalkespasien_t.additional_data::json ->> 'satuankonversi_id'::text)::character varying AS satuankonversi_id,
    (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text)::character varying AS satuan_konversi,
    (obatalkespasien_t.additional_data::json ->> 'harga_konversi'::text)::character varying AS harga_konversi,
    (obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
    obatalkespasien_t.qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id,
    obatalkespasien_t.det,
    obatalkespasien_t.det_konversi,
    0::double precision AS qty_tersedia,
        CASE
            WHEN obatalkespasien_t.qty_medis IS NOT NULL THEN obatalkespasien_t.qty_medis
            ELSE obatalkespasien_t.qty_oa
        END AS qty_transaksi,
    obatalkespasien_t.nama_racikan,
    obatalkespasien_t.qty_racikan,
    obatalkespasien_t.satuan_racikan_id,
        CASE
            WHEN obatalkespasien_t.det_medis IS NOT NULL THEN obatalkespasien_t.det_medis
            ELSE obatalkespasien_t.det
        END AS det_transaksi,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama,
    NULL::timestamp without time zone AS tgl_update_resep,
    NULL::integer AS user_update_resep,
    NULL::integer AS kelompokpegawai_user_resep
   FROM obatalkespasien_t
     JOIN ( SELECT b.penjualanresep_id,
            b.pendaftaran_id,
            b.pasien_id,
            b.pegawai_id,
            b.ruangan_id,
            b.reseptur_id,
            b.noresep,
            b.tglresep,
            b.status_reseptur,
            b.biayaadministrasi,
            b.totalhargajual,
            b.nama_pembeli
           FROM penjualanresep_t b) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            b.no_pendaftaran,
            b.status_periksa
           FROM pendaftaran_t b) pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT b.pasien_id,
            b.no_rekam_medik,
            b.nama_pasien
           FROM pasien_m b) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.satuanunit_id,
            b.satuanunit_nama
           FROM satuanunit_m b) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON obatalkespasien_t.satuan_racikan_id = satuan_racikan.satuanunit_id
     LEFT JOIN ( SELECT b.racikan_id,
            b.racikan_nama
           FROM racikan_m b) racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama,
            b.instalasi_id
           FROM ruangan_m b) ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ( SELECT b.is_deleted,
            b.persen_diskon,
            b.persenppn
           FROM konfigfarmasi_k b) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT b.signa_id,
            b.signa_nama,
            b.qty_obat,
            b.iterasi
           FROM signaobat_m b) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_status_periksa ON pendaftaran_t.status_periksa::integer = lkp_status_periksa.lookup_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_status_reseptur ON penjualanresep_t.status_reseptur::integer = lkp_status_reseptur.lookup_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true;