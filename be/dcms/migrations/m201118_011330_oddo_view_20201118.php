<?php

use yii\db\Migration;

/**
 * Class m201118_011330_oddo_view_20201118
 */
class m201118_011330_oddo_view_20201118 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_pegawai_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_pegawai_v\" AS  SELECT concat('PEG', pegawai_m.pegawai_id) AS sync_id_api,
    '-'::text AS parent_doctor_id,
    pegawai_m.nomorindukpegawai AS doctor_code,
    pegawai_m.nama_pegawai AS name,
    pegawai_m.nama_pegawai AS display_name,
    spesialis_m.spesialis_nama AS spec,
    '-'::text AS department,
    'internal'::text AS type_employment,
    'internal'::text AS type_doctor,
    '-'::text AS designation,
    kelompokpegawai_m.kelompokpegawai_nama AS hrprofile,
    lower((fgetnamalookup((pegawai_m.jeniskelamin)::integer))::text) AS gender,
    pegawai_m.notelp_pegawai AS phone,
    pegawai_m.nomobile_pegawai AS mobile,
    '-'::text AS fax,
    pegawai_m.alamatemail AS email,
    '-'::text AS website,
    pegawai_m.alamat_pegawai AS street,
    pegawai_m.alamat_pegawai AS street2,
    pegawai_m.alamat_pegawai AS street3,
    kabupaten_m.kabupaten_nama AS city,
    '-'::text AS zip,
    spesialis_m.spesialis_nama AS specialise_api_id,
    pegawai_m.is_active AS wipro_block,
    pegawai_m.is_active AS empblocked,
    'TRUE'::text AS doctor,
    pegawai_m.is_active AS active,
    6 AS sync_type
   FROM ((((pegawai_m
     LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
     LEFT JOIN kabupaten_m ON ((pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
  WHERE (pegawai_m.is_deleted = false);");

        $this->execute('ALTER TABLE "public"."int_pegawai_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."saleorder_line_v";');

        $this->execute("
            CREATE VIEW \"public\".\"saleorder_line_v\" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    ((tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, (0)::double precision)) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, (0)::double precision)) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE (- tindakanpelayanan_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dijamin
            ELSE (- tindakanpelayanan_r.tarif_dijamin)
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
    (daftartindakan_m.servicecategory_id)::character(1) AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    (ruangan_m.ruangan_id)::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN (pendaftaran_r.is_aps = true) THEN 'LOS'::text
            WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOS'::text) THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OUTPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'INPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 4) THEN 'LABORATORY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 5) THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    lower(servicegroup_m.servicegroup_nama) AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
        CASE
            WHEN (tindakanpelayanan_r.pasienmasukpenunjang_id IS NOT NULL) THEN pasienmasukpenunjang_t.no_masukpenunjang
            ELSE tindakanpelayanan_r.no_tindakanpelayanan
        END AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    NULL::json AS additional_paket
   FROM ((((((((((((((((tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN (pendaftaran_r_1.pasienadmisi_id IS NULL) THEN pendaftaran_r_1.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps
           FROM ((((((((pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON ((pendaftaran_r_1.id = max.id)))
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_r_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (pendaftaran_r_1.is_sent = true)) pendaftaran_r ON ((tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN servicegroup_m ON ((daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id)))
     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN penjamin_m ON ((tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE (tindakanpelayanan_r_1.is_deleted = false)
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON ((tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON ((tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id)))
     LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'ACCRUAL REVERSAL'::text) THEN (- tindakanpelayanan_r.qty_tindakan)
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE (- tindakanpelayanan_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dijamin
            ELSE (- tindakanpelayanan_r.tarif_dijamin)
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
    'OP/IP PACKAGE'::bpchar AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    (ruangan_m.ruangan_id)::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN (pendaftaran_r.is_aps = true) THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OUTPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'INPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 4) THEN 'LABORATORY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 5) THEN 'RADIOLOGY'::text
            ELSE 'OUTPATIENT'::text
        END AS special_group2,
    'others'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    tindakanpelayanan_r.no_tindakanpelayanan AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    true AS is_package,
    tipepaket_m.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'PAKET'::text AS jenis,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS state,
    ( SELECT array_to_json(array_agg(row_to_json(detail_paket.*))) AS array_to_json
           FROM ( SELECT 6 AS sync_type,
                    (tp_paket.tgl_proses)::date AS tglproses,
                    concat('TND', tp_paket.id, '-', concat('TND', paketpelayanan_mp.daftartindakan_id)) AS sync_id_api,
                    concat('TND', paketpelayanan_mp.daftartindakan_id) AS product_id,
                    daftartindakan_m.daftartindakan_nama AS name,
                    351 AS product_uom,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'ACCRUAL REVERSAL'::text) THEN (- tp_paket.qty_tindakan)
                            WHEN ((tp_paket.keterangan)::text = 'BILLING CANCEL'::text) THEN (- tp_paket.qty_tindakan)
                            ELSE tp_paket.qty_tindakan
                        END AS product_uom_qty,
                    ((paket_detail.harga_satuan + paket_detail.harga_cyto) + paket_detail.harga_penyulit) AS price_unit,
                    paket_detail.harga_total AS price_total,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'BILLING'::text) THEN tp_paket.tarif_dibayarkan
                            ELSE (- tp_paket.tarif_dibayarkan)
                        END AS personal_amount,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'BILLING'::text) THEN tp_paket.tarif_dijamin
                            ELSE (- tp_paket.tarif_dijamin)
                        END AS payer_amount,
                    tp_paket.pendaftaran_id AS order_id,
                    daftartindakan_m.servicecategory_id AS service_categ_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS primary_doc_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS prescribe_doc_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS perform_doc_id,
                    (ruangan_m_1.ruangan_id)::text AS location_id,
                    ruangan_m_1.ruangan_nama AS department_id,
                    COALESCE(pembayaranpelayanan_t_1.no_pembayaran, no_pembayaran_1.no_pembayaran) AS billno,
                    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
                    tp_paket.keterangan AS type_line,
                        CASE
                            WHEN (pendaftaran_r_1.is_aps = true) THEN 'LOS'::text
                            WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOS'::text) THEN 'LOS'::text
                            ELSE 'LOB'::text
                        END AS revenue_type,
                    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
                        CASE
                            WHEN (pendaftaran_r_1.instalasi_id = 1) THEN 'OPD'::text
                            WHEN (pendaftaran_r_1.instalasi_id = 2) THEN 'EMERGENCY'::text
                            WHEN (pendaftaran_r_1.instalasi_id = 3) THEN 'IPD'::text
                            WHEN (tp_paket.instalasi_id = 21) THEN 'MCU'::text
                            ELSE 'OPD'::text
                        END AS patient_group,
                    '-'::text AS special_group,
                        CASE
                            WHEN (tp_paket.instalasi_id = 1) THEN 'OUTPATIENT'::text
                            WHEN (tp_paket.instalasi_id = 2) THEN 'EMERGENCY'::text
                            WHEN (tp_paket.instalasi_id = 3) THEN 'INPATIENT'::text
                            WHEN (tp_paket.instalasi_id = 21) THEN 'MCU'::text
                            WHEN (tp_paket.instalasi_id = 4) THEN 'LABORATORY'::text
                            WHEN (tp_paket.instalasi_id = 5) THEN 'RADIOLOGY'::text
                            ELSE 'OUTPATIENT'::text
                        END AS special_group2,
                    lower(servicegroup_m.servicegroup_nama) AS service_group,
                    kelaspelayanan_m_1.kelaspelayanan_nama AS bed_type,
                    concat('PEN', tp_paket.penjamin_id) AS payer,
                    penjamin_m_1.penjamin_kode AS payer_code,
                    carabayar_m_1.carabayar_nama AS payer_type,
                    penjamin_m_1.penjamin_nama AS payer_name,
                        CASE
                            WHEN (tp_paket.pasienmasukpenunjang_id IS NOT NULL) THEN pasienmasukpenunjang_t.no_masukpenunjang
                            ELSE tp_paket.no_tindakanpelayanan
                        END AS order_no,
                    (tp_paket.tgl_tindakan)::date AS order_date,
                    true AS is_package,
                    tipepaket_m_1.tipepaket_nama AS package_name,
                    NULL::text AS cost_unit,
                    NULL::text AS cost_total,
                        CASE
                            WHEN (tp_paket.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r_1.pegawai_id)
                            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
                        END AS account_analytic_id,
                        CASE
                            WHEN (tp_paket.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r_1.pegawai_id)
                            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
                        END AS backup_analytic_id,
                    pendaftaran_r_1.kota,
                    pendaftaran_r_1.kecamatan,
                    pendaftaran_r_1.kelurahan,
                    pendaftaran_r_1.pasien_id AS partner_id,
                    pendaftaran_r_1.no_rekam_medik AS registration_code,
                    pendaftaran_r_1.no_pendaftaran AS number_admission,
                    '-'::text AS manufacture,
                    (pendaftaran_r_1.tglpasienpulang)::date AS discharge_date,
                    pegawai_1.spesialis_nama AS specialization_primary,
                    tp_paket.is_sent,
                    tp_paket.is_sending,
                    tp_paket.id,
                    'PAKET_DETAIL'::text AS jenis,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = ANY (ARRAY[('ACCRUAL REVERSAL'::character varying)::text, ('ACCRUAL'::character varying)::text])) THEN 'draft'::text
                            ELSE 'done'::text
                        END AS status_bill,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = ANY (ARRAY[('ACCRUAL REVERSAL'::character varying)::text, ('ACCRUAL'::character varying)::text])) THEN 'draft'::text
                            ELSE 'done'::text
                        END AS state
                   FROM (((((((((((((((((((tindakanpelayanan_r tp_paket
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
                                    WHEN (pen_det.pasienadmisi_id IS NULL) THEN pen_det.instalasi_id
                                    ELSE ruangan_m_2.instalasi_id
                                END AS instalasi_id,
                            pen_det.is_aps
                           FROM ((((((((pendaftaran_r pen_det
                             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                                    pendaftaran_r_2.pendaftaran_id
                                   FROM pendaftaran_r pendaftaran_r_2
                                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON ((pen_det.id = max.id)))
                             JOIN pasien_m ON ((pen_det.pasien_id = pasien_m.pasien_id)))
                             LEFT JOIN pasienadmisi_t ON ((pen_det.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                             LEFT JOIN ruangan_m ruangan_m_2 ON ((pasienadmisi_t.ruangan_id = ruangan_m_2.ruangan_id)))
                             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
                             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
                             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
                             LEFT JOIN pasienpulang_t ON ((pen_det.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
                          WHERE (pen_det.is_sent = true)) pendaftaran_r_1 ON ((tp_paket.pendaftaran_id = pendaftaran_r_1.pendaftaran_id)))
                     JOIN tipepaket_m tipepaket_m_1 ON ((tp_paket.tipepaket_id = tipepaket_m_1.tipepaket_id)))
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     LEFT JOIN servicegroup_m ON ((daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id)))
                     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
                     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                     JOIN ruangan_m ruangan_m_1 ON ((tp_paket.ruangan_id = ruangan_m_1.ruangan_id)))
                     JOIN penjamin_m penjamin_m_1 ON ((tp_paket.penjamin_id = penjamin_m_1.penjamin_id)))
                     JOIN carabayar_m carabayar_m_1 ON ((penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id)))
                     LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON ((tp_paket.kelaspelayanan_id = kelaspelayanan_m_1.kelaspelayanan_id)))
                     LEFT JOIN ( SELECT tp_paket_1.pendaftaran_id,
                            sum(tp_paket_1.tarif_tindakan) AS total_tagihan
                           FROM tindakanpelayanan_r tp_paket_1
                          WHERE (tp_paket_1.is_deleted = false)
                          GROUP BY tp_paket_1.pendaftaran_id) total_tagihan_1 ON ((tp_paket.pendaftaran_id = total_tagihan_1.pendaftaran_id)))
                     LEFT JOIN tindakansudahbayar_t tindakansudahbayar_t_1 ON ((tp_paket.tindakansudahbayar_id = tindakansudahbayar_t_1.tindakansudahbayar_id)))
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((tindakansudahbayar_t_1.pembayaranpelayanan_id = pembayaranpelayanan_t_1.pembayaranpelayanan_id)))
                     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
                            sum(pembayaran_t.total_dibayar) AS total_dibayar,
                            sum(pembayaran_t.total_dijamin) AS total_dijamin
                           FROM pembayaran_t
                          GROUP BY pembayaran_t.pendaftaran_id) pembayaran_1 ON ((tp_paket.pendaftaran_id = pembayaran_1.pendaftaran_id)))
                     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'daftartindakan_id'::text))::integer AS daftartindakan_id,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_satuan'::text))::double precision AS harga_satuan,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_cyto'::text))::double precision AS harga_cyto,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_penyulit'::text))::double precision AS harga_penyulit,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_total'::text))::double precision AS harga_total
                           FROM tindakanpelayanan_t) paket_detail ON (((tp_paket.tindakanpelayanan_id = paket_detail.tindakanpelayanan_id) AND (daftartindakan_m.daftartindakan_id = paket_detail.daftartindakan_id))))
                     LEFT JOIN ( SELECT pembayaranpelayanan_t_1_1.pembayaran_id,
                            pembayaranpelayanan_t_1_1.no_pembayaran
                           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1_1) no_pembayaran_1 ON ((tp_paket.pembayaran_id = no_pembayaran_1.pembayaran_id)))
                     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                            spesialis_m.spesialis_nama
                           FROM (pegawai_m
                             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai_1 ON ((tp_paket.dokterpenanggungjawab_id = pegawai_1.pegawai_id)))
                     LEFT JOIN pasienmasukpenunjang_t ON ((tp_paket.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                  WHERE (tp_paket.id = tindakanpelayanan_r.id)) detail_paket) AS additional_paket
   FROM ((((((((((((tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang,
                CASE
                    WHEN (pendaftaran_r_1.pasienadmisi_id IS NULL) THEN pendaftaran_r_1.instalasi_id
                    ELSE ruangan_m_1.instalasi_id
                END AS instalasi_id,
            pendaftaran_r_1.is_aps
           FROM ((((((((pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON ((pendaftaran_r_1.id = max.id)))
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_r_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienadmisi_t.ruangan_id = ruangan_m_1.ruangan_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (pendaftaran_r_1.is_sent = true)) pendaftaran_r ON ((tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN penjamin_m ON ((tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE (tindakanpelayanan_r_1.is_deleted = false)
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON ((tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON ((tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id)));");

        $this->execute('ALTER TABLE "public"."saleorder_line_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_obatalkespasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dibayarkan
            ELSE (- obatalkespasien_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dijamin
            ELSE (- obatalkespasien_r.tarif_dijamin)
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
    jenisobatalkes_m.servicecategory_id AS service_categ_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS primary_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS prescribe_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS perform_doc_id,
    (obatalkespasien_r.ruangan_id)::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (pendaftaran_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE 'OPD'::text
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN 'PHARMACY OUTPATIENT'::text
            ELSE 'PHARMACY INPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    COALESCE(penjualanresep_t.no_resep, pendaftaran_r.no_pendaftaran) AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (obatalkespasien_r.penjualanresep_id IS NULL) THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN (obatalkespasien_r.penjualanresep_id IS NULL) THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS status_bill,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'done'::text
        END AS state
   FROM (((((((((((((((obatalkespasien_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasienadmisi_id,
            pendaftaran_r_1.instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM ((((((pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON ((pendaftaran_r_1.id = max.id)))
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (pendaftaran_r_1.is_sent = true)) pendaftaran_r ON ((obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN servicegroup_m ON ((jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id)))
     JOIN ruangan_m ON ((obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE (obatalkespasien_r_1.is_deleted = false)
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON ((obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN kelaspelayanan_m ON ((obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((obatalkespasien_r.pegawai_id = pegawai.pegawai_id)))
     LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
            penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN (reseptur_t.penjualanresep_id IS NULL) THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END AS no_resep
           FROM (penjualanresep_t penjualanresep_t_1
             LEFT JOIN reseptur_t ON ((penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id)))
          GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id,
                CASE
                    WHEN (reseptur_t.penjualanresep_id IS NULL) THEN penjualanresep_t_1.noresep
                    ELSE reseptur_t.noresep
                END) penjualanresep_t ON ((obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id)));");
        
        $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_purchasegrndetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_purchasegrndetail_v\" AS  SELECT concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    returpenerimaanobat_r.no_returpenerimaanobat AS order_no,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.harganetto AS normal_price,
    (penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) AS price_unit,
    ((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * ((penerimaansuppdetail_t.diskon / (100)::numeric))::double precision) AS discount_value,
    penerimaansuppdetail_t.diskon AS discount_persen,
    penerimaansuppdetail_t.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN (\"left\"((obatalkes_m.obatalkes_kode)::text, 3) = 'CGN'::text) THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
    penerimaansupp_t.pajak_id AS taxes_id,
    (((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) AS price_tax,
    (((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) AS has_tax,
    ((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) - ((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) + (((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) AS price_total,
    ((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) - ((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) + (((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_r.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    returpenerimaanobat_r.tgl_retur AS order_date,
    13 AS currency_id,
    penerimaansuppdetail_t.no_batch AS batch_id,
    'draft'::text AS state,
    6 AS sync_type,
    returpenerimaanobatdetail_r.id,
    returpenerimaanobatdetail_r.is_sent,
    returpenerimaanobatdetail_r.is_sending
   FROM (((((returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON (((returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id) AND (returpenerimaanobat_r.is_sent = true))))
     JOIN penerimaansupp_t ON ((returpenerimaanobat_r.panerimaanobatsupp_id = penerimaansupp_t.penerimaansupp_id)))
     JOIN penerimaansuppdetail_t ON ((returpenerimaanobatdetail_r.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id)))
     JOIN obatalkes_m ON ((returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN pajak_m ON ((penerimaansupp_t.pajak_id = pajak_m.pajak_id)))
UNION ALL
 SELECT concat('RPOM', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanobat_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    returpenerimaanobat_r.no_returpenerimaanobat AS order_no,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.harganetto AS normal_price,
    penerimaanobatdetail.harga_konversi AS price_unit,
    ((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision) AS discount_value,
    penerimaanobatdetail.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN (\"left\"((obatalkes_m.obatalkes_kode)::text, 3) = 'CGN'::text) THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
    po.pajak_id AS taxes_id,
    ((((penerimaanobatdetail.harga_konversi - ((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) AS price_tax,
    ((((penerimaanobatdetail.harga_konversi - ((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision) AS has_tax,
    (((penerimaanobatdetail.harga_konversi * (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) + ((((penerimaanobatdetail.harga_konversi - ((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) AS price_total,
    (((penerimaanobatdetail.harga_konversi * (returpenerimaanobatdetail_r.qty_retur)::double precision) - (((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) + ((((penerimaanobatdetail.harga_konversi - ((penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_r.qty_retur)::double precision)) AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    returpenerimaanobat_r.tgl_retur AS order_date,
    13 AS currency_id,
    penerimaanobatdetail.no_batch AS batch_id,
    'draft'::text AS state,
    6 AS sync_type,
    returpenerimaanobatdetail_r.id,
    returpenerimaanobatdetail_r.is_sent,
    returpenerimaanobatdetail_r.is_sending
   FROM ((((((returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON (((returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id) AND (returpenerimaanobat_r.is_sent = true))))
     LEFT JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
            ((penerimaanobatdetail_t.qty_diterima)::double precision * satuankonversi_m.nilai_konversi) AS qty_konversi,
            (penerimaanobatdetail_t.harga / satuankonversi_m.nilai_konversi) AS harga_konversi,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.discount
           FROM (penerimaanobatdetail_t
             JOIN satuankonversi_m ON ((penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
          WHERE (satuankonversi_m.is_deleted = false)) penerimaanobatdetail ON ((returpenerimaanobatdetail_r.penerimaanobatdetail_id = penerimaanobatdetail.penerimaanobatdetail_id)))
     JOIN penerimaanobat_t ON ((returpenerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id)))
     JOIN obatalkes_m ON ((returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
            validasipoobat_t.pajak_id,
            pajak_m.pajak_persen
           FROM (validasipoobat_t
             JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
          WHERE (validasipoobat_t.is_deleted = false)) po ON ((penerimaanobat_t.validasipoobat_id = po.validasipoobat_id)))
     LEFT JOIN ( SELECT satuankonversi_m.satuankonversi_id,
            satuankonversi_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM (satuankonversi_m
             JOIN satuanunit_m satuan_kecil ON ((satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE (satuankonversi_m.is_deleted = false)) satuan ON ((penerimaanobatdetail.s_konversiobt_id = satuan.satuankonversi_id)));");

        $this->execute('ALTER TABLE "public"."int_purchasegrndetail_v" OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201118_011330_oddo_view_20201118 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201118_011330_oddo_view_20201118 cannot be reverted.\n";

        return false;
    }
    */
}
