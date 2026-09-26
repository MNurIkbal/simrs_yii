<?php

use yii\db\Migration;

/**
 * Class m200724_064708_migrate_mhkn_20200724
 */
class m200724_064708_migrate_mhkn_20200724 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infotindakanpenatajasa_v";');
         
         $this->execute("CREATE VIEW \"public\".\"infotindakanpenatajasa_v\" AS  SELECT 'tindakan'::text AS jenis,
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
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
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
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM ((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted = false)
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
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM (((((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN obatalkespasien_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id) AND (obatalkespasien_t.is_deleted = false))))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
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
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE COALESCE(obatsudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    NULL::integer AS kelaspelayanan_id,
    NULL::character varying AS kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_masukpenunjang
   FROM (((((((((obatalkespasien_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
            count(*) AS telahbayar
           FROM obatsudahbayar_t
          GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON ((obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN tindakanpelayanan_t ON (((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.is_deleted = false))))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (obatalkespasien_t.is_deleted IS FALSE);");

         $this->execute('DROP VIEW if exists "public"."worklistresepdetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE((obatalkespasien_t.signa ->> 'text'::text), (signaobat_m.signa_nama)::text) AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM ((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
    NULL::text AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE((resepturdetail_t.signa ->> 'text'::text), (signaobat_m.signa_nama)::text) AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    resepturdetail_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN (resepturdetail_t.det IS NULL) THEN resepturdetail_t.qty_reseptur
            ELSE resepturdetail_t.det
        END AS det,
    resepturdetail_t.is_deleted AS detail_is_deleted,
    resepturdetail_t.racikan_id
   FROM (((((reseptur_t
     JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    COALESCE((obatalkespasien_t.signa ->> 'text'::text), (signaobat_m.signa_nama)::text) AS signa,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::double precision AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    stokobatalkes_t.tglkadaluarsa,
        CASE
            WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM (((((penjualanresep_t
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     LEFT JOIN stokobatalkes_t ON (((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id) AND (obatalkespasien_t.obatalkes_id = stokobatalkes_t.obatalkes_id))))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

         $this->execute('DROP VIEW "public"."inforesepdetail_v";');
         
         $this->execute("
            CREATE VIEW \"public\".\"inforesepdetail_v\" AS  SELECT 'reseptur'::text AS jenis,
    resepturdetail_t.resepturdetail_id,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS penjualanresep_id,
    resepturdetail_t.reseptur_id,
    reseptur_t.pendaftaran_id,
    reseptur_t.pasien_id,
    resepturdetail_t.obatalkes_id,
    resepturdetail_t.satuankecil_id,
    resepturdetail_t.racikan_id,
    (COALESCE((resepturdetail_t.signa ->> 'id'::text), (resepturdetail_t.signa_id)::text))::integer AS signa_id,
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
    COALESCE((resepturdetail_t.signa ->> 'text'::text), (signaobat_m.signa_nama)::text) AS signa_nama,
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
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
    resepturdetail_t.is_deleted,
    resepturdetail_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    resepturdetail_t.qty_konversi,
    resepturdetail_t.additional_data AS additional_reseptur,
    (((resepturdetail_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((resepturdetail_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((resepturdetail_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    (0)::double precision AS biayaadministrasiresep,
    (0)::double precision AS totalhargajualresep,
    (0)::double precision AS totaltagihanresep,
    NULL::character varying AS nama_pembeli,
    resepturdetail_t.qty_reseptur AS qty_oa,
    reseptur_t.ruanganreseptur_id AS ruanganasal_id,
    ruangan_asal.instalasi_id AS instalasiasal_id,
    resepturdetail_t.det,
    resepturdetail_t.det_konversi
   FROM (((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
UNION ALL
 SELECT 'resep'::text AS jenis,
    obatalkespasien_t.resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkespasien_t.satuankecil_id,
    obatalkespasien_t.racikan_id,
    NULL::integer AS signa_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    penjualanresep_t.noresep,
    penjualanresep_t.tglresep AS tglreseptur,
    racikan_m.racikan_nama,
    obatalkespasien_t.r,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::double precision AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    COALESCE((obatalkespasien_t.signa ->> 'text'::text), (signaobat_m.signa_nama)::text) AS signa_nama,
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
    fgethargajualobat(obatalkes_m.obatalkes_id) AS harga_jual,
    ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision) AS margin,
    (obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) AS hn_margin,
    (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS disc,
    ((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS hn_diskon,
    ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS ppn,
    (obatalkes_m.harganetto + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    obatalkespasien_t.is_deleted,
    obatalkespasien_t.is_active,
    obatalkespasien_t.additional_data,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_konversi,
    obatalkespasien_t.additional_data AS additional_reseptur,
    (((obatalkespasien_t.additional_data)::json ->> 'satuaninput_id'::text))::character varying AS satuaninput_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text))::character varying AS satuan_input,
    (((obatalkespasien_t.additional_data)::json ->> 'satuankonversi_id'::text))::character varying AS satuankonversi_id,
    (((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text))::character varying AS satuan_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'harga_konversi'::text))::character varying AS harga_konversi,
    (((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::character varying AS nilai_konversi,
    penjualanresep_t.biayaadministrasi AS biayaadministrasiresep,
    penjualanresep_t.totalhargajual AS totalhargajualresep,
    (COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totaltagihanresep,
    penjualanresep_t.nama_pembeli,
        CASE
            WHEN (obatalkespasien_t.det = (0)::double precision) THEN obatalkespasien_t.det
            WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id,
    obatalkespasien_t.det,
    obatalkespasien_t.det_konversi
   FROM ((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true));");
        
         $this->execute('CREATE TABLE "public"."purchasereq_t" (
  "purchasereq_id" serial8,
  "no_pr" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_pr" date,
  "ruangan_id" int4,
  "pegawai_id" int4,
  "status" int2,
  "reference" text COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "purchasereq_t_pkey" PRIMARY KEY ("purchasereq_id")
)
;');
    $this->execute("COMMENT ON COLUMN public.purchasereq_t.status IS 'lookup_type=''status_purchaserequest''';");
         
         $this->execute('CREATE TABLE "public"."purchasereqdetail_t" (
  "purchasereqdetail_id" serial8,
  "purchasereq_id" int4,
  "obatalkes_id" int4,
  "qty_input" numeric(15,2),
  "qty_konversi" numeric(15,2),
  "satuan_id" int4,
  "satuankonversi_id" int4,
  "catatan" text COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "purchasereqdetail_t_pkey" PRIMARY KEY ("purchasereqdetail_id")
)
;');
         $this->execute("
            CREATE VIEW \"public\".\"infopurchasereq_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    fgetnamalookup((purchasereq_t.status)::integer) AS status_pr
   FROM ((purchasereq_t
     JOIN ruangan_m ON ((purchasereq_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pegawai_m ON ((purchasereq_t.pegawai_id = pegawai_m.pegawai_id)))
  WHERE (purchasereq_t.is_deleted = false);");

         $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqdetail_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereqdetail_t.purchasereqdetail_id,
    purchasereq_t.no_pr,
    purchasereqdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    purchasereqdetail_t.qty_input,
    purchasereqdetail_t.qty_konversi,
    purchasereqdetail_t.satuan_id,
    satuan_1.satuanunit_nama AS satuan,
    purchasereqdetail_t.satuankonversi_id,
    satuan_2.satuanunit_nama AS satuan_konversi,
    purchasereqdetail_t.catatan
   FROM ((((purchasereq_t
     JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
     JOIN obatalkes_m ON ((purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_1 ON ((purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_2 ON ((purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id)))
  WHERE ((purchasereq_t.is_deleted = false) AND (purchasereqdetail_t.is_deleted = false));");
         
         $this->execute("CREATE VIEW \"public\".\"invoiceri_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien AS alamat,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    penjamin_m.penjamin_nama AS penjamin,
    pasienadmisi_t.tgl_admisi,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    ruangan_m.ruangan_nama AS ruangan,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur AS bed,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    pembayaranmetode_t.metode_bayar,
    jenisnontunai_m.nama AS jenis_nontunai,
    bank_m.nama_bank AS bank,
    pendaftaran_t.tgl_stopakomodasi,
    pembayaran_t.total_dijamin
   FROM ((((((((((((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pembayaranmetode_t ON ((pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id)))
     LEFT JOIN jenisnontunai_m ON ((pembayaranmetode_t.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id)))
     LEFT JOIN bank_m ON ((jenisnontunai_m.bank_id = bank_m.bank_id)));");

         $this->execute("
            CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan,
    layanan.tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi
   FROM ((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
            kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi
           FROM ((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             LEFT JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompok,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.harganetto_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi
           FROM (((((obatalkespasien_t
             JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
             LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
             LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN pegawai_m dok_dpjp ON ((obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id)))
          WHERE (obatalkespasien_t.is_deleted = false)) layanan ON ((pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id)));");
         

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200724_064708_migrate_mhkn_20200724 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200724_064708_migrate_mhkn_20200724 cannot be reverted.\n";

        return false;
    }
    */
}
