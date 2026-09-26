<?php

use yii\db\Migration;

/**
 * Class m200511_014705_migrate_20200508
 */
class m200511_014705_migrate_20200508 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pemusnahanobatdetail_t" ADD COLUMN "satuan_id" int4;');
        
        $this->execute('DROP VIEW if exists "public"."infopasienmcudetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienmcudetail_v\" AS  SELECT 'RAD'::text AS penunjang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    radiologi.tindakanpelayanan_id,
        CASE
            WHEN (radiologi.tipepaket_id IS NULL) THEN radiologi.daftartindakan_id
            ELSE radiologi.tipepaket_id
        END AS tindakan_paket_id,
    radiologi.tipepaket_nama,
    radiologi.detail_2id,
    radiologi.detail_2,
    radiologi.detail_3id,
        CASE
            WHEN (radiologi.tipepaket_id IS NULL) THEN radiologi.daftartindakan_nama
            ELSE radiologi.detail_3
        END AS detail_3,
    radiologi.p_rad AS pemeriksaan,
    radiologi.j_rad AS jenis
   FROM (((((pendaftaran_t
     JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
     JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 5))))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT rad.tindakanpelayanan_id,
            rad.pendaftaran_id,
            rad.tipepaket_id,
            rad.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_rad,
            detail.j_rad
           FROM (((tindakanpelayanan_t rad
             LEFT JOIN tipepaket_m ON ((rad.tipepaket_id = tipepaket_m.tipepaket_id)))
             LEFT JOIN daftartindakan_m ON ((rad.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_rad,
                    paket_detail.j_rad
                   FROM (((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                           FROM ((((tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                             JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
                             LEFT JOIN pemeriksaanrad_m ON ((daftartindakan_m_1.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM (((((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
                     JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                     LEFT JOIN pemeriksaanrad_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((rad.tipepaket_id = detail.detail_1id)))) radiologi ON ((pendaftaran_t.pendaftaran_id = radiologi.pendaftaran_id)))
  WHERE (pendaftaran_t.instalasi_id = 21)
UNION ALL
 SELECT 'LAB'::text AS penunjang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    laboratorium.tindakanpelayanan_id,
        CASE
            WHEN (laboratorium.tipepaket_id IS NULL) THEN laboratorium.daftartindakan_id
            ELSE laboratorium.tipepaket_id
        END AS tindakan_paket_id,
    laboratorium.tipepaket_nama,
    laboratorium.detail_2id,
    laboratorium.detail_2,
    laboratorium.detail_3id,
        CASE
            WHEN (laboratorium.tipepaket_id IS NULL) THEN laboratorium.daftartindakan_nama
            ELSE laboratorium.detail_3
        END AS detail_3,
    laboratorium.p_lab AS pemeriksaan,
    laboratorium.j_lab AS jenis
   FROM (((((pendaftaran_t
     JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
     JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 4))))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ( SELECT lab.tindakanpelayanan_id,
            lab.pendaftaran_id,
            lab.tipepaket_id,
            lab.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_lab,
            detail.j_lab
           FROM (((tindakanpelayanan_t lab
             JOIN tipepaket_m ON ((lab.tipepaket_id = tipepaket_m.tipepaket_id)))
             LEFT JOIN daftartindakan_m ON ((lab.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_lab,
                    paket_detail.j_lab
                   FROM (((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                           FROM ((((tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                             JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
                             LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m_1.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                             LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                   FROM (((((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
                     JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                     LEFT JOIN pemeriksaanlab_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                     LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((lab.tipepaket_id = detail.detail_1id)))) laboratorium ON ((pendaftaran_t.pendaftaran_id = laboratorium.pendaftaran_id)))
  WHERE (pendaftaran_t.instalasi_id = 21);");

        $this->execute('DROP VIEW if exists "public"."inforesepdetail_v";');

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
    resepturdetail_t.signa_id,
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
    signaobat_m.signa_nama,
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
    NULL::character varying AS nama_pembeli
   FROM ((((((((((((resepturdetail_t
     JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
     JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN rotd_t ON ((resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id)))
     LEFT JOIN pegawai_m ON ((rotd_t.pegawairotd_id = rotd_t.pegawairotd_id)))
     LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
  WHERE ((resepturdetail_t.is_deleted = false) AND (resepturdetail_t.is_active = true))
UNION ALL
 SELECT 'resep'::text AS jenis,
    NULL::integer AS resepturdetail_id,
    obatalkespasien_t.obatalkespasien_id,
    penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
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
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::integer AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    signaobat_m.signa_nama,
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
    penjualanresep_t.nama_pembeli
   FROM ((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON (((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id) AND (penjualanresep_t.reseptur_id IS NULL))))
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

        $this->execute('
            CREATE TABLE "public"."kesimpulanmcu_t" (
  "kesimpulanmcu_id" serial8,
  "pendaftaran_id" int4 NOT NULL,
  "tindakanpelayanan_id" int4 NOT NULL,
  "kesimpulan" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "kesimpulanmcu_t_pkey" PRIMARY KEY ("kesimpulanmcu_id")
)
;');

        $this->execute('
            CREATE TABLE "public"."pemeriksaanfisikmcu_t" (
  "pemeriksaanfisikmcu_id" serial4,
  "pendaftaran_id" int4 NOT NULL,
  "berat_badan" float4,
  "tinggi_badan" float4,
  "td_sistolik" int2,
  "td_diastolik" int2,
  "pernafasan" varchar(50) COLLATE "pg_catalog"."default",
  "detak_nadi" int2,
  "suhu" varchar(50) COLLATE "pg_catalog"."default",
  "pemeriksaan_fisik" text COLLATE "pg_catalog"."default",
  "pemeriksaan_lainnya" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "pemeriksaanfisikmcu_t_pkey" PRIMARY KEY ("pemeriksaanfisikmcu_id")
)
;');
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200511_014705_migrate_20200508 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200511_014705_migrate_20200508 cannot be reverted.\n";

        return false;
    }
    */
}
