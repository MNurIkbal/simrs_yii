<?php

use yii\db\Migration;

/**
 * Class m210524_093916_oddo_20210524_penyesuaianview
 */
class m210524_093916_oddo_20210524_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."penerimaanobat_r" ADD COLUMN if not exists "is_consigment" bool;');
       $this->execute('ALTER TABLE "public"."penerimaanobat_t" ADD COLUMN if not exists "is_consigment" bool;');

       $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

       $this->execute("
        CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi <> 0::double precision OR obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi <> 0::double precision OR obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision) IS NOT NULL AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    obatalkespasien_r.hargajual_oa AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dibayarkan <> 0::double precision AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', pendaftaran_r.pegadmisi_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN obatalkespasien_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN 'PHARMACY OUTPATIENT'::text
            ELSE 'PHARMACY INPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN obatalkespasien_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE COALESCE(penjualanresep_t.no_resep, obatalkespasien_r.no_obatalkespasien)
        END AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.nama_pasien,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_r_1.instalasi_id
                    ELSE 3
                END AS instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_r_1.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pendaftaran_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pasienmasukpenunjang_t ON obatalkespasien_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE obatalkespasien_r_1.is_deleted = false
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t penjualanresep_t_1
             LEFT JOIN reseptur_t ON penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id
UNION ALL
 SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi <> 0::double precision OR obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi <> 0::double precision OR obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision) IS NOT NULL AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> 'nilai_konversi'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
    obatalkespasien_r.hargajual_oa::integer AS price_subtotal,
    obatalkespasien_r.hargajual_oa::integer AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dibayarkan <> 0::double precision AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    concat('RSPB', obatalkespasien_r.penjualanresep_id) AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS primary_doc_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
    'OPD'::text AS patient_group,
    NULL::text AS special_group,
    'PHARMACY OUTPATIENT'::text AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    COALESCE(penjualanresep_t.no_resep) AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
    concat('PEG', penjualanresep_t.pegawai_id) AS account_analytic_id,
    concat('PEG', penjualanresep_t.pegawai_id) AS backup_analytic_id,
    penjualanresep_r.kota,
    penjualanresep_r.kecamatan,
    penjualanresep_r.kelurahan,
    penjualanresep_r.pasien_id AS partner_id,
    penjualanresep_r.no_rekam_medik AS registration_code,
    penjualanresep_r.noresep AS number_admission,
    '-'::text AS manufacture,
    penjualanresep_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep_r.nama_pasien,
    penjualanresep_r.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT penjualanresep_r_1.id,
            penjualanresep_r_1.penjualanresep_id,
            penjualanresep_r_1.pegawai_id,
            penjualanresep_r_1.pasienadmisi_id,
            0 AS pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            penjualanresep_r_1.noresep,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            penjualanresep_r_1.tglresep AS tglpasienpulang,
            penjualanresep_r_1.tglresep
           FROM penjualanresep_r penjualanresep_r_1
             JOIN ( SELECT max(resep_max.id) AS id,
                    resep_max.penjualanresep_id
                   FROM penjualanresep_r resep_max
                  WHERE resep_max.keterangan::text = 'ACCRUAL'::text
                  GROUP BY resep_max.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
             JOIN pasien_m ON 0 = pasien_m.pasien_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
          WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON obatalkespasien_r.penjualanresep_id = penjualanresep_r.penjualanresep_id
     JOIN obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE obatalkespasien_r_1.is_deleted = false
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t penjualanresep_t_1
             LEFT JOIN reseptur_t ON penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id;");

       $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."saleorder_linedetail_v";');

       $this->execute("
        CREATE VIEW \"public\".\"saleorder_linedetail_v\" AS  SELECT 6 AS sync_type,
    tp_paket.tgl_proses::date AS tglproses,
    concat('TND', tp_paket.id, '-', concat('TND', paketpelayanan_mp.daftartindakan_id)) AS sync_id_api,
    concat('TND', paketpelayanan_mp.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tp_paket.qty_tindakan AS product_uom_qty,
    paket_detail.harga_satuan + paket_detail.harga_cyto + paket_detail.harga_penyulit AS price_unit,
    paket_detail.harga_total AS price_subtotal,
    paket_detail.harga_total AS price_total,
        CASE
            WHEN persen.tarif_dijamin = 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = 'BILLING'::text THEN paket_detail.harga_total
                WHEN tp_paket.keterangan::text = 'BILLING CANCEL'::text THEN '-1'::integer::double precision * paket_detail.harga_total
                ELSE 0::double precision
            END
            WHEN persen.tarif_dijamin <> 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = 'BILLING'::text THEN (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan
                WHEN tp_paket.keterangan::text = 'BILLING CANCEL'::text THEN '-1'::integer::double precision * ((paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan)
                ELSE NULL::double precision
            END
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN persen.tarif_dibayarkan = 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = 'BILLING'::text THEN paket_detail.harga_total
                WHEN tp_paket.keterangan::text = 'BILLING CANCEL'::text THEN '-1'::integer::double precision * paket_detail.harga_total
                ELSE 0::double precision
            END
            WHEN persen.tarif_dibayarkan <> 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = 'BILLING'::text THEN paket_detail.harga_total - (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan
                WHEN tp_paket.keterangan::text = 'BILLING CANCEL'::text THEN '-1'::integer::double precision * (paket_detail.harga_total - (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan)
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS payer_amount,
    tp_paket.pendaftaran_id AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', pendaftaran_r_1.pegawai_id) AS primary_doc_id,
    NULL::text AS prescribe_doc_id,
    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m_1.ruangan_id::text AS location_id,
    ruangan_m_1.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t_1.no_pembayaran, no_pembayaran_1.no_pembayaran) AS billno,
    pembayaranpelayanan_t_1.tgl_pembayaran AS bill_date,
    tp_paket.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r_1.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r_1.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tp_paket.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r_1.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r_1.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r_1.instalasi_id = 3 THEN 'IPD'::text
                WHEN tp_paket.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN tp_paket.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN tp_paket.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN tp_paket.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN tp_paket.instalasi_id = 21 THEN 'MCU'::text
            WHEN tp_paket.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN tp_paket.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m_1.kelaspelayanan_nama AS bed_type,
    concat('PEN', tp_paket.penjamin_id) AS payer,
    penjamin_m_1.penjamin_kode AS payer_code,
    carabayar_m_1.carabayar_nama AS payer_type,
    penjamin_m_1.penjamin_nama AS payer_name,
        CASE
            WHEN tp_paket.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tp_paket.no_tindakanpelayanan
        END AS order_no,
    tp_paket.tgl_tindakan::date AS order_date,
    true AS is_package,
    tipepaket_m_1.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r_1.pegawai_id)
            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat('PEG', pendaftaran_r_1.pegawai_id)
            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r_1.kota,
    pendaftaran_r_1.kecamatan,
    pendaftaran_r_1.kelurahan,
    pendaftaran_r_1.pasien_id AS partner_id,
    pendaftaran_r_1.no_rekam_medik AS registration_code,
    pendaftaran_r_1.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r_1.tglpasienpulang::date AS discharge_date,
    pegawai_1.spesialis_nama AS specialization_primary,
    tp_paket.is_sent,
    tp_paket.is_sending,
    tp_paket.id,
    'PAKET_DETAIL'::text AS jenis,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY['ACCRUAL REVERSAL'::character varying::text, 'ACCRUAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY['ACCRUAL REVERSAL'::character varying::text, 'ACCRUAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
        CASE
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = true THEN 'SUKSES'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r tp_paket
     JOIN ( SELECT pen_det.id,
            pen_det.pendaftaran_id,
            pen_det.pegawai_id,
            pen_det.pasien_id,
            pasien_m.no_rekam_medik,
            pen_det.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pen_det.pasienadmisi_id IS NULL THEN pen_det.instalasi_id
                    ELSE ruangan_m_2.instalasi_id
                END AS instalasi_id,
            pen_det.is_aps,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r pen_det
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pen_det.id = max.id
             JOIN pasien_m ON pen_det.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pen_det.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pen_det.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_2 ON pasienadmisi_t.ruangan_id = ruangan_m_2.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pen_det.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pen_det.is_sent = true) pendaftaran_r_1 ON tp_paket.pendaftaran_id = pendaftaran_r_1.pendaftaran_id
     JOIN tipepaket_m tipepaket_m_1 ON tp_paket.tipepaket_id = tipepaket_m_1.tipepaket_id
     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ruangan_m ruangan_m_1 ON tp_paket.ruangan_id = ruangan_m_1.ruangan_id
     JOIN penjamin_m penjamin_m_1 ON tp_paket.penjamin_id = penjamin_m_1.penjamin_id
     JOIN carabayar_m carabayar_m_1 ON penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id
     LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON tp_paket.kelaspelayanan_id = kelaspelayanan_m_1.kelaspelayanan_id
     LEFT JOIN ( SELECT tp_paket_1.pendaftaran_id,
            sum(tp_paket_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tp_paket_1
          WHERE tp_paket_1.is_deleted = false
          GROUP BY tp_paket_1.pendaftaran_id) total_tagihan_1 ON tp_paket.pendaftaran_id = total_tagihan_1.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t tindakansudahbayar_t_1 ON tp_paket.tindakansudahbayar_id = tindakansudahbayar_t_1.tindakansudahbayar_id
     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON tindakansudahbayar_t_1.pembayaranpelayanan_id = pembayaranpelayanan_t_1.pembayaranpelayanan_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran_1 ON tp_paket.pendaftaran_id = pembayaran_1.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'daftartindakan_id'::text)::integer AS daftartindakan_id,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_satuan'::text)::double precision AS harga_satuan,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_cyto'::text)::double precision AS harga_cyto,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_penyulit'::text)::double precision AS harga_penyulit,
            (json_array_elements(tindakanpelayanan_t.additional_data::json -> 'detail_paket'::text) ->> 'harga_total'::text)::double precision AS harga_total
           FROM tindakanpelayanan_t) paket_detail ON tp_paket.tindakanpelayanan_id = paket_detail.tindakanpelayanan_id AND daftartindakan_m.daftartindakan_id = paket_detail.daftartindakan_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1_1.pembayaran_id,
            pembayaranpelayanan_t_1_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1_1) no_pembayaran_1 ON tp_paket.pembayaran_id = no_pembayaran_1.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai_1 ON tp_paket.dokterpenanggungjawab_id = pegawai_1.pegawai_id
     LEFT JOIN pasienmasukpenunjang_t ON tp_paket.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN int_billing_r ON tp_paket.pembayaran_id = int_billing_r.pembayaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_r.id,
            tindakanpelayanan_r.tarif_dijamin / (tindakanpelayanan_r.tarif_tindakan - tindakanpelayanan_r.tarif_diskon) AS persen_dijamin,
            tindakanpelayanan_r.tarif_dibayarkan / (tindakanpelayanan_r.tarif_tindakan - tindakanpelayanan_r.tarif_diskon) AS persen_dibayarkan,
            tindakanpelayanan_r.tarif_diskon / tindakanpelayanan_r.tarif_tindakan AS persen_diskon,
            tindakanpelayanan_r.tarif_dijamin,
            tindakanpelayanan_r.tarif_dibayarkan
           FROM tindakanpelayanan_r) persen ON tp_paket.id = persen.id;");

       $this->execute('ALTER TABLE "public"."saleorder_linedetail_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."int_supplier_v";');

       $this->execute("
        CREATE VIEW \"public\".\"int_supplier_v\" AS  SELECT concat('SUP', supplier_r.supplier_id) AS sync_id_api,
    supplier_r.supplier_kode AS vendor_code,
    supplier_r.supplier_nama AS name,
    supplier_r.supplier_nama AS display_name,
    'CORPORATE'::text AS customer_type_api,
    supplier_r.no_tlp AS contact_person,
    supplier_r.no_tlp AS phone,
    supplier_r.no_tlp AS mobile,
    supplier_r.no_fax AS fax,
    supplier_r.email,
    supplier_r.website,
    supplier_r.supplier_alamat AS street,
    supplier_r.supplier_alamat AS street2,
    supplier_r.supplier_alamat AS street3,
    propinsi_m.propinsi_nama AS city,
    NULL::text AS zip,
    supplier_r.credit_limit AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS astaxid,
        CASE
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS TRUE THEN true
            WHEN supplier_r.is_active IS TRUE AND supplier_r.is_deleted IS FALSE THEN false
            WHEN supplier_r.is_active IS FALSE AND supplier_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    true AS insruance,
    true AS customer,
    true AS active,
    6 AS sync_type,
    supplier_r.keterangan_rekap,
    supplier_r.id,
    supplier_r.is_sent,
    supplier_r.is_sending,
        CASE
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = true THEN 'SUKSES'::text
            WHEN supplier_r.is_sending = false AND supplier_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN supplier_r.is_sending = true AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN supplier_r.is_sending = false AND supplier_r.is_sent = false AND supplier_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    supplier_r.supplier_id
   FROM supplier_r
     LEFT JOIN propinsi_m ON supplier_r.propinsi_id = propinsi_m.propinsi_id;");

       $this->execute('ALTER TABLE "public"."int_supplier_v" OWNER TO "postgres";');


       $this->execute('DROP VIEW if exists "public"."saleorder_line_v";');

       $this->execute("
        CREATE VIEW \"public\".\"saleorder_line_v\" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan AS price_subtotal,
    tindakanpelayanan_r.tarif_tindakan AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = 'LOS'::text THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN ruangan_m.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN ruangan_m.instalasi_id = 21 THEN 'MCU'::text
            WHEN ruangan_m.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN ruangan_m.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pendaftaran_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pembayaranpelayanan_t ON tindakanpelayanan_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = 'ACCRUAL REVERSAL'::text THEN tindakanpelayanan_r.qty_tindakan
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
    0 AS personal_amount,
    0 AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat('CATEG', 10) AS service_categ_id,
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE NULL::text
        END AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN 'IPD'::text
                ELSE 'EMERGENCY'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
                ELSE 'OPD'::text
            END
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN ruangan_m.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN ruangan_m.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN ruangan_m.instalasi_id = 21 THEN 'MCU'::text
            WHEN ruangan_m.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN ruangan_m.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    'others'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    true AS is_package,
    tipepaket_m.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat('PEG', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat('PEG', pendaftaran_r.pegadmisi_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'PAKET'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pendaftaran_r_1.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ruangan_m ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN pasienpulang_t ON pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pembayaranpelayanan_t ON tindakanpelayanan_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan::integer AS price_subtotal,
    tindakanpelayanan_r.tarif_tindakan::integer AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            ELSE 0::double precision
        END AS payer_amount,
    concat('RSPB', penjualanresep.penjualanresep_id) AS order_id,
    concat('CATEG', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE concat('PEG', penjualanresep.pegawairesep_id)
        END AS prescribe_doc_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS perform_doc_id,
    penjualanresep.ruangan_id::text AS location_id,
    penjualanresep.ruangan_nama AS department_id,
    penjualanresep.no_pembayaran AS billno,
    penjualanresep.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
    'OPD'::text AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN tindakanpelayanan_r.instalasi_id = 1 THEN 'OUTPATIENT'::text
            WHEN tindakanpelayanan_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN tindakanpelayanan_r.instalasi_id = 3 THEN 'INPATIENT'::text
            WHEN tindakanpelayanan_r.instalasi_id = 21 THEN 'MCU'::text
            WHEN tindakanpelayanan_r.instalasi_id = 4 THEN 'LABORATORY'::text
            WHEN tindakanpelayanan_r.instalasi_id = 5 THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    NULL::character varying AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjualanresep.penjamin_kode AS payer_code,
    penjualanresep.carabayar_nama AS payer_type,
    penjualanresep.penjamin_nama AS payer_name,
    penjualanresep.noresep AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
    concat('PEG', penjualanresep.pegawairesep_id) AS account_analytic_id,
    concat('PEG', penjualanresep.pegawairesep_id) AS backup_analytic_id,
    NULL::character varying AS kota,
    NULL::character varying AS kecamatan,
    NULL::character varying AS kelurahan,
    0 AS partner_id,
    penjualanresep.no_rekam_medik AS registration_code,
    penjualanresep.noresep AS number_admission,
    '-'::text AS manufacture,
    penjualanresep.tglresep::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY['ACCRUAL'::character varying::text, 'ACCRUAL REVERSAL'::character varying::text]) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket,
    penjualanresep.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN 'SUKSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing
   FROM tindakanpelayanan_r
     JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
            penjualanresep_r.pegawairesep_id,
            penjualanresep_r.noresep,
            pasien_m.no_rekam_medik,
            penjualanresep_r.tglresep,
            pasien_m.nama_pasien,
            penjamin_m.penjamin_nama,
            penjamin_m.penjamin_kode,
            carabayar_m.carabayar_nama,
            pembayaranpelayanan_t.no_pembayaran,
            penjualanresep_r.penjualanresep_id,
            penjualanresep_r.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaranpelayanan_t.tgl_pembayaran
           FROM penjualanresep_r
             JOIN pasien_m ON 0 = pasien_m.pasien_id
             JOIN ruangan_m ON penjualanresep_r.ruangan_id = ruangan_m.ruangan_id
             JOIN pembayaranpelayanan_t ON penjualanresep_r.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id
             JOIN penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
             JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
          WHERE penjualanresep_r.jenispenjualan::text = '343'::text AND penjualanresep_r.keterangan::text = 'ACCRUAL'::text AND penjualanresep_r.is_sent = true) penjualanresep ON tindakanpelayanan_r.pembayaran_id = penjualanresep.pembayaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id) pembayaran ON penjualanresep.pembayaran_id = pembayaran.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON penjualanresep.pegawairesep_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_diskon
           FROM tindakanpelayanan_t) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id;");

       $this->execute('ALTER TABLE "public"."saleorder_line_v" OWNER TO "postgres";');

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
    COALESCE(resepturdetail_t.signa ->> 'id'::text, resepturdetail_t.signa_id::text)::integer AS signa_id,
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
    COALESCE(resepturdetail_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa_nama,
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
    sr.qty_tersedia
   FROM resepturdetail_t
     JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     JOIN pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m satuan_kecil ON resepturdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     JOIN ruangan_m ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN ruangan_m ruangan_asal ON reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id
     LEFT JOIN rotd_t ON resepturdetail_t.resepturdetail_id = rotd_t.resepturdetail_id
     LEFT JOIN pegawai_m ON rotd_t.pegawairotd_id = rotd_t.pegawairotd_id
     LEFT JOIN obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN stokobatalkes_r sr ON sr.obatalkes_id = resepturdetail_t.obatalkes_id AND sr.ruangan_id = reseptur_t.ruangan_id
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
    (obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision AS qty_reseptur,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkespasien_t.hargasatuan_oa AS hargajual_satuan,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    obatalkespasien_t.etiket,
    NULL::integer AS iter,
    COALESCE(obatalkespasien_t.signa ->> 'text'::text, signaobat_m.signa_nama::text) AS signa_nama,
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
        CASE
            WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.det
            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS qty_oa,
    penjualanresep_t.ruangan_id AS ruanganasal_id,
    ruangan_tujuan.instalasi_id AS instalasiasal_id,
    obatalkespasien_t.det,
    obatalkespasien_t.det_konversi,
    sr.qty_tersedia
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN stokobatalkes_r sr ON sr.obatalkes_id = obatalkespasien_t.obatalkes_id AND sr.ruangan_id = penjualanresep_t.ruangan_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true;");

       $this->execute('ALTER TABLE "public"."inforesepdetail_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."infopasienmcu_v";');

       $this->execute("
        CREATE VIEW \"public\".\"infopasienmcu_v\" AS  SELECT 'APS'::text AS tipe_pasien,
    pendaftaran_t.pendaftaran_id,
    NULL::text AS pasienmasukpenunjang_id,
    NULL::text AS pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran AS no_masukpenunjang,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pendaftaran_t.instalasi_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.status_periksa,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama,
    antrian_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
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
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pendaftaran_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.ruangan_id,
    NULL::text AS is_bayar,
    NULL::text AS status_penunjang,
    NULL::text AS tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    pasien_m.tempat_lahir,
    pasien_m.alamat_sekarang,
    fgetnamalookup(pasien_m.warga_negara::integer) AS kebangsaan,
    pendaftaran_t.tgl_pendaftaran AS tgl_pemeriksaan,
    jenis_paket.jenis_paket AS tipepaket_nama,
    pendaftaran_t.keterangan_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    pendaftaran_t.asuransipasien_id,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.last_modified_date AS tgl_update_terakhir,
    petugas_pemakai.nama_pegawai AS petugas_nama,
    pendaftaran_t.created_date AS tgl_pembuatan,
    petugas_pembuat.nama_pegawai AS pembuat_nama,
    pendaftaran_t.penanggungbiaya_id,
    carabayar_m.carabayar_kode_warna,
        CASE
            WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
            ELSE '-'::text
        END AS no_antrian_poli,
    pendaftaran_t.limit_tagihan,
    jenis_paket.tarif_tindakan
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
     LEFT JOIN pegawai_m petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
     LEFT JOIN loginpemakai_k pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
     LEFT JOIN pegawai_m petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
     LEFT JOIN antrian_t antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            array_agg(tipepaket_m.tipepaket_nama) AS jenis_paket,
            array_agg(tindakanpelayanan_t.tarif_tindakan) AS tarif_tindakan
           FROM tindakanpelayanan_t
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pendaftaran_id) jenis_paket ON pendaftaran_t.pendaftaran_id = jenis_paket.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 21;");

       $this->execute('ALTER TABLE "public"."infopasienmcu_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

       $this->execute("
        CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = false THEN pembayaranpelayanan_t.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = false THEN pembayaranpelayanan_t.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_t.pasien_id AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_t.is_aps = true AND pendaftaran_t.instalasi_id <> 21 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 1 THEN '1'::text
            WHEN pendaftaran_t.instalasi_id = 3 THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN '2'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NULL THEN '3'::text
            WHEN pendaftaran_t.instalasi_id = 6 THEN '6'::text
            WHEN pendaftaran_t.instalasi_id = 21 THEN '5'::text
            ELSE '4'::text
        END AS patient_type,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_t.penjamin_id)
            ELSE concat('PEN', pasienadmisi_t.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    COALESCE(pembayaran.personal_amount, 0::double precision) AS personal_amount,
    COALESCE(pembayaran.payer_amount, 0::double precision) AS payer_amount,
        CASE
            WHEN pembayaranpelayanan_t.is_deleted = true THEN 0::double precision
            ELSE COALESCE(pembayaran_amount.total_tagihan, 0::double precision)
        END AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
          WHERE pendaftaran_r_1.is_sent = true) pendaftaran_r ON pendaftaran_t.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN ( SELECT tmp_pembayaran.no_pembayaran,
            tmp_pembayaran.tgl_pembayaran,
            tmp_pembayaran.is_deleted,
            tmp_pembayaran.pendaftaran_id
           FROM ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.no_pembayaran,
                    pembayaranpelayanan_t_1.tgl_pembayaran,
                    pembayaranpelayanan_t_1.is_deleted,
                    pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  WHERE pembayaranpelayanan_t_1.is_deleted = false
                  GROUP BY pembayaranpelayanan_t_1.no_pembayaran, pembayaranpelayanan_t_1.tgl_pembayaran, pembayaranpelayanan_t_1.is_deleted, pembayaranpelayanan_t_1.pendaftaran_id) tmp_pembayaran) pembayaranpelayanan_t ON pembayaran_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount) AS total_tagihan,
            sum(pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian + pembayaran_t.penggunaan_uangmuka) AS personal_amount,
            sum(pembayaran_t.total_dijamin) AS payer_amount
           FROM pembayaran_t
             JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t_1.pembayaran_id AND pembayaranpelayanan_t_1.is_deleted = false
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON pembayaran_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount) AS total_tagihan
           FROM pembayaran_t
          WHERE pembayaran_t.is_deleted = false
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran_amount ON pembayaran_r.pendaftaran_id = pembayaran_amount.pendaftaran_id
  WHERE pembayaran_r.is_update = false
UNION ALL
 SELECT pembayaran_r.id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN '-'::character varying
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.no_pembayaran
            ELSE '-'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    0 AS partner_id,
    penjualanresep_t.tglresep AS date_order,
    '1'::text AS patient_type,
    concat('PEN', penjualanresep_t.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 'draft'::text
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian + pembayaran.penggunaan_uangmuka
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_dijamin
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tagihan
            ELSE 0::double precision
        END AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.is_deleted,
            pembayaranpelayanan_t.pembayaran_id
           FROM pembayaranpelayanan_t
             JOIN ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) max_pembayaran ON pembayaranpelayanan_t.pendaftaran_id = max_pembayaran.pendaftaran_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id) pembayaranpelayanan ON pembayaran_r.pembayaran_id = pembayaranpelayanan.pembayaran_id
     JOIN penjualanresep_t ON pembayaranpelayanan.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.jenispenjualan::text = '343'::text
     JOIN ( SELECT penjualanresep_r_1.id,
            penjualanresep_r_1.penjualanresep_id
           FROM penjualanresep_r penjualanresep_r_1
             JOIN ( SELECT max(penjualanresep_r_2.id) AS id,
                    penjualanresep_r_2.penjualanresep_id
                   FROM penjualanresep_r penjualanresep_r_2
                  WHERE penjualanresep_r_2.keterangan::text = 'ACCRUAL'::text
                  GROUP BY penjualanresep_r_2.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
          WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON penjualanresep_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin,
            pembayaran_t.penggunaan_uangmuka
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;");

       $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."int_purchase_v";');

       $this->execute("
        CREATE VIEW \"public\".\"int_purchase_v\" AS  SELECT 'obat'::text AS jenis,
    concat('POS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
    penerimaansupp_r.no_penerimaan AS origin,
    penerimaansupp_r.no_penerimaan AS title,
    penerimaansupp_r.no_penerimaan AS name,
    penerimaansupp_r.no_penerimaan AS rfq,
    penerimaansupp_r.no_penerimaan AS purchase_name,
    concat(supplier_m.supplier_nama, '-', penerimaansupp_r.no_penerimaan) AS vendor_ref,
    13 AS currency_id,
    penerimaansupp_r.tgl_penerimaan AS date_order,
    concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    penerimaansupp_detail.amount_untaxed,
    penerimaansupp_detail.amount_tax,
    penerimaansupp_detail.total_discount,
    penerimaansupp_detail.total_tampilan,
    penerimaansupp_detail.amount_total,
    'draft'::text AS state,
    penerimaansupp_r.tgl_verifikasi AS date_planned,
    false AS is_return,
    penerimaansupp_detail.ppn,
    0 AS pph,
    penerimaansupp_r.tgl_penerimaan AS wipro_date,
    true AS no_approval,
    penerimaansupp_r.is_consigment AS is_consignment,
    '6'::text AS sync_type,
    'POS'::text AS tipe_rekap,
    penerimaansupp_r.id,
    penerimaansupp_r.is_sending,
    penerimaansupp_r.is_sent,
        CASE
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL AND penerimaansupp_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaansupp_r.is_sending = false AND penerimaansupp_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaansupp_r
     JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT penerimaansupp_t.penerimaansupp_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            supplier_m_1.supplier_nama,
            sum(obatalkes_m.harganetto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
            sum((penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision + (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS amount_total,
            pajak_m.pajak_persen AS ppn
           FROM penerimaansuppdetail_t
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN obatalkes_m ON penerimaansuppdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id
          GROUP BY penerimaansupp_t.penerimaansupp_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m_1.supplier_nama, pajak_m.pajak_persen) penerimaansupp_detail ON penerimaansupp_detail.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id
  WHERE penerimaansupp_r.is_deleted = false AND penerimaansupp_r.is_verifikasi = true
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('POM', penerimaanobat_r.penerimaanobat_id) AS sync_id_api,
    penerimaanobat_r.no_penerimaan AS origin,
    penerimaanobat_r.no_penerimaan AS title,
    penerimaanobat_r.no_penerimaan AS name,
    penerimaanobat_r.no_penerimaan AS rfq,
    penerimaanobat_r.no_penerimaan AS purchase_name,
    concat(supplier_m.supplier_nama, '-', validasipoobat_t.no_poobat) AS vendor_ref,
    13 AS currency_id,
    penerimaanobat_r.tgl_penerimaan AS date_order,
    concat('SUP', penerimaanobat_r.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    penerimaanobatdetail.amount_untaxed,
    penerimaanobatdetail.amount_tax,
    penerimaanobatdetail.total_discount,
    penerimaanobatdetail.total_tampilan,
    penerimaanobatdetail.amount_total,
    'draft'::text AS state,
    penerimaanobat_r.tgl_penerimaan AS date_planned,
    false AS is_return,
    penerimaanobatdetail.ppn,
    0 AS pph,
    penerimaanobat_r.tgl_penerimaan AS wipro_date,
    true AS no_approval,
    penerimaanobat_r.is_consigment AS is_consignment,
    '6'::text AS sync_type,
    'POM'::text AS tipe_rekap,
    penerimaanobat_r.id,
    penerimaanobat_r.is_sending,
    penerimaanobat_r.is_sent,
        CASE
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = false AND penerimaanobat_r.id_sync_sercon IS NOT NULL AND penerimaanobat_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanobat_r.is_sending = false AND penerimaanobat_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanobat_r.is_sending = true AND penerimaanobat_r.is_sent = false AND penerimaanobat_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaanobat_r
     JOIN ( SELECT penerimaanobat_t.penerimaanobat_id,
            penerimaanobat_t.no_penerimaan,
            po.no_poobat,
            penerimaanobat_t.supplier_id,
            supplier_m_1.supplier_nama,
            sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn
           FROM penerimaanobatdetail_t
             JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
             JOIN obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaanobat_t.supplier_id = supplier_m_1.supplier_id
             LEFT JOIN ( SELECT validasipoobat_t_1.validasipoobat_id,
                    pajak_m.pajak_persen,
                    validasipoobat_t_1.no_poobat
                   FROM validasipoobat_t validasipoobat_t_1
                     JOIN pajak_m ON validasipoobat_t_1.pajak_id = pajak_m.pajak_id
                  WHERE validasipoobat_t_1.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
             LEFT JOIN ( SELECT penerimaanobatdetail_t_1.penerimaanobatdetail_id,
                    penerimaanobatdetail_t_1.qty_diterima AS qty_konversi,
                    penerimaanobatdetail_t_1.harga / satuankonversi_m.nilai_konversi AS harga_konversi
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                     JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                  WHERE satuankonversi_m.is_deleted = false) qty_konversi ON penerimaanobatdetail_t.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
          GROUP BY penerimaanobat_t.penerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen) penerimaanobatdetail ON penerimaanobatdetail.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id
     JOIN supplier_m ON penerimaanobat_r.supplier_id = supplier_m.supplier_id
     JOIN validasipoobat_t ON penerimaanobat_r.validasipoobat_id = validasipoobat_t.validasipoobat_id
  WHERE penerimaanobat_r.is_deleted = false
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('PBM', penerimaanbarang_r.penerimaanbarang_id) AS sync_id_api,
    penerimaanbarang_r.no_penerimaan AS origin,
    penerimaanbarang_r.no_penerimaan AS title,
    penerimaanbarang_r.no_penerimaan AS name,
    penerimaanbarang_r.no_penerimaan AS rfq,
    penerimaanbarang_r.no_penerimaan AS purchase_name,
    concat(supplier_m.supplier_nama, '-', validasipobarang_t.no_pobarang) AS vendor_ref,
    13 AS currency_id,
    penerimaanbarang_r.tgl_penerimaan AS date_order,
    concat('SUP', penerimaanbarang_r.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    penerimaanbarangdetail.amount_untaxed,
    penerimaanbarangdetail.amount_tax,
    penerimaanbarangdetail.total_discount,
    penerimaanbarangdetail.total_tampilan,
    penerimaanbarangdetail.amount_total,
    'draft'::text AS state,
    penerimaanbarang_r.tgl_penerimaan AS date_planned,
    false AS is_return,
    penerimaanbarangdetail.ppn,
    0 AS pph,
    penerimaanbarang_r.tgl_penerimaan AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    '6'::text AS sync_type,
    'PBM'::text AS tipe_rekap,
    penerimaanbarang_r.id,
    penerimaanbarang_r.is_sending,
    penerimaanbarang_r.is_sent,
        CASE
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = false AND penerimaanbarang_r.id_sync_sercon IS NOT NULL AND penerimaanbarang_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaanbarang_r.is_sending = false AND penerimaanbarang_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaanbarang_r.is_sending = true AND penerimaanbarang_r.is_sent = false AND penerimaanbarang_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaanbarang_r
     JOIN ( SELECT penerimaanbarang_t.penerimaanbarang_id,
            penerimaanbarang_t.no_penerimaan,
            po.no_pobarang,
            penerimaanbarang_t.supplier_id,
            supplier_m_1.supplier_nama,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn
           FROM penerimaanbarangdetail_t
             JOIN penerimaanbarang_t ON penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
             JOIN barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaanbarang_t.supplier_id = supplier_m_1.supplier_id
             LEFT JOIN ( SELECT validasipobarang_t_1.validasipobarang_id,
                    pajak_m.pajak_persen,
                    validasipobarang_t_1.no_pobarang
                   FROM validasipobarang_t validasipobarang_t_1
                     JOIN pajak_m ON validasipobarang_t_1.pajak_id = pajak_m.pajak_id
                  WHERE validasipobarang_t_1.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
             LEFT JOIN ( SELECT penerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
                    penerimaanbarangdetail_t_1.qty_diterima AS qty_konversi,
                    penerimaanbarangdetail_t_1.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi
                   FROM penerimaanbarangdetail_t penerimaanbarangdetail_t_1
                     JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                  WHERE satuankonversibrg_m.is_deleted = false) qty_konversi ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id
          GROUP BY penerimaanbarang_t.penerimaanbarang_id, penerimaanbarang_t.no_penerimaan, po.no_pobarang, penerimaanbarang_t.supplier_id, supplier_m_1.supplier_nama, po.pajak_persen) penerimaanbarangdetail ON penerimaanbarangdetail.penerimaanbarang_id = penerimaanbarang_r.penerimaanbarang_id
     JOIN supplier_m ON penerimaanbarang_r.supplier_id = supplier_m.supplier_id
     JOIN validasipobarang_t ON penerimaanbarang_r.validasipobarang_id = validasipobarang_t.validasipobarang_id
  WHERE penerimaanbarang_r.is_deleted = false
UNION ALL
 SELECT 'BARANG'::text AS jenis,
    concat('PBS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
    penerimaansupp_r.no_penerimaan AS origin,
    penerimaansupp_r.no_penerimaan AS title,
    penerimaansupp_r.no_penerimaan AS name,
    penerimaansupp_r.no_penerimaan AS rfq,
    penerimaansupp_r.no_penerimaan AS purchase_name,
    concat(supplier_m.supplier_nama, '-', penerimaansupp_r.no_penerimaan) AS vendor_ref,
    13 AS currency_id,
    penerimaansupp_r.tgl_penerimaan AS date_order,
    concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    penerimaansupp_detail.amount_untaxed,
    penerimaansupp_detail.amount_tax,
    penerimaansupp_detail.total_discount,
    penerimaansupp_detail.total_tampilan,
    penerimaansupp_detail.amount_total,
    'draft'::text AS state,
    penerimaansupp_r.tgl_verifikasi AS date_planned,
    false AS is_return,
    penerimaansupp_detail.ppn,
    0 AS pph,
    penerimaansupp_r.tgl_penerimaan AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    '6'::text AS sync_type,
    'PBS'::text AS tipe_rekap,
    penerimaansupp_r.id,
    penerimaansupp_r.is_sending,
    penerimaansupp_r.is_sent,
        CASE
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL AND penerimaansupp_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = true THEN 'SUKSES'::text
            WHEN penerimaansupp_r.is_sending = false AND penerimaansupp_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN penerimaansupp_r.is_sending = true AND penerimaansupp_r.is_sent = false AND penerimaansupp_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses
   FROM penerimaansupp_r
     JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT penerimaansupp_t.penerimaansupp_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            supplier_m_1.supplier_nama,
            sum(barang_m.barang_harganetto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
            sum((penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision + (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_t.qty_besar::double precision) AS amount_total,
            pajak_m.pajak_persen AS ppn
           FROM penerimaansuppdetail_t
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN barang_m ON penerimaansuppdetail_t.barang_id = barang_m.barang_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id
          GROUP BY penerimaansupp_t.penerimaansupp_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, supplier_m_1.supplier_nama, pajak_m.pajak_persen) penerimaansupp_detail ON penerimaansupp_detail.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id
  WHERE penerimaansupp_r.is_deleted = false AND penerimaansupp_r.is_verifikasi = true;");

       $this->execute('ALTER TABLE "public"."int_purchase_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."invoiceridetail_v";');

       $this->execute("
        CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.groupinacbg_id,
    layanan.groupinacbg_nama,
    layanan.tindakan_obat_id,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan,
    layanan.tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    layanan.kamarruangan_nokamar AS kamar,
    layanan.no_tempattidur AS no_bed,
    layanan.kelaspelayanan_nama AS kelas,
    layanan.pembayaran_id,
    layanan.tarif_dijamin,
    layanan.tarif_dibayarkan,
    layanan.tarif_diskon,
    layanan.tarifcyto_tindakan,
    layanan.cyto_tindakan,
    layanan.is_visite,
    layanan.kelompoktindakan_id,
    layanan.tarifpenyulit_tindakan,
    layanan.pasienadmisi_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_diskon,
            tindakanpelayanan_t.cyto_tindakan,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            daftartindakan_m.kelompoktindakan_id,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.pasienadmisi_id
           FROM tindakanpelayanan_t
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
             LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            obatalkes_m.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            'Drugs & Consumables'::character varying AS kelompok,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi,
            obatalkespasien_t.additional_data,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            0 AS tarifcyto_tindakan,
            obatalkespasien_t.tarif_diskon,
            false AS cyto_tindakan,
            false AS is_visite,
            NULL::integer AS kelompoktindakan_id,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.pasienadmisi_id
           FROM obatalkespasien_t
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id
             JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
          WHERE obatalkespasien_t.is_deleted = false) layanan ON pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id;");

       $this->execute('ALTER TABLE "public"."invoiceridetail_v" OWNER TO "postgres";');

       $this->execute('DROP VIEW if exists "public"."invoicesudahbayardetail_v";');

       $this->execute("
        CREATE VIEW \"public\".\"invoicesudahbayardetail_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.pelayanan_id,
    tagihan.pasien_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    pasien_m.tanggal_lahir,
    tagihan.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    tagihan.tgl_pendaftaran,
    tagihan.no_pendaftaran,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.tgl_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_tinpelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
    tagihan.penjamin_tinpelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.jeniskasuspenyakit_id,
    tagihan.pembayaranpelayanan_id,
    tagihan.biaya_administrasi,
    tagihan.e_collection,
    tagihan.nama_pemrekening,
    tagihan.no_rekening,
    tagihan.carabayar_pelayanan_id,
    tagihan.carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    tagihan.penjamin_pelayanan,
    tagihan.tarif_cyto,
    tagihan.tandabuktibayar_id,
    tagihan.jeniskasuspenyakit_nama,
    tagihan.penjualanresep_id,
    tagihan.is_konsultasi,
    dok_tindakan.nama_pegawai AS dokter_tindakan,
    tagihan.pembayaran_id,
    tagihan.satuan_kecil AS uom,
    tagihan.tarif_dijamin,
    tagihan.tarif_dibayarkan,
    tagihan.groupinacbg_nama,
    tagihan.tarif_diskon,
    tagihan.is_visite,
    tagihan.tarifpenyulit_tindakan,
    tagihan.jenis_racikan
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_paket'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            NULL::text AS satuan_kecil,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            NULL::character varying AS groupinacbg_nama,
            tindakanpelayanan_t.tarif_diskon,
            false AS is_visite,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            NULL::text AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Drugs & Consumables'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT penjualanresep_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            penjualanresep_t.noresep AS no_pendaftaran,
            NULL::character varying AS umur,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'kelompok_obat'::character varying AS kelompoktindakan_nama,
            NULL::integer AS jeniskasuspenyakit_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.e_collection,
            pembayaranpelayanan_t.nama_pemrekening,
            pembayaranpelayanan_t.no_rekening,
            pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            pembayaranpelayanan_t.tandabuktibayar_id,
            NULL::character varying AS jeniskasuspenyakit_nama,
            obatalkespasien_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::boolean AS is_konsultasi,
            NULL::bigint AS doktertindakan_id,
            pembayaranpelayanan_t.pembayaran_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            groupinacbg_m.groupinacbg_nama,
            obatalkespasien_t.tarif_diskon,
            false AS is_visite,
            0 AS tarifpenyulit_tindakan,
                CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                    WHEN 0 THEN 'Non Racikan'::text
                    ELSE 'Racikan'::text
                END AS jenis_racikan
           FROM obatalkespasien_t
             JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
             LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE penjualanresep_t.jenispenjualan::integer <> 344) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dok_tindakan ON tagihan.doktertindakan_id = dok_tindakan.pegawai_id;");

       $this->execute('ALTER TABLE "public"."invoicesudahbayardetail_v" OWNER TO "postgres";');
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210524_093916_oddo_20210524_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210524_093916_oddo_20210524_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
