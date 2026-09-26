<?php

use yii\db\Migration;

/**
 * Class m221221_065726_migrate_hotfix_view_int_obatalkespasien_v
 */
class m221221_065726_migrate_hotfix_view_int_obatalkespasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."int_obatalkespasien_v";
        ');

        $this->execute('
            CREATE VIEW "public"."int_obatalkespasien_v" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat(\'OBT\', obatalkespasien_r.id) AS sync_id_api,
    concat(\'OBT\', obatalkespasien_r.obatalkes_id) AS product_id,
    concat(obatalkes_m.obatalkes_nama, \'//\', kelaspelayanan_m.kelaspelayanan_nama, \'//\', ruangan_m.ruangan_nama, \'//\') AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) <> \'\'::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) <> \'\'::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END *
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS price_subtotal,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) <> \'\'::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END *
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.is_penjaminutama = true THEN
            CASE
                WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id::character varying AS order_id,
    concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.pasienadmisi_id IS NULL THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN obatalkespasien_r.pasienadmisi_id IS NOT NULL AND pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN obatalkespasien_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            ELSE concat(\'PEG\', obatalkespasien_r.pegawai_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id = 1 THEN concat(\'PEG\', obatalkespasien_r.pegawai_id)
            ELSE concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
        END AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    \'LOS\'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN obatalkespasien_r.pasienadmisi_id IS NOT NULL THEN \'IPD\'::text
                ELSE \'EMERGENCY\'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r.instalasi_id = 1 THEN \'OPD\'::text
                WHEN pendaftaran_r.instalasi_id = 2 THEN \'EMERGENCY\'::text
                WHEN pendaftaran_r.instalasi_id = 3 THEN \'IPD\'::text
                WHEN pendaftaran_r.instalasi_id = 21 THEN \'MCU\'::text
                ELSE \'OPD\'::text
            END
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN \'PHARMACY OUTPATIENT\'::text
            ELSE \'PHARMACY INPATIENT\'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'GENERAL\'::character varying) AS bed_type,
        CASE
            WHEN cob.is_penjaminutama IS NULL THEN concat(\'PEN\', obatalkespasien_r.penjamin_id)
            WHEN cob.is_penjaminutama = false THEN cob.main_payer
            ELSE cob.payer
        END AS payer,
        CASE
            WHEN cob.is_penjaminutama IS NULL THEN penjamin_m.penjamin_kode
            WHEN cob.is_penjaminutama = false THEN cob.main_payer_code
            ELSE cob.payer_code
        END AS payer_code,
        CASE
            WHEN cob.is_penjaminutama IS NULL THEN carabayar_m.carabayar_nama
            WHEN cob.is_penjaminutama = false THEN cob.main_payer_type
            ELSE cob.payer_type
        END AS payer_type,
        CASE
            WHEN cob.is_penjaminutama IS NULL THEN penjamin_m.penjamin_nama
            WHEN cob.is_penjaminutama = false THEN cob.main_payer_name
            ELSE cob.payer_name
        END AS payer_name,
        CASE
            WHEN obatalkespasien_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE COALESCE(penjualanresep_t.no_resep, obatalkespasien_r.no_obatalkespasien)
        END AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_id,
    NULL::text AS package_name,
    obatalkespasien_t.cost AS cost_unit,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END * obatalkespasien_t.cost AS cost_total,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id = 1 THEN concat(\'PEG\', obatalkespasien_r.pegawai_id)
            ELSE concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
        END AS account_analytic_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
            WHEN obatalkespasien_r.penjualanresep_id IS NULL AND pegawai.kelompokpegawai_id = 1 THEN concat(\'PEG\', obatalkespasien_r.pegawai_id)
            ELSE concat(\'PEG\', COALESCE(pendaftaran_r.pegadmisi_id, pendaftaran_r.pegawai_id))
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id::text AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    \'-\'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN \'SUKSES\'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.nama_pasien,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
        CASE
            WHEN obatalkespasien_r.is_penjaminutama = false AND obatalkespasien_r.tarif_dijamin = 0::double precision THEN obatalkespasien_r.tarif_dijamin
            WHEN obatalkespasien_r.is_penjaminutama = false AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN obatalkespasien_r.tarif_dijamin + obatalkespasien_r.tarif_diskon
            ELSE 0::double precision
        END AS subpayer_amount,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.cob_id::text
            ELSE NULL::text
        END AS cob_id,
        CASE
            WHEN cob.is_penjaminutama = true THEN NULL::text
            ELSE cob.cob_id::text
        END AS sub_cob_id,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.is_sent
            ELSE cob.main_is_sent
        END AS is_sent_cob,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.main_is_sent
            ELSE cob.is_sent
        END AS is_sent_subcob,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.main_payer
            ELSE cob.payer
        END AS sub_payer,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.main_payer_code
            ELSE cob.payer_code
        END AS sub_payer_code,
        CASE
            WHEN cob.is_penjaminutama = true THEN cob.main_payer_type::text
            ELSE cob.payer_type::text
        END AS sub_payer_type,
    NULL::text AS discharge_reason,
    NULL::text AS admission_type,
    pendaftaran_r.no_pendaftaran AS ref_admission_no,
    pendaftaran_r.no_rujukan AS referral_number,
    concat(\'OBT\', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    false AS cyto,
    false AS penyulit,
    NULL::text AS room_id,
    NULL::text AS bed_type_id,
    NULL::text AS eligible_bed_type_id,
    NULL::text AS alloted_bed_type_id,
    NULL::text AS bed_id,
    NULL::text AS bed_allocation_start,
    NULL::text AS bed_allocation_end,
    concat(\'REF\', pendaftaran_r.perujuk_id) AS referral_doc_id,
    NULL::text AS surgery_operator_id,
    NULL::text AS surgery_operator_ass_id,
    NULL::text AS anesthetist_operator_id,
    NULL::text AS anesthetist_operator_ass_id,
    NULL::text AS surgery_team_id,
    NULL::text AS surgery_team_ass_id,
    NULL::text AS secondary_diagnosis_code,
    NULL::text AS secondary_diagnosis_name,
    NULL::text AS repeat_diagnosis_code,
    NULL::text AS discount_name,
    NULL::text AS discount_id,
    NULL::text AS discount_date,
    NULL::text AS discount_level,
    NULL::text AS discount_reason,
    NULL::text AS is_baby,
    NULL::text AS parent_admission_no,
    NULL::text AS sales_id,
    NULL::text AS sales_date,
    NULL::text AS sales_return_id,
    NULL::text AS sales_return_date,
    NULL::text AS hna_unit,
    NULL::text AS hna_total,
    NULL::text AS base_price_unit,
    NULL::text AS base_price_total,
    NULL::text AS order_type,
    NULL::text AS batch_id,
    NULL::text AS batch_rel,
    NULL::text AS batch_expired
   FROM obatalkespasien_r
     JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
            obatalkespasien_t_1.tarif_dibayarkan,
            obatalkespasien_t_1.tarif_dijamin,
            obatalkespasien_t_1.tarif_diskon,
            obatalkespasien_t_1.cost
           FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN a.instalasi_id
                    ELSE 3
                END AS instalasi_id,
            a.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            a.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
            a.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            rujukan_t.no_rujukan,
            rujukan_t.perujuk_id
           FROM pendaftaran_r a
             JOIN ( SELECT a1.pasien_id,
                    a1.no_rekam_medik,
                    a1.nama_pasien,
                    a1.kabupaten_id,
                    a1.kecamatan_id,
                    a1.kelurahan_id
                   FROM pasien_m a1) pasien_m ON a.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a2.pendaftaran_id,
                    a2.pegawai_id,
                    a2.pasienadmisi_id
                   FROM pendaftaran_t a2) pendaftaran_t ON a.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a3.pasienadmisi_id,
                    a3.pegawai_id
                   FROM pasienadmisi_t a3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a4.kabupaten_id,
                    a4.kabupaten_nama
                   FROM kabupaten_m a4) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a5.kecamatan_id,
                    a5.kecamatan_nama
                   FROM kecamatan_m a5) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a6.kelurahan_id,
                    a6.kelurahan_nama
                   FROM kelurahan_m a6) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a7.pasienpulang_id,
                    a7.tglpasienpulang
                   FROM pasienpulang_t a7) pasienpulang_t ON a.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a8.rujukan_id,
                    a8.no_rujukan,
                    a8.rujukandari_id AS perujuk_id
                   FROM rujukan_t a8) rujukan_t ON a.rujukan_id = rujukan_t.rujukan_id
          WHERE a.is_sent = true AND a.keterangan::text = \'INSERT\'::text) pendaftaran_r ON obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.obatalkes_id,
            a.jenisobatalkes_id,
            a.obatalkes_nama,
            a.satuankecil_id
           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama,
            a.servicegroup_id,
            a.servicecategory_id
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.servicegroup_id,
            a.servicegroup_nama
           FROM servicegroup_m a) servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON obatalkespasien_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran ON obatalkespasien_r.pembayaran_id = pembayaran.pembayaran_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            spesialis_m.spesialis_nama,
            a.kelompokpegawai_id
           FROM pegawai_m a
             LEFT JOIN ( SELECT a1.spesialis_id,
                    a1.spesialis_nama
                   FROM spesialis_m a1) spesialis_m ON a.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT a.penjualanresep_id,
            a.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN a.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t a
             LEFT JOIN ( SELECT a1.penjualanresep_id,
                    a1.noresep
                   FROM reseptur_t a1) reseptur_t ON a.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY a.penjualanresep_id, a.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN a.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT a.id,
            a.pembayaran_id,
            a.is_sent
           FROM int_billing_r a
          WHERE a.is_sent = true) int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id
     LEFT JOIN ( SELECT DISTINCT ON (a.obatsudahbayar_id) a.obatsudahbayar_id,
            a.pembayaranpelayanan_id,
            pembayaranpelayanan_r.id AS cob_id,
            pembayaranpelayanan_r.is_sent,
            pembayaranpelayanan_r.is_penjaminutama,
            concat(\'PEN\', cob_penjamin.penjamin_id) AS payer,
            cob_penjamin.penjamin_kode AS payer_code,
            cob_penjamin.carabayar_nama AS payer_type,
            cob_penjamin.penjamin_nama AS payer_name,
            concat(\'PEN\', main_penjamin.penjamin_id) AS main_payer,
            main_penjamin.penjamin_kode AS main_payer_code,
            main_penjamin.carabayar_nama AS main_payer_type,
            main_penjamin.penjamin_nama AS main_payer_name,
            pembayaranpelayanan_r.main_is_sent,
            pembayaranpelayanan_r.main_id AS main_cob_id
           FROM obatsudahbayar_t a
             LEFT JOIN ( SELECT a1.id,
                    a1.pembayaranpelayanan_id,
                    a1.is_sent,
                    a1.penjamin_id,
                    a1.pembayaran_id,
                    a1.is_penjaminutama,
                    a2.id AS main_id,
                    a2.penjamin_id AS main_penjamin_id,
                    a2.is_sent AS main_is_sent
                   FROM pembayaranpelayanan_r a1
                     LEFT JOIN pembayaranpelayanan_r a2 ON a1.pembayaran_id = a2.pembayaran_id AND a2.is_penjaminutama <> a1.is_penjaminutama) pembayaranpelayanan_r ON a.pembayaranpelayanan_id = pembayaranpelayanan_r.pembayaranpelayanan_id
             LEFT JOIN ( SELECT a1.penjamin_id,
                    a1.penjamin_kode,
                    a2.carabayar_nama,
                    a1.penjamin_nama
                   FROM penjamin_m a1
                     LEFT JOIN carabayar_m a2 ON a1.carabayar_id = a2.carabayar_id) cob_penjamin ON pembayaranpelayanan_r.penjamin_id = cob_penjamin.penjamin_id
             LEFT JOIN ( SELECT a1.penjamin_id,
                    a1.penjamin_kode,
                    a2.carabayar_nama,
                    a1.penjamin_nama
                   FROM penjamin_m a1
                     JOIN carabayar_m a2 ON a1.carabayar_id = a2.carabayar_id) main_penjamin ON pembayaranpelayanan_r.main_penjamin_id = main_penjamin.penjamin_id) cob ON obatalkespasien_r.obatsudahbayar_id = cob.obatsudahbayar_id
  WHERE (obatalkespasien_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR (pendaftaran_r.instalasi_id = ANY (ARRAY[2, 3])) AND ((obatalkespasien_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR NOT (obatalkespasien_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])))
UNION ALL
 SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat(\'OBT\', obatalkespasien_r.id) AS sync_id_api,
    concat(\'OBT\', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END AS product_uom_qty,
        CASE
            WHEN (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) IS NOT NULL AND (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) <> \'\'::text AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text)::double precision)
            ELSE obatalkespasien_r.hargasatuan_oa
        END AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    obatalkespasien_r.hargajual_oa AS price_total,
        CASE
            WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
            WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.is_penjaminutama = true THEN
            CASE
                WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS payer_amount,
    concat(\'RSPB\', obatalkespasien_r.penjualanresep_id) AS order_id,
    concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat(\'PEG\', penjualanresep_t.pegawai_id) AS primary_doc_id,
    concat(\'PEG\', penjualanresep_t.pegawai_id) AS prescribe_doc_id,
    NULL::text AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    \'LOS\'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
    \'OPD\'::text AS patient_group,
    NULL::text AS special_group,
    \'PHARMACY OUTPATIENT\'::text AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'GENERAL\'::character varying) AS bed_type,
    concat(\'PEN\', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    COALESCE(penjualanresep_t.no_resep) AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_id,
    NULL::text AS package_name,
    obatalkespasien_t.cost AS cost_unit,
        CASE
            WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
            WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
            ELSE obatalkespasien_r.qty_oa
        END * obatalkespasien_t.cost AS cost_total,
    concat(\'PEG\', penjualanresep_t.pegawai_id) AS account_analytic_id,
    concat(\'PEG\', penjualanresep_t.pegawai_id) AS backup_analytic_id,
    penjualanresep_r.kota,
    penjualanresep_r.kecamatan,
    penjualanresep_r.kelurahan,
    penjualanresep_r.pasien_id AS partner_id,
    penjualanresep_r.no_rekam_medik AS registration_code,
    penjualanresep_r.noresep AS number_admission,
    \'-\'::text AS manufacture,
    penjualanresep_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
        CASE
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN \'SUKSES\'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep_r.nama_pasien,
    penjualanresep_r.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
        CASE
            WHEN obatalkespasien_r.is_penjaminutama = false AND obatalkespasien_r.tarif_dijamin = 0::double precision THEN obatalkespasien_r.tarif_dijamin
            WHEN obatalkespasien_r.is_penjaminutama = false AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN obatalkespasien_r.tarif_dijamin + obatalkespasien_r.tarif_diskon
            ELSE 0::double precision
        END AS subpayer_amount,
    NULL::text AS cob_id,
    NULL::text AS sub_cob_id,
    NULL::boolean AS is_sent_cob,
    NULL::boolean AS is_sent_subcob,
    NULL::text AS sub_payer,
    NULL::text AS sub_payer_code,
    NULL::text AS sub_payer_type,
    NULL::text AS discharge_reason,
    NULL::text AS admission_type,
    NULL::text AS ref_admission_no,
    NULL::text AS referral_number,
    concat(\'OBT\', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    false AS cyto,
    false AS penyulit,
    NULL::text AS room_id,
    NULL::text AS bed_type_id,
    NULL::text AS eligible_bed_type_id,
    NULL::text AS alloted_bed_type_id,
    NULL::text AS bed_id,
    NULL::text AS bed_allocation_start,
    NULL::text AS bed_allocation_end,
    NULL::text AS referral_doc_id,
    NULL::text AS surgery_operator_id,
    NULL::text AS surgery_operator_ass_id,
    NULL::text AS anesthetist_operator_id,
    NULL::text AS anesthetist_operator_ass_id,
    NULL::text AS surgery_team_id,
    NULL::text AS surgery_team_ass_id,
    NULL::text AS secondary_diagnosis_code,
    NULL::text AS secondary_diagnosis_name,
    NULL::text AS repeat_diagnosis_code,
    NULL::text AS discount_name,
    NULL::text AS discount_id,
    NULL::text AS discount_date,
    NULL::text AS discount_level,
    NULL::text AS discount_reason,
    NULL::text AS is_baby,
    NULL::text AS parent_admission_no,
    NULL::text AS sales_id,
    NULL::text AS sales_date,
    NULL::text AS sales_return_id,
    NULL::text AS sales_return_date,
    NULL::text AS hna_unit,
    NULL::text AS hna_total,
    NULL::text AS base_price_unit,
    NULL::text AS base_price_total,
    NULL::text AS order_type,
    NULL::text AS batch_id,
    NULL::text AS batch_rel,
    NULL::text AS batch_expired
   FROM obatalkespasien_r
     JOIN ( SELECT b.obatalkespasien_id,
            b.tarif_dibayarkan,
            b.tarif_dijamin,
            b.tarif_diskon,
            b.cost
           FROM obatalkespasien_t b) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN ( SELECT DISTINCT ON (b.penjualanresep_id) b.id,
            b.penjualanresep_id,
            b.pegawai_id,
            b.pasienadmisi_id,
                CASE
                    WHEN b.jenispenjualan::text = \'345\'::text THEN concat(\'PEG\', b.karyawan_id)
                    ELSE 0::text
                END AS pasien_id,
            pasien_m.no_rekam_medik,
                CASE
                    WHEN b.jenispenjualan::text = \'345\'::text THEN karyawan.nama_pegawai
                    ELSE b.nama_pembeli
                END AS nama_pasien,
            b.noresep,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            b.tglresep AS tglpasienpulang,
            b.tglresep
           FROM penjualanresep_r b
             LEFT JOIN ( SELECT b1.pasien_id,
                    b1.no_rekam_medik,
                    b1.nama_pasien,
                    b1.kabupaten_id,
                    b1.kecamatan_id,
                    b1.kelurahan_id
                   FROM pasien_m b1) pasien_m ON 0 = pasien_m.pasien_id
             LEFT JOIN ( SELECT b2.pegawai_id,
                    b2.nama_pegawai
                   FROM pegawai_m b2) karyawan ON b.karyawan_id = karyawan.pegawai_id
             LEFT JOIN ( SELECT b3.kabupaten_id,
                    b3.kabupaten_nama
                   FROM kabupaten_m b3) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT b4.kecamatan_id,
                    b4.kecamatan_nama
                   FROM kecamatan_m b4) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT b5.kelurahan_id,
                    b5.kelurahan_nama
                   FROM kelurahan_m b5) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
          WHERE b.is_sent = true AND b.keterangan::text = \'INSERT\'::text) penjualanresep_r ON obatalkespasien_r.penjualanresep_id = penjualanresep_r.penjualanresep_id
     JOIN ( SELECT b.obatalkes_id,
            b.jenisobatalkes_id,
            b.obatalkes_nama,
            b.satuankecil_id
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT b.jenisobatalkes_id,
            b.jenisobatalkes_nama,
            b.servicegroup_id,
            b.servicecategory_id
           FROM jenisobatalkes_m b) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT b.servicegroup_id, 
            b.servicegroup_nama
           FROM servicegroup_m b) servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
     JOIN ( SELECT b.ruangan_id,
            b.instalasi_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaranpelayanan.penjualanresep_id,
            b.pembayaran_id,
            b.created_date AS tgl_pembayaran,
            pembayaranpelayanan.no_pembayaran
           FROM pembayaran_t b
             JOIN ( SELECT b1.pembayaran_id,
                    b1.penjualanresep_id,
                    b1.no_pembayaran
                   FROM pembayaranpelayanan_t b1) pembayaranpelayanan ON b.pembayaran_id = pembayaranpelayanan.pembayaran_id
          WHERE b.is_deleted = false) pembayaran ON obatalkespasien_r.penjualanresep_id = pembayaran.penjualanresep_id
     LEFT JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m b
             LEFT JOIN ( SELECT b1.spesialis_id,
                    b1.spesialis_nama
                   FROM spesialis_m b1) spesialis_m ON b.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT b.penjualanresep_id,
            b.pegawai_id,
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN b.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM penjualanresep_t b
             LEFT JOIN ( SELECT b1.penjualanresep_id,
                    b1.noresep
                   FROM reseptur_t b1) reseptur_t ON b.penjualanresep_id = reseptur_t.penjualanresep_id
          GROUP BY b.penjualanresep_id, b.pegawai_id, (
                CASE
                    WHEN reseptur_t.penjualanresep_id IS NULL THEN b.noresep
                    ELSE reseptur_t.noresep
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.id,
            b.pembayaran_id,
            b.is_sent
           FROM int_billing_r b
          WHERE b.is_sent = true) int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id
  WHERE (obatalkespasien_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221221_065726_migrate_hotfix_view_int_obatalkespasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221221_065726_migrate_hotfix_view_int_obatalkespasien_v cannot be reverted.\n";

        return false;
    }
    */
}
