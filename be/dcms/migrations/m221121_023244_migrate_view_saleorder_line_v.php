<?php

use yii\db\Migration;

/**
 * Class m221121_023244_migrate_view_saleorder_line_v
 */
class m221121_023244_migrate_view_saleorder_line_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            ALTER TABLE tindakanpelayanan_r ADD IF NOT EXISTS tarif_subpayer float8 ;
        ');

        $this->execute('
            DROP VIEW IF EXISTS saleorder_line_v;
        ');

        $this->execute('
            CREATE VIEW "public"."saleorder_line_v" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses, 
    concat(\'TND\', tindakanpelayanan_r.id) AS sync_id_api,
    concat(\'TND\', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    concat(daftartindakan_m.daftartindakan_nama, \'//\', kelaspelayanan_m.kelaspelayanan_nama, \'//\', ruangan_m.ruangan_nama, \'//\', kamarruangan_m.no_kamar) AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision) AS price_unit,
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_subtotal,
    (tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, 0::double precision)) * tindakanpelayanan_r.qty_tindakan::double precision AS price_total,
        CASE
            WHEN tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
            WHEN tindakanpelayanan_r.tarif_dibayarkan <> 0::double precision AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dibayarkan, 0::double precision)
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN tindakanpelayanan_r.is_penjaminutama = true THEN
            CASE
                WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::numeric::double precision) + COALESCE(tindakanpelayanan_r.tarif_diskon, 0::double precision)
                ELSE 0::double precision
            END
            WHEN tindakanpelayanan_r.is_penjaminutama IS NOT TRUE THEN
            CASE
                WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::numeric::double precision)
                ELSE 0::double precision
            END
            ELSE tindakanpelayanan_r.tarif_dijamin
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat(\'CATEG\', daftartindakan_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN konsulpoli_t.pegawai_id IS NOT NULL THEN concat(\'PEG\', konsulpoli_t.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN
            CASE
                WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
                ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            END
            ELSE NULL::text
        END AS prescribe_doc_id,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN \'LOS\'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = \'LOS\'::text THEN \'LOS\'::text
            ELSE \'LOB\'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN \'IPD\'::text
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
    \'-\'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN \'OUTPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN \'INPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 1 THEN \'OUTPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 2 THEN \'EMERGENCY\'::text
            WHEN ruangan_m.instalasi_id = 3 THEN \'INPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 21 THEN \'MCU\'::text
            WHEN ruangan_m.instalasi_id = 4 THEN \'LABORATORY\'::text
            WHEN ruangan_m.instalasi_id = 5 THEN \'RADIOLOGY\'::text
            ELSE \'OUTPATIENT\'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
        CASE
            WHEN cob.is_penjaminutama IS NULL THEN concat(\'PEN\', tindakanpelayanan_r.penjamin_id)
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
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
        CASE
            WHEN tindakanpelayanan.parent_id IS NULL THEN false
            ELSE true
        END AS is_package,
        CASE
            WHEN tindakanpelayanan.parent_id IS NULL THEN NULL::text
            ELSE concat(\'PKT\', paket.tipepaket_id)
        END AS package_id,
        CASE
            WHEN tindakanpelayanan.parent_id IS NULL THEN NULL::text::character varying
            ELSE paket.tipepaket_nama
        END AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NOT NULL THEN concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            ELSE concat(\'PEG\', pendaftaran_r.pegawai_id)
        END AS backup_analytic_id2,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NOT NULL THEN concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            ELSE concat(\'PEG\', pendaftaran_r.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id::character varying AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    \'-\'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    \'TINDAKAN\'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN \'SUKSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
        CASE
            WHEN tindakanpelayanan_r.is_penjaminutama = false AND tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN tindakanpelayanan_r.tarif_dijamin
            WHEN tindakanpelayanan_r.is_penjaminutama = false AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_dijamin + tindakanpelayanan_r.tarif_diskon
            ELSE tindakanpelayanan_r.tarif_subpayer
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
    concat(\'TND\', kelompoktindakan_m.kelompoktindakan_id) AS product_categ_id,
    tindakanpelayanan_r.cyto_tindakan AS cyto,
    tindakanpelayanan_r.penyulit_tindakan AS penyulit,
    NULL::text AS room_id,
    NULL::text AS bed_type_id,
    NULL::text AS eligible_bed_type_id,
    NULL::text AS alloted_bed_type_id,
    NULL::text AS bed_id,
    NULL::text AS bed_allocation_start,
    NULL::text AS bed_allocation_end,
    concat(\'REF\', pendaftaran_r.perujuk_id) AS referral_doc_id,
        CASE
            WHEN verifikasibedah_r.kode_posisi::text = \'508\'::text THEN concat(\'PEG\', verifikasibedah_r.dokter_id)
            ELSE NULL::text
        END AS surgery_operator_id,
    NULL::text AS surgery_operator_ass_id,
        CASE
            WHEN verifikasibedah_r.kode_posisi::text = \'509\'::text THEN concat(\'PEG\', verifikasibedah_r.dokter_id)
            ELSE NULL::text
        END AS anesthetist_operator_id,
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
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            a.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            a.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            a.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id,
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
                    a2.pasienadmisi_id,
                    a2.tgl_pendaftaran,
                    a2.instalasi_id,
                    a2.pegawai_id
                   FROM pendaftaran_t a2) pendaftaran_t ON a.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a3.pasienadmisi_id,
                    a3.ruangan_id,
                    a3.pegawai_id
                   FROM pasienadmisi_t a3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a4.ruangan_id,
                    a4.instalasi_id
                   FROM ruangan_m a4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT a5.kabupaten_id,
                    a5.kabupaten_nama
                   FROM kabupaten_m a5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a6.kecamatan_id,
                    a6.kecamatan_nama
                   FROM kecamatan_m a6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a7.kelurahan_id,
                    a7.kelurahan_nama
                   FROM kelurahan_m a7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a8.pasienpulang_id,
                    a8.tglpasienpulang
                   FROM pasienpulang_t a8) pasienpulang_t ON a.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a9.rujukan_id,
                    a9.no_rujukan,
                    a9.rujukandari_id AS perujuk_id
                   FROM rujukan_t a9) rujukan_t ON a.rujukan_id = rujukan_t.rujukan_id
          WHERE a.is_sent = true AND a.keterangan::text = \'INSERT\'::text) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT a.daftartindakan_id,
            a.servicegroup_id,
            a.servicecategory_id,
            a.kelompoktindakan_id,
            a.kategoritindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.servicegroup_id,
            a.servicegroup_nama
           FROM servicegroup_m a) servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN ( SELECT a.kategoritindakan_id,
            a.kategoritindakan_nama
           FROM kategoritindakan_m a) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN ( SELECT a.kelompoktindakan_id,
            a.kelompoktindakan_nama,
            a.kelompoktindakan_namalainnya
           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran ON tindakanpelayanan_r.pembayaran_id = pembayaran.pembayaran_id
     LEFT JOIN ( SELECT a.pegawai_id,
            spesialis_m.spesialis_nama,
            a.kelompokpegawai_id
           FROM pegawai_m a
             LEFT JOIN ( SELECT a1.spesialis_id,
                    a1.spesialis_nama
                   FROM spesialis_m a1) spesialis_m ON a.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.no_masukpenunjang
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tarif_dibayarkan,
            a.tarif_dijamin,
            a.tarif_diskon,
            a.parent_id
           FROM tindakanpelayanan_t a) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id,
            a.is_sent
           FROM int_billing_r a
          WHERE a.is_sent = true) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar AS no_kamar
           FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_r.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (a.tindakansudahbayar_id) a.tindakansudahbayar_id,
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
           FROM tindakansudahbayar_t a
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
                     JOIN carabayar_m a2 ON a1.carabayar_id = a2.carabayar_id) main_penjamin ON pembayaranpelayanan_r.main_penjamin_id = main_penjamin.penjamin_id) cob ON tindakanpelayanan_r.tindakansudahbayar_id = cob.tindakansudahbayar_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.daftartindakan_id,
            a.kode_posisi,
            a.dokter_id
           FROM verifikasibedah_r a) verifikasibedah_r ON tindakanpelayanan_r.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id AND tindakanpelayanan_r.daftartindakan_id = verifikasibedah_r.daftartindakan_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tindakanpelayanan_t a
             LEFT JOIN tipepaket_m ON a.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE a.is_deleted = false) paket ON tindakanpelayanan.parent_id = paket.tindakanpelayanan_id
     LEFT JOIN konsulpoli_t ON konsulpoli_t.konsulpoli_id = tindakanpelayanan_r.konsulpoli_id
  WHERE (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR (pendaftaran_r.instalasi_id = ANY (ARRAY[2, 3])) AND ((tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR NOT (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])))
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat(\'TND\', tindakanpelayanan_r.id) AS sync_id_api,
    concat(\'PKT\', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = \'ACCRUAL REVERSAL\'::text THEN tindakanpelayanan_r.qty_tindakan
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
    0 AS personal_amount,
    0 AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id::character varying AS order_id,
    concat(\'CATEG\', 10) AS service_categ_id,
        CASE
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            ELSE NULL::text
        END AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN
            CASE
                WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
                ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            END
            ELSE NULL::text
        END AS prescribe_doc_id,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS perform_doc_id,
    ruangan_m.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaran.no_pembayaran AS billno,
    pembayaran.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN \'LOS\'::text
            ELSE \'LOB\'::text
        END AS revenue_type,
    \'-\'::character varying AS item_specialisation,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN \'IPD\'::text
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
    \'-\'::text AS special_group,
        CASE
            WHEN ruangan_m.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN \'OUTPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN \'INPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 1 THEN \'OUTPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 2 THEN \'EMERGENCY\'::text
            WHEN ruangan_m.instalasi_id = 3 THEN \'INPATIENT\'::text
            WHEN ruangan_m.instalasi_id = 21 THEN \'MCU\'::text
            WHEN ruangan_m.instalasi_id = 4 THEN \'LABORATORY\'::text
            WHEN ruangan_m.instalasi_id = 5 THEN \'RADIOLOGY\'::text
            ELSE \'OUTPATIENT\'::text
        END AS special_group2,
    \'others\'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat(\'PEN\', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    true AS is_package,
    NULL::text AS package_id,
    tipepaket_m.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            ELSE concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NOT NULL THEN concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            ELSE concat(\'PEG\', pendaftaran_r.pegawai_id)
        END AS backup_analytic_id2,
        CASE
            WHEN pegawai.kelompokpegawai_id <> 1 THEN concat(\'PEG\', pendaftaran_r.pegawai_id)
            WHEN tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN concat(\'PEG\', pendaftaran_r.pegadmisi_id)
            WHEN tindakanpelayanan_r.dokterpenanggungjawab_id IS NOT NULL THEN concat(\'PEG\', tindakanpelayanan_r.dokterpenanggungjawab_id)
            ELSE concat(\'PEG\', pendaftaran_r.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id::character varying AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    \'-\'::text AS manufacture,
    pendaftaran_r.tglpasienpulang::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    \'PAKET\'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
    NULL::json AS additional_paket,
    pendaftaran_r.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN \'SUKSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            ELSE NULL::text
        END AS status_proses,
    pendaftaran_r.tgl_pendaftaran AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
        CASE
            WHEN tindakanpelayanan_r.is_penjaminutama = false AND tindakanpelayanan_r.tarif_dijamin = 0::double precision THEN tindakanpelayanan_r.tarif_dijamin
            WHEN tindakanpelayanan_r.is_penjaminutama = false AND tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN tindakanpelayanan_r.tarif_dijamin + tindakanpelayanan_r.tarif_diskon
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
    pendaftaran_r.no_pendaftaran AS ref_admission_no,
    pendaftaran_r.no_rujukan AS referral_number,
    NULL::text AS product_categ_id,
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
   FROM tindakanpelayanan_r
     JOIN ( SELECT DISTINCT ON (b.pendaftaran_id) b.id,
            b.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            b.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            b.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            b.is_aps,
            pendaftaran_t.tgl_pendaftaran,
            pasienadmisi_t.pegawai_id AS pegadmisi_id,
            pendaftaran_t.pasienadmisi_id,
            rujukan_t.no_rujukan,
            rujukan_t.perujuk_id
           FROM pendaftaran_r b
             JOIN ( SELECT b1.pasien_id,
                    b1.no_rekam_medik,
                    b1.nama_pasien,
                    b1.kabupaten_id,
                    b1.kecamatan_id,
                    b1.kelurahan_id
                   FROM pasien_m b1) pasien_m ON b.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT b2.pendaftaran_id,
                    b2.pasienadmisi_id,
                    b2.tgl_pendaftaran,
                    b2.instalasi_id,
                    b2.pegawai_id
                   FROM pendaftaran_t b2) pendaftaran_t ON b.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT b3.pasienadmisi_id,
                    b3.ruangan_id,
                    b3.pegawai_id
                   FROM pasienadmisi_t b3) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT b4.ruangan_id,
                    b4.instalasi_id
                   FROM ruangan_m b4) ruangan_m_1 ON pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id
             LEFT JOIN ( SELECT b5.kabupaten_id,
                    b5.kabupaten_nama
                   FROM kabupaten_m b5) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT b6.kecamatan_id,
                    b6.kecamatan_nama
                   FROM kecamatan_m b6) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT b7.kelurahan_id,
                    b7.kelurahan_nama
                   FROM kelurahan_m b7) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT b8.pasienpulang_id,
                    b8.tglpasienpulang
                   FROM pasienpulang_t b8) pasienpulang_t ON b.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT b9.rujukan_id,
                    b9.no_rujukan,
                    b9.rujukandari_id AS perujuk_id
                   FROM rujukan_t b9) rujukan_t ON b.rujukan_id = rujukan_t.rujukan_id
          WHERE b.is_sent = true) pendaftaran_r ON tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id
     JOIN ( SELECT b.tipepaket_id,
            b.tipepaket_nama
           FROM tipepaket_m b) tipepaket_m ON tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT b.ruangan_id,
            b.instalasi_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT b.penjamin_id,
            b.carabayar_id,
            b.penjamin_kode,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.pendaftaran_id,
            pembayaranpelayanan_t.no_pembayaran,
            b.created_date AS tgl_pembayaran
           FROM pembayaran_t b
             JOIN pembayaranpelayanan_t ON b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
          WHERE b.is_deleted = false) pembayaran ON tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT b.pegawai_id,
            spesialis_m.spesialis_nama,
            b.kelompokpegawai_id
           FROM pegawai_m b
             LEFT JOIN ( SELECT b1.spesialis_id,
                    b1.spesialis_nama
                   FROM spesialis_m b1) spesialis_m ON b.spesialis_id = spesialis_m.spesialis_id) pegawai ON tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT b.tindakanpelayanan_id,
            b.tarif_dibayarkan,
            b.tarif_dijamin,
            b.tarif_diskon
           FROM tindakanpelayanan_t b) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT b.pasienmasukpenunjang_id,
            b.no_masukpenunjang
           FROM pasienmasukpenunjang_t b) pasienmasukpenunjang_t ON tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT b.pembayaran_id,
            b.id,
            b.is_sent
           FROM int_billing_r b
          WHERE b.is_sent = true) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
  WHERE (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR (pendaftaran_r.instalasi_id = ANY (ARRAY[2, 3])) AND ((tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR NOT (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])))
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat(\'TND\', tindakanpelayanan_r.id) AS sync_id_api,
    concat(\'TND\', tindakanpelayanan_r.daftartindakan_id) AS product_id,
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
            WHEN tindakanpelayanan_r.is_penjaminutama = true THEN
            CASE
                WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(round(tindakanpelayanan_r.tarif_dijamin::integer::numeric, 2), 0::numeric)::double precision + COALESCE(round(tindakanpelayanan_r.tarif_diskon::integer::numeric, 2)::double precision, 0::double precision)
                ELSE 0::double precision
            END
            WHEN tindakanpelayanan_r.is_penjaminutama IS NOT TRUE THEN
            CASE
                WHEN tindakanpelayanan_r.tarif_dijamin <> 0::double precision THEN COALESCE(tindakanpelayanan_r.tarif_dijamin, 0::numeric::double precision)
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS payer_amount,
    concat(\'RSPB\', penjualanresep.penjualanresep_id) AS order_id,
    concat(\'CATEG\', daftartindakan_m.servicecategory_id) AS service_categ_id,
    concat(\'PEG\', penjualanresep.pegawairesep_id) AS primary_doc_id,
        CASE
            WHEN tindakanpelayanan_r.pasienmasukpenunjang_id IS NULL THEN NULL::text
            ELSE concat(\'PEG\', penjualanresep.pegawairesep_id)
        END AS prescribe_doc_id,
    concat(\'PEG\', penjualanresep.pegawairesep_id) AS perform_doc_id,
    penjualanresep.ruangan_id::text AS location_id,
    penjualanresep.ruangan_nama AS department_id,
    penjualanresep.no_pembayaran AS billno,
    penjualanresep.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
    \'LOS\'::text AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
    \'OPD\'::text AS patient_group,
    \'-\'::text AS special_group,
        CASE
            WHEN tindakanpelayanan_r.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NULL THEN \'OUTPATIENT\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 12 AND tindakanpelayanan_r.pasienadmisi_id IS NOT NULL THEN \'INPATIENT\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 1 THEN \'OUTPATIENT\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 2 THEN \'EMERGENCY\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 3 THEN \'INPATIENT\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 21 THEN \'MCU\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 4 THEN \'LABORATORY\'::text
            WHEN tindakanpelayanan_r.instalasi_id = 5 THEN \'RADIOLOGY\'::text
            ELSE \'OUTPATIENT\'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    NULL::character varying AS bed_type,
    concat(\'PEN\', tindakanpelayanan_r.penjamin_id) AS payer,
    penjualanresep.penjamin_kode AS payer_code,
    penjualanresep.carabayar_nama AS payer_type,
    penjualanresep.penjamin_nama AS payer_name,
    penjualanresep.noresep AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_id,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
    concat(\'PEG\', penjualanresep.pegawairesep_id) AS account_analytic_id,
    concat(\'PEG\', penjualanresep.pegawairesep_id) AS backup_analytic_id2,
    concat(\'PEG\', penjualanresep.pegawairesep_id) AS backup_analytic_id,
    NULL::character varying AS kota,
    NULL::character varying AS kecamatan,
    NULL::character varying AS kelurahan,
    penjualanresep.partner_id,
    penjualanresep.no_rekam_medik AS registration_code,
    penjualanresep.noresep AS number_admission,
    \'-\'::text AS manufacture,
    penjualanresep.tglresep::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    \'TINDAKAN\'::text AS jenis,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN tindakanpelayanan_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
    NULL::json AS additional_paket,
    penjualanresep.nama_pasien,
        CASE
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = true THEN \'SUKSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = true AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN tindakanpelayanan_r.is_sending = false AND tindakanpelayanan_r.is_sent = false AND tindakanpelayanan_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            ELSE NULL::text
        END AS status_proses,
    penjualanresep.tglresep AS admit_date,
    int_billing_r.id::text AS billing_id,
    int_billing_r.is_sent AS is_sent_billing,
    0 AS subpayer_amount,
    NULL::text AS cob_id,
    NULL::text AS sub_cob_id,
    NULL::boolean AS is_sent_cob,
    NULL::boolean AS is_sent_subcob,
    NULL::text AS sub_payer,
    NULL::text AS sub_payer_code,
    NULL::text AS sub_payer_type,
    NULL::text AS discharge_reason,
    NULL::text AS admission_type,
    NULL::character varying AS ref_admission_no,
    NULL::text AS referral_number,
    NULL::text AS product_categ_id,
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
   FROM tindakanpelayanan_r
     JOIN ( SELECT pembayaran.pembayaran_id,
            c.pegawairesep_id,
            c.noresep,
            pasien_m.no_rekam_medik,
            c.tglresep,
                CASE
                    WHEN c.jenispenjualan::text = \'345\'::text THEN karyawan.nama_pegawai
                    ELSE c.nama_pembeli
                END AS nama_pasien,
            penjamin_m.penjamin_nama,
            penjamin_m.penjamin_kode,
            carabayar_m.carabayar_nama,
            pembayaran.no_pembayaran,
            c.penjualanresep_id,
            c.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.tgl_pembayaran,
                CASE
                    WHEN c.jenispenjualan::text = \'343\'::text THEN 0::character varying
                    ELSE concat(\'PEG\', c.karyawan_id)::character varying
                END AS partner_id
           FROM penjualanresep_r c
             JOIN ( SELECT c1.pasien_id,
                    c1.no_rekam_medik,
                    c1.nama_pasien
                   FROM pasien_m c1) pasien_m ON 0 = pasien_m.pasien_id
             JOIN ( SELECT c2.ruangan_id,
                    c2.ruangan_nama
                   FROM ruangan_m c2) ruangan_m ON c.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT pembayaranpelayanan_t.penjualanresep_id,
                    pembayaranpelayanan_t.pembayaran_id,
                    pembayaranpelayanan_t.tgl_pembayaran,
                    pembayaranpelayanan_t.no_pembayaran
                   FROM pembayaranpelayanan_t) pembayaran ON c.penjualanresep_id = pembayaran.penjualanresep_id
             JOIN ( SELECT c4.penjamin_id,
                    c4.carabayar_id,
                    c4.penjamin_nama,
                    c4.penjamin_kode
                   FROM penjamin_m c4) penjamin_m ON c.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT c5.carabayar_id,
                    c5.carabayar_nama
                   FROM carabayar_m c5) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT c6.pegawai_id,
                    c6.nama_pegawai
                   FROM pegawai_m c6) karyawan ON c.karyawan_id = karyawan.pegawai_id
          WHERE (c.jenispenjualan::text = ANY (ARRAY[\'343\'::character varying::text, \'345\'::character varying::text])) AND c.keterangan::text = \'INSERT\'::text AND c.is_sent = true) penjualanresep ON tindakanpelayanan_r.pembayaran_id = penjualanresep.pembayaran_id
     JOIN ( SELECT c.daftartindakan_id,
            c.servicegroup_id,
            c.servicecategory_id,
            c.kelompoktindakan_id,
            c.kategoritindakan_id,
            c.daftartindakan_nama
           FROM daftartindakan_m c) daftartindakan_m ON tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT c.servicegroup_id,
            c.servicegroup_nama
           FROM servicegroup_m c) servicegroup_m ON daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id
     LEFT JOIN ( SELECT c.kategoritindakan_id,
            c.kategoritindakan_nama
           FROM kategoritindakan_m c) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN ( SELECT c.kelompoktindakan_id,
            c.kelompoktindakan_nama,
            c.kelompoktindakan_namalainnya
           FROM kelompoktindakan_m c) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT c.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m c
             LEFT JOIN ( SELECT c1.spesialis_id,
                    c1.spesialis_nama
                   FROM spesialis_m c1) spesialis_m ON c.spesialis_id = spesialis_m.spesialis_id) pegawai ON penjualanresep.pegawairesep_id = pegawai.pegawai_id
     LEFT JOIN ( SELECT c.tindakanpelayanan_id,
            c.tarif_dibayarkan,
            c.tarif_dijamin,
            c.tarif_diskon
           FROM tindakanpelayanan_t c) tindakanpelayanan ON tindakanpelayanan_r.tindakanpelayanan_id = tindakanpelayanan.tindakanpelayanan_id
     LEFT JOIN ( SELECT c.pembayaran_id,
            c.id,
            c.is_sent
           FROM int_billing_r c
          WHERE c.is_sent = true) int_billing_r ON tindakanpelayanan_r.pembayaran_id = int_billing_r.pembayaran_id
  WHERE (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) AND int_billing_r.id IS NOT NULL OR NOT (tindakanpelayanan_r.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text]));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221121_023244_migrate_view_saleorder_line_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221121_023244_migrate_view_saleorder_line_v cannot be reverted.\n";

        return false;
    }
    */
}
