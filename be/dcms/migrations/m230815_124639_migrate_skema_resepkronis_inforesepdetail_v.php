<?php

use yii\db\Migration;

/**
 * Class m230815_124639_migrate_skema_resepkronis_inforesepdetail_v
 */
class m230815_124639_migrate_skema_resepkronis_inforesepdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE udd_t ADD IF NOT EXISTS status_udd int4 DEFAULT 1029;');
		
        $this->execute('DROP VIEW IF EXISTS "public"."inforesepdetail_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.inforesepdetail_v
        AS  SELECT 'reseptur'::text AS jenis,
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
    COALESCE(stok.jml_stok, 0::double precision) - ((COALESCE(round(mutasi.jml::numeric, 3), 0::numeric) + COALESCE(round(resepfarmasi.jml::numeric, 3), 0::numeric) + COALESCE(round(resepdokter.jml::numeric, 3), 0::numeric) + COALESCE(cssd_detail.qty, 0::bigint)::numeric)::double precision + COALESCE(udd_detail_t.qty, 0::double precision)) AS qty_tersedia,
        CASE
            WHEN resepturdetail_t.qty_medis IS NOT NULL THEN resepturdetail_t.qty_medis
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_transaksi,
    resepturdetail_t.nama_racikan,
    resepturdetail_t.qty_racikan,
    resepturdetail_t.satuan_racikan_id,
    resepturdetail_t.det_medis AS det_transaksi,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama
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
            a.qty_obat
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
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.qty_tersedia
           FROM stokobatalkes_r a) sr ON sr.obatalkes_id = resepturdetail_t.obatalkes_id AND sr.ruangan_id = reseptur_t.ruangan_id
     LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
            mt.obatalkes_id,
            sum(mt.jumlah_mutasi) AS jml,
            string_agg(mt2.nomutasioa::text, ','::text) AS reference
           FROM mutasiobatdetail_t mt
             LEFT JOIN ( SELECT a.ruanganasal_id,
                    a.nomutasioa,
                    a.mutasiobatruangan_id,
                    a.is_deleted,
                    a.status_mutasi
                   FROM mutasiobatruangan_t a) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
          WHERE mt.is_deleted IS FALSE AND mt2.is_deleted IS FALSE AND mt2.status_mutasi = 401
          GROUP BY mt2.ruanganasal_id, mt.obatalkes_id) mutasi ON mutasi.ruangan_id = sr.ruangan_id AND mutasi.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.jml) AS jml,
            string_agg(a.reference::text, ','::text) AS reference
           FROM ( SELECT pt.ruangan_id,
                    ot.obatalkes_id,
                    sum(COALESCE(ot.det_konversi, ot.qty_konversi)) - sum(COALESCE(stokobatalkes_t.qtystok_out, 0::double precision)) AS jml,
                    pt.noresep AS reference
                   FROM obatalkespasien_t ot
                     LEFT JOIN ( SELECT a_1.obatalkes_id
                           FROM obatalkes_m a_1) om ON om.obatalkes_id = ot.obatalkes_id
                     LEFT JOIN ( SELECT a_1.ruangan_id,
                            a_1.noresep,
                            a_1.penjualanresep_id,
                            a_1.is_deleted,
                            a_1.status_reseptur,
                            a_1.reseptur_id
                           FROM penjualanresep_t a_1) pt ON pt.penjualanresep_id = ot.penjualanresep_id
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.ruangan_id,
                            a_1.obatalkespasien_id,
                            a_1.qtystok_out
                           FROM stokobatalkes_t a_1) stokobatalkes_t ON ot.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
                  WHERE ot.is_deleted IS FALSE AND pt.is_deleted IS FALSE AND pt.status_reseptur <> 660 AND pt.status_reseptur <> 432
                  GROUP BY pt.ruangan_id, ot.obatalkes_id, pt.noresep) a
          WHERE a.jml <> 0::double precision
          GROUP BY a.ruangan_id, a.obatalkes_id) resepfarmasi ON resepfarmasi.ruangan_id = sr.ruangan_id AND resepfarmasi.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.jml) AS jml,
            string_agg(a.reference, ','::text) AS reference
           FROM ( SELECT rt.ruangan_id,
                    dt.obatalkes_id,
                    sum(COALESCE(dt.det_konversi, dt.qty_konversi)) - sum(COALESCE(stokobatalkes_t.qtystok_out, 0::double precision)) AS jml,
                        CASE
                            WHEN penjualanresep_t.noresep IS NULL THEN rt.noresep::text
                            ELSE NULL::text
                        END AS reference
                   FROM reseptur_t rt
                     JOIN ( SELECT a_1.resepturdetail_id,
                            a_1.obatalkes_id,
                            a_1.det_konversi,
                            a_1.qty_konversi,
                            a_1.reseptur_id,
                            a_1.is_deleted
                           FROM resepturdetail_t a_1) dt ON dt.reseptur_id = rt.reseptur_id
                     LEFT JOIN ( SELECT a_1.penjualanresep_id,
                            a_1.noresep,
                            a_1.reseptur_id
                           FROM penjualanresep_t a_1) penjualanresep_t ON rt.reseptur_id = penjualanresep_t.reseptur_id
                     LEFT JOIN ( SELECT a_1.resepturdetail_id,
                            a_1.obatalkespasien_id,
                            a_1.obatalkes_id,
                            a_1.ruangan_id
                           FROM obatalkespasien_t a_1) obatalkespasien_t_1 ON dt.resepturdetail_id = obatalkespasien_t_1.resepturdetail_id AND obatalkespasien_t_1.ruangan_id = rt.ruangan_id AND dt.obatalkes_id = obatalkespasien_t_1.obatalkes_id
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.ruangan_id,
                            a_1.obatalkespasien_id,
                            a_1.qtystok_out
                           FROM stokobatalkes_t a_1) stokobatalkes_t ON obatalkespasien_t_1.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
                  WHERE rt.status_reseptur <> 660 AND rt.status_reseptur <> 432 AND rt.is_deleted IS FALSE AND dt.is_deleted IS FALSE AND penjualanresep_t.noresep IS NULL
                  GROUP BY rt.ruangan_id, dt.obatalkes_id, rt.noresep, stokobatalkes_t.qtystok_out, penjualanresep_t.noresep) a
          WHERE a.jml <> 0::double precision
          GROUP BY a.ruangan_id, a.obatalkes_id) resepdokter ON resepdokter.ruangan_id = sr.ruangan_id AND resepdokter.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.qtystok_in) - sum(a.qtystok_out) AS jml_stok
           FROM stokobatalkes_t a
          WHERE a.is_deleted = false
          GROUP BY a.ruangan_id, a.obatalkes_id) stok ON sr.ruangan_id = stok.ruangan_id AND sr.obatalkes_id = stok.obatalkes_id
     LEFT JOIN ( SELECT a.ruanganasal_id,
            b.barangalkes_id,
            sum(b.qty) AS qty,
            string_agg(a.no_pengajuan_sterilisasi::text, ','::text) AS reference
           FROM cssd_t a
             JOIN ( SELECT b_1.cssd_id,
                    b_1.barangalkes_id,
                    b_1.qty,
                    b_1.is_alkes
                   FROM cssddet_t b_1
                  WHERE b_1.is_deleted = false) b ON a.cssd_id = b.cssd_id
          WHERE b.is_alkes = true AND a.status_cssd = 1190 AND a.is_deleted = false
          GROUP BY a.ruanganasal_id, b.barangalkes_id) cssd_detail ON cssd_detail.ruanganasal_id = sr.ruangan_id AND cssd_detail.barangalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruanganproses_id,
            b.obatalkes_id,
            round(sum(b.qty)::double precision) AS qty,
            string_agg(a.no_udd::text, ','::text) AS reference
           FROM udd_t a
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.qty,
                    a_1.udd_id
                   FROM udd_detail_t a_1) b ON a.udd_id = b.udd_id
          WHERE a.status_udd = 1030 OR a.status_udd = 1031
          GROUP BY a.ruanganproses_id, b.obatalkes_id) udd_detail_t ON udd_detail_t.ruanganproses_id = sr.ruangan_id AND udd_detail_t.obatalkes_id = sr.obatalkes_id
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true
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
    obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS margin,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision AS hn_margin,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS disc,
    obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision AS hn_diskon,
    (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS ppn,
    obatalkes_m.harganetto + (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision - (obatalkes_m.harganetto + obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto) / 100::double precision) * konfigfarmasi_k.persen_diskon / 100::double precision) * konfigfarmasi_k.persenppn / 100::double precision AS hn_ppn,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(penjualanresep_t.status_reseptur::integer) AS status_reseptur,
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
    COALESCE(stok.jml_stok, 0::double precision) - ((COALESCE(round(mutasi.jml::numeric, 3), 0::numeric) + COALESCE(round(resepfarmasi.jml::numeric, 3), 0::numeric) + COALESCE(round(resepdokter.jml::numeric, 3), 0::numeric) + COALESCE(cssd_detail.qty, 0::bigint)::numeric)::double precision + COALESCE(udd_detail_t.qty, 0::double precision)) AS qty_tersedia,
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
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama
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
            b.qty_obat
           FROM signaobat_m b) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.ruangan_id,
            b.qty_tersedia
           FROM stokobatalkes_r b) sr ON sr.obatalkes_id = obatalkespasien_t.obatalkes_id AND sr.ruangan_id = penjualanresep_t.ruangan_id
     LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
            mt.obatalkes_id,
            sum(mt.jumlah_mutasi) AS jml,
            string_agg(mt2.nomutasioa::text, ','::text) AS reference
           FROM mutasiobatdetail_t mt
             LEFT JOIN ( SELECT a.ruanganasal_id,
                    a.nomutasioa,
                    a.mutasiobatruangan_id,
                    a.is_deleted,
                    a.status_mutasi
                   FROM mutasiobatruangan_t a) mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
          WHERE mt.is_deleted IS FALSE AND mt2.is_deleted IS FALSE AND mt2.status_mutasi = 401
          GROUP BY mt2.ruanganasal_id, mt.obatalkes_id) mutasi ON mutasi.ruangan_id = sr.ruangan_id AND mutasi.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.jml) AS jml,
            string_agg(a.reference::text, ','::text) AS reference
           FROM ( SELECT pt.ruangan_id,
                    ot.obatalkes_id,
                    sum(COALESCE(ot.det_konversi, ot.qty_konversi)) - sum(COALESCE(stokobatalkes_t.qtystok_out, 0::double precision)) AS jml,
                    pt.noresep AS reference
                   FROM obatalkespasien_t ot
                     LEFT JOIN ( SELECT a_1.obatalkes_id
                           FROM obatalkes_m a_1) om ON om.obatalkes_id = ot.obatalkes_id
                     LEFT JOIN ( SELECT a_1.ruangan_id,
                            a_1.noresep,
                            a_1.penjualanresep_id,
                            a_1.is_deleted,
                            a_1.status_reseptur,
                            a_1.reseptur_id
                           FROM penjualanresep_t a_1) pt ON pt.penjualanresep_id = ot.penjualanresep_id
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.ruangan_id,
                            a_1.obatalkespasien_id,
                            a_1.qtystok_out
                           FROM stokobatalkes_t a_1) stokobatalkes_t ON ot.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
                  WHERE ot.is_deleted IS FALSE AND pt.is_deleted IS FALSE AND pt.status_reseptur <> 660 AND pt.status_reseptur <> 432
                  GROUP BY pt.ruangan_id, ot.obatalkes_id, pt.noresep) a
          WHERE a.jml <> 0::double precision
          GROUP BY a.ruangan_id, a.obatalkes_id) resepfarmasi ON resepfarmasi.ruangan_id = sr.ruangan_id AND resepfarmasi.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.jml) AS jml,
            string_agg(a.reference, ','::text) AS reference
           FROM ( SELECT rt.ruangan_id,
                    dt.obatalkes_id,
                    sum(COALESCE(dt.det_konversi, dt.qty_konversi)) - sum(COALESCE(stokobatalkes_t.qtystok_out, 0::double precision)) AS jml,
                        CASE
                            WHEN penjualanresep_t_1.noresep IS NULL THEN rt.noresep::text
                            ELSE NULL::text
                        END AS reference
                   FROM reseptur_t rt
                     JOIN ( SELECT a_1.resepturdetail_id,
                            a_1.obatalkes_id,
                            a_1.det_konversi,
                            a_1.qty_konversi,
                            a_1.reseptur_id,
                            a_1.is_deleted
                           FROM resepturdetail_t a_1) dt ON dt.reseptur_id = rt.reseptur_id
                     LEFT JOIN ( SELECT a_1.penjualanresep_id,
                            a_1.noresep,
                            a_1.reseptur_id
                           FROM penjualanresep_t a_1) penjualanresep_t_1 ON rt.reseptur_id = penjualanresep_t_1.reseptur_id
                     LEFT JOIN ( SELECT a_1.resepturdetail_id,
                            a_1.obatalkespasien_id,
                            a_1.obatalkes_id,
                            a_1.ruangan_id
                           FROM obatalkespasien_t a_1) obatalkespasien_t_1 ON dt.resepturdetail_id = obatalkespasien_t_1.resepturdetail_id AND obatalkespasien_t_1.ruangan_id = rt.ruangan_id AND dt.obatalkes_id = obatalkespasien_t_1.obatalkes_id
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.ruangan_id,
                            a_1.obatalkespasien_id,
                            a_1.qtystok_out
                           FROM stokobatalkes_t a_1) stokobatalkes_t ON obatalkespasien_t_1.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
                  WHERE rt.status_reseptur <> 660 AND rt.status_reseptur <> 432 AND rt.is_deleted IS FALSE AND dt.is_deleted IS FALSE AND penjualanresep_t_1.noresep IS NULL
                  GROUP BY rt.ruangan_id, dt.obatalkes_id, rt.noresep, stokobatalkes_t.qtystok_out, penjualanresep_t_1.noresep) a
          WHERE a.jml <> 0::double precision
          GROUP BY a.ruangan_id, a.obatalkes_id) resepdokter ON resepdokter.ruangan_id = sr.ruangan_id AND resepdokter.obatalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            sum(a.qtystok_in) - sum(a.qtystok_out) AS jml_stok
           FROM stokobatalkes_t a
          WHERE a.is_deleted = false
          GROUP BY a.ruangan_id, a.obatalkes_id) stok ON sr.ruangan_id = stok.ruangan_id AND sr.obatalkes_id = stok.obatalkes_id
     LEFT JOIN ( SELECT a.ruanganasal_id,
            b.barangalkes_id,
            sum(b.qty) AS qty,
            string_agg(a.no_pengajuan_sterilisasi::text, ','::text) AS reference
           FROM cssd_t a
             JOIN ( SELECT b_1.cssd_id,
                    b_1.barangalkes_id,
                    b_1.qty,
                    b_1.is_alkes
                   FROM cssddet_t b_1
                  WHERE b_1.is_deleted = false) b ON a.cssd_id = b.cssd_id
          WHERE b.is_alkes = true AND a.status_cssd = 1190 AND a.is_deleted = false
          GROUP BY a.ruanganasal_id, b.barangalkes_id) cssd_detail ON cssd_detail.ruanganasal_id = sr.ruangan_id AND cssd_detail.barangalkes_id = sr.obatalkes_id
     LEFT JOIN ( SELECT a.ruanganproses_id,
            b.obatalkes_id,
            round(sum(b.qty)::double precision) AS qty,
            string_agg(a.no_udd::text, ','::text) AS reference
           FROM udd_t a
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.qty,
                    a_1.udd_id
                   FROM udd_detail_t a_1) b ON a.udd_id = b.udd_id
          WHERE a.status_udd = 1030 OR a.status_udd = 1031
          GROUP BY a.ruanganproses_id, b.obatalkes_id) udd_detail_t ON udd_detail_t.ruanganproses_id = sr.ruangan_id AND udd_detail_t.obatalkes_id = sr.obatalkes_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_124639_migrate_skema_resepkronis_inforesepdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_124639_migrate_skema_resepkronis_inforesepdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
