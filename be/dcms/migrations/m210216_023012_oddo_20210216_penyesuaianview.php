<?php

use yii\db\Migration;

/**
 * Class m210216_023012_oddo_20210216_penyesuaianview
 */
class m210216_023012_oddo_20210216_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    obatalkespasien_r.hargajual_oa AS price_total,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dibayarkan
            ELSE - obatalkespasien_r.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dijamin
            ELSE - obatalkespasien_r.tarif_dijamin
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS perform_doc_id,
    obatalkespasien_r.ruangan_id::text AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r.instalasi_id = 1 THEN 'OPD'::text
            WHEN pendaftaran_r.instalasi_id = 2 THEN 'EMERGENCY'::text
            WHEN pendaftaran_r.instalasi_id = 3 THEN 'IPD'::text
            WHEN pendaftaran_r.instalasi_id = 21 THEN 'MCU'::text
            ELSE 'OPD'::text
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
    COALESCE(penjualanresep_t.no_resep, pendaftaran_r.no_pendaftaran) AS order_no,
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
    pendaftaran_r.nama_pasien
   FROM obatalkespasien_r
     JOIN ( SELECT pendaftaran_r_1.id,
            pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasienadmisi_id,
            pendaftaran_r_1.instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM pendaftaran_r pendaftaran_r_1
             JOIN ( SELECT max(pendaftaran_r_2.id) AS id,
                    pendaftaran_r_2.pendaftaran_id
                   FROM pendaftaran_r pendaftaran_r_2
                  WHERE pendaftaran_r_2.keterangan::text = 'INSERT'::text
                  GROUP BY pendaftaran_r_2.pendaftaran_id) max ON pendaftaran_r_1.id = max.id
             JOIN pasien_m ON pendaftaran_r_1.pasien_id = pasien_m.pasien_id
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
UNION ALL
 SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.satuankecil_id AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    obatalkespasien_r.hargajual_oa AS price_total,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dibayarkan
            ELSE - obatalkespasien_r.tarif_dibayarkan
        END AS personal_amount,
        CASE
            WHEN obatalkespasien_r.keterangan::text = 'BILLING'::text THEN obatalkespasien_r.tarif_dijamin
            ELSE - obatalkespasien_r.tarif_dijamin
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS primary_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS prescribe_doc_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS perform_doc_id,
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
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN obatalkespasien_r.penjualanresep_id IS NULL THEN concat('PEG', obatalkespasien_r.pegawai_id)
            ELSE concat('PEG', penjualanresep_t.pegawai_id)
        END AS backup_analytic_id,
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
    penjualanresep_r.nama_pasien
   FROM obatalkespasien_r
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
            penjualanresep_r_1.tglresep AS tglpasienpulang
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
                END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id;");

        $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockoutdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockoutdetail_v\" AS  SELECT concat('PRSP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PRSP', int_obatalkespasien_r.penjualanresep_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN int_obatalkespasien_r.det_konversi = 0::double precision THEN int_obatalkespasien_r.det_konversi
            WHEN int_obatalkespasien_r.det_konversi IS NULL THEN int_obatalkespasien_r.qty_konversi
            ELSE int_obatalkespasien_r.det_konversi
        END AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_obatalkespasien_r.tglpelayanan AS tanggal_transaksi
   FROM int_obatalkespasien_r
     JOIN int_penjualanresep_r ON int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id AND int_penjualanresep_r.is_sent = true
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m ON int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id
UNION ALL
 SELECT concat('PBHP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PBHP', int_obatalkespasien_r.pendaftaran_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
    int_obatalkespasien_r.qty_oa AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_obatalkespasien_r.tglpelayanan AS tanggal_transaksi
   FROM int_obatalkespasien_r
     JOIN int_pendaftaranbmhp_r ON int_obatalkespasien_r.pendaftaran_id = int_pendaftaranbmhp_r.pendaftaran_id AND int_pendaftaranbmhp_r.is_sent = true
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m ON int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id
UNION ALL
 SELECT concat('PRSP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PRSP', int_obatalkespasien_r.penjualanresep_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN int_obatalkespasien_r.det_konversi = 0::double precision THEN int_obatalkespasien_r.det_konversi
            WHEN int_obatalkespasien_r.det_konversi IS NULL THEN int_obatalkespasien_r.qty_konversi
            ELSE int_obatalkespasien_r.det_konversi
        END AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobatalkes_t.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon,
        CASE
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = true THEN 'SUKSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = true AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN int_obatalkespasien_r.is_sending = false AND int_obatalkespasien_r.is_sent = false AND int_obatalkespasien_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    int_obatalkespasien_r.tglpelayanan AS tanggal_transaksi
   FROM int_obatalkespasien_r
     JOIN int_penjualanresep_r ON int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id AND int_penjualanresep_r.is_sent = true
     JOIN penjualanresep_t ipr ON ipr.penjualanresep_id = int_obatalkespasien_r.penjualanresep_id
     JOIN stokobatalkes_t ON stokobatalkes_t.obatalkespasien_id = int_obatalkespasien_r.obatalkespasien_id
     JOIN obatalkes_m ON int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
  WHERE ipr.status_reseptur = 432 AND ipr.pembatalanresep_id IS NOT NULL AND int_obatalkespasien_r.racikan_id = 1;");

        $this->execute('ALTER TABLE "public"."int_stockoutdetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockreturn_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockreturn_v\" AS  SELECT concat('RTR', returresep_r.returresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', returresep_r.no_returresep) AS name,
    penjualanresep_t.pasien_id::character varying AS partner_id,
    'return'::text AS location_id,
    returresep_r.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    returresep_r.tgl_retur AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    returresep_r.is_sent,
    returresep_r.is_sending,
    returresep_r.sync_respon,
    returresep_r.id,
    'RETUR_RESEP'::text AS tipe_rekap,
        CASE
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN returresep_r.is_sending = false AND returresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returresep_r.is_sending = true AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN returresep_r.is_sending = false AND returresep_r.is_sent = false AND returresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    returresep_r.tgl_retur AS tanggal_transaksi,
    pasien_m.nama_pasien AS partner_name,
    ruangan_m.ruangan_nama AS location_name
   FROM returresep_r
     JOIN penjualanresep_t ON returresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = returresep_r.ruangan_id
UNION ALL
 SELECT concat('BTL', pembatalanresep_r.pembatalanresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', pembatalanresep_r.no_pembatalan) AS name,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN penjualanresep_t.pasien_id::character varying
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat('PEG', penjualanresep_t.karyawan_id)::character varying
            ELSE penjualanresep_t.nama_pembeli
        END AS partner_id,
    'return'::text AS location_id,
    penjualanresep_t.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    pembatalanresep_r.tgl_pembatalan AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    pembatalanresep_r.is_sent,
    pembatalanresep_r.is_sending,
    pembatalanresep_r.sync_respon,
    pembatalanresep_r.id,
    'BATAL_RESEP'::text AS tipe_rekap,
        CASE
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembatalanresep_r.is_sending = false AND pembatalanresep_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembatalanresep_r.is_sending = true AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembatalanresep_r.is_sending = false AND pembatalanresep_r.is_sent = false AND pembatalanresep_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pembatalanresep_r.tgl_pembatalan AS tanggal_transaksi,
    pasien_m.nama_pasien AS partner_name,
    ruangan_m.ruangan_nama AS location_name
   FROM pembatalanresep_r
     JOIN penjualanresep_t ON pembatalanresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = penjualanresep_t.pasien_id
     LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = penjualanresep_t.ruangan_id
  WHERE penjualanresep_t.status_reseptur = 660;");

        $this->execute('ALTER TABLE "public"."int_stockreturn_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockreturndetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockreturndetail_v\" AS  SELECT concat('RTR', returresepdetail_r.returresepdetail_id) AS sync_id_api,
    concat('RTR', returresepdetail_r.returresep_id) AS picking_id,
    concat('OBT', obatalkespasien_t.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE COALESCE(obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text, ''::text)
            WHEN ''::text THEN returresepdetail_r.qty_retur
            ELSE ((obatalkespasien_t.additional_data::json ->> 'nilai_konversi'::text)::double precision) * returresepdetail_r.qty_retur
        END AS product_uom_qty,
    obatalkespasien_t.satuankecil_id AS product_uom_id,
    obatalkespasien_t.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    returresepdetail_r.id,
    returresepdetail_r.is_sent,
    returresepdetail_r.is_sending,
    returresepdetail_r.sync_respon,
    'RETUR_RESEP'::text AS tipe_rekap,
        CASE
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = false AND returresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN returresepdetail_r.is_sending = false AND returresepdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN returresepdetail_r.is_sending = true AND returresepdetail_r.is_sent = false AND returresepdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    returresepdetail_r.tgl_proses AS tanggal_transaksi
   FROM returresepdetail_r
     JOIN returresep_r ON returresepdetail_r.returresep_id = returresep_r.returresep_id AND returresep_r.is_sent = true
     JOIN obatalkespasien_t ON returresepdetail_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
UNION ALL
 SELECT concat('BTL', pembatalanresepdetail_r.obatalkespasien_id) AS sync_id_api,
    concat('BTL', pembatalanresep_r.pembatalanresep_id) AS picking_id,
    concat('OBT', pembatalanresepdetail_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN pembatalanresepdetail_r.det_konversi = 0::double precision THEN pembatalanresepdetail_r.det_konversi
            WHEN pembatalanresepdetail_r.det_konversi IS NULL THEN pembatalanresepdetail_r.qty_konversi
            ELSE pembatalanresepdetail_r.det_konversi
        END AS product_uom_qty,
    pembatalanresepdetail_r.satuankecil_id AS product_uom_id,
    pembatalanresepdetail_r.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    pembatalanresepdetail_r.id,
    pembatalanresepdetail_r.is_sent,
    pembatalanresepdetail_r.is_sending,
    pembatalanresepdetail_r.sync_respon,
    'BATAL_RESEP'::text AS tipe_rekap,
        CASE
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pembatalanresepdetail_r.is_sending = false AND pembatalanresepdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pembatalanresepdetail_r.is_sending = true AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pembatalanresepdetail_r.is_sending = false AND pembatalanresepdetail_r.is_sent = false AND pembatalanresepdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pembatalanresepdetail_r.tgl_proses AS tanggal_transaksi
   FROM pembatalanresepdetail_r
     JOIN penjualanresep_t ON pembatalanresepdetail_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN pembatalanresep_r ON penjualanresep_t.penjualanresep_id = pembatalanresep_r.penjualanresep_id AND pembatalanresep_r.is_sent = true
     JOIN obatalkes_m ON pembatalanresepdetail_r.obatalkes_id = obatalkes_m.obatalkes_id;");

        $this->execute('ALTER TABLE "public"."int_stockreturndetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_stockscrap_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'adj_keluar'::text AS tipe_rekap,
    concat('AJK', adjusmenobatkeluar_r.adjusmenobatkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AI'::text AS trans_type,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
    concat('OBT', adjusmenobatkeluar_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenobatkeluar_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatkeluar_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatkeluar_r.id,
    adjusmenobatkeluar_r.is_sending,
    adjusmenobatkeluar_r.is_sent,
    adjusmenobatkeluar_r.sync_respon,
        CASE
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatkeluar_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatkeluar_r
     JOIN adjusmenobat_t ON adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatkeluar_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatkeluar_id, stokobatalkes_t.nobatch) stok ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id
UNION ALL
 SELECT 'adj_masuk'::text AS tipe_rekap,
    concat('AJM', adjusmenobatmasuk_r.adjusmenobatmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
    concat('OBT', adjusmenobatmasuk_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenobatmasuk_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatmasuk_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatmasuk_r.id,
    adjusmenobatmasuk_r.is_sending,
    adjusmenobatmasuk_r.is_sent,
    adjusmenobatmasuk_r.sync_respon,
        CASE
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatmasuk_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatmasuk_r
     JOIN adjusmenobat_t ON adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatmasuk_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatmasuk_id, stokobatalkes_t.nobatch) stok ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id
UNION ALL
 SELECT 'pemusnahan_obat'::text AS tipe_rekap,
    concat('PMO', pemusnahanobatdetail_r.pemusnahanobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanobat_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanobat_t.tglpemusnahan AS transaction_datetime,
    to_char(pemusnahanobat_t.tglpemusnahan, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'BR'::text AS trans_type,
    pemusnahanobat_t.ruangan_id::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanobatdetail_r.nobatch AS lot_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
    concat('OBT', pemusnahanobatdetail_r.obatalkes_id) AS product_id,
    concat(pemusnahanobat_t.nopemusnahan, '-', obatalkes_m.obatalkes_nama) AS name,
    pemusnahanobatdetail_r.satuan_id AS product_uom_id,
    pemusnahanobatdetail_r.jumlah AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemusnahanobatdetail_r.jumlah * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemusnahanobatdetail_r.id,
    pemusnahanobatdetail_r.is_sending,
    pemusnahanobatdetail_r.is_sent,
    pemusnahanobatdetail_r.sync_respon,
        CASE
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemusnahanobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemusnahanobatdetail_r
     JOIN pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemusnahanobatdetail_id
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
UNION ALL
 SELECT 'pemakaian_obat'::text AS tipe_rekap,
    concat('PKO', pemakaianobatdetail_r.pemakaianobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianobat_t.nopemakaian_obat AS origin,
    NULL::text AS admission_id,
    pemakaianobat_t.tglpemakaianobat AS transaction_datetime,
    to_char(pemakaianobat_t.tglpemakaianobat::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'SC'::text AS trans_type,
    pemakaianobat_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
    concat('OBT', pemakaianobatdetail_r.obatalkes_id) AS product_id,
    concat(pemakaianobat_t.nopemakaian_obat, '-', obatalkes_m.obatalkes_nama) AS name,
    pemakaianobatdetail_r.satuankecil_id AS product_uom_id,
    pemakaianobatdetail_r.qty_satuanpakai AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemakaianobatdetail_r.qty_satuanpakai::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemakaianobatdetail_r.id,
    pemakaianobatdetail_r.is_sending,
    pemakaianobatdetail_r.is_sent,
    pemakaianobatdetail_r.sync_respon,
        CASE
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NULL THEN 'DALAM PROSES'::text
            WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN 'GAGAL'::text
            ELSE NULL::text
        END AS status_proses,
    pemakaianobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemakaianobatdetail_r
     JOIN pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemakaianobatdetail_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemakaianobatdetail_id, stokobatalkes_t.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id;");

        $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210216_023012_oddo_20210216_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210216_023012_oddo_20210216_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
