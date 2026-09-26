<?php

use yii\db\Migration;

/**
 * Class m221121_074121_migrate_odoo_view_saleorder_linedetail_v
 */
class m221121_074121_migrate_odoo_view_saleorder_linedetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS saleorder_linedetail_v;
        ');

        $this->execute('
            CREATE VIEW "public"."saleorder_linedetail_v" AS  SELECT 6 AS sync_type,
    tp_paket.tgl_proses::date AS tglproses,
    concat(\'TND\', tp_paket.id, \'-\', concat(\'TND\', paketpelayanan_mp.daftartindakan_id)) AS sync_id_api,
    concat(\'TND\', paketpelayanan_mp.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom, 
    tp_paket.qty_tindakan AS product_uom_qty,
    paket_detail.harga_satuan + paket_detail.harga_cyto + paket_detail.harga_penyulit AS price_unit,
    paket_detail.harga_total AS price_subtotal,
    paket_detail.harga_total AS price_total,
        CASE
            WHEN persen.tarif_dijamin = 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = \'BILLING\'::text THEN paket_detail.harga_total
                WHEN tp_paket.keterangan::text = \'BILLING CANCEL\'::text THEN \'-1\'::integer::double precision * paket_detail.harga_total
                ELSE 0::double precision
            END
            WHEN persen.tarif_dijamin <> 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = \'BILLING\'::text THEN (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan
                WHEN tp_paket.keterangan::text = \'BILLING CANCEL\'::text THEN \'-1\'::integer::double precision * ((paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan)
                ELSE NULL::double precision
            END
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN persen.tarif_dibayarkan = 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = \'BILLING\'::text THEN paket_detail.harga_total
                WHEN tp_paket.keterangan::text = \'BILLING CANCEL\'::text THEN \'-1\'::integer::double precision * paket_detail.harga_total
                ELSE 0::double precision
            END
            WHEN persen.tarif_dibayarkan <> 0::double precision THEN
            CASE
                WHEN tp_paket.keterangan::text = \'BILLING\'::text THEN paket_detail.harga_total - (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan
                WHEN tp_paket.keterangan::text = \'BILLING CANCEL\'::text THEN \'-1\'::integer::double precision * (paket_detail.harga_total - (paket_detail.harga_total - paket_detail.harga_total * persen.persen_diskon) * persen.persen_dibayarkan)
                ELSE 0::double precision
            END
            ELSE 0::double precision
        END AS payer_amount,
    tp_paket.pendaftaran_id AS order_id,
    concat(\'CATEG\', daftartindakan_m.servicecategory_id) AS service_categ_id,
        CASE
            WHEN tin_pel.dokterpenanggungjawab_id IS NOT NULL THEN concat(\'PEG\', tin_pel.dokterpenanggungjawab_id)
            ELSE concat(\'PEG\', pendaftaran_r_1.pegawai_id)
        END AS primary_doc_id,
    NULL::text AS prescribe_doc_id,
    concat(\'PEG\', tp_paket.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m_1.ruangan_id::text AS location_id,
    ruangan_m_1.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t_1.no_pembayaran, no_pembayaran_1.no_pembayaran) AS billno,
    pembayaranpelayanan_t_1.tgl_pembayaran AS bill_date,
    tp_paket.keterangan AS type_line,
        CASE
            WHEN pendaftaran_r_1.is_aps = true THEN \'LOS\'::text
            WHEN kelompoktindakan_m.kelompoktindakan_namalainnya::text = \'LOS\'::text THEN \'LOS\'::text
            ELSE \'LOB\'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN pendaftaran_r_1.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN tp_paket.pasienadmisi_id IS NOT NULL THEN \'IPD\'::text
                ELSE \'EMERGENCY\'::text
            END
            ELSE
            CASE
                WHEN pendaftaran_r_1.instalasi_id = 1 THEN \'OPD\'::text
                WHEN pendaftaran_r_1.instalasi_id = 2 THEN \'EMERGENCY\'::text
                WHEN pendaftaran_r_1.instalasi_id = 3 THEN \'IPD\'::text
                WHEN tp_paket.instalasi_id = 21 THEN \'MCU\'::text
                ELSE \'OPD\'::text
            END
        END AS patient_group,
    \'-\'::text AS special_group,
        CASE
            WHEN tp_paket.instalasi_id = 1 THEN \'OUTPATIENT\'::text
            WHEN tp_paket.instalasi_id = 2 THEN \'EMERGENCY\'::text
            WHEN tp_paket.instalasi_id = 3 THEN \'INPATIENT\'::text
            WHEN tp_paket.instalasi_id = 21 THEN \'MCU\'::text
            WHEN tp_paket.instalasi_id = 4 THEN \'LABORATORY\'::text
            WHEN tp_paket.instalasi_id = 5 THEN \'RADIOLOGY\'::text
            ELSE \'OUTPATIENT\'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m_1.kelaspelayanan_nama AS bed_type,
    concat(\'PEN\', tp_paket.penjamin_id) AS payer,
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
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat(\'PEG\', pendaftaran_r_1.pegawai_id)
            ELSE concat(\'PEG\', tp_paket.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN tp_paket.dokterpenanggungjawab_id IS NULL THEN concat(\'PEG\', pendaftaran_r_1.pegawai_id)
            ELSE concat(\'PEG\', tp_paket.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r_1.kota,
    pendaftaran_r_1.kecamatan,
    pendaftaran_r_1.kelurahan,
    pendaftaran_r_1.pasien_id AS partner_id,
    pendaftaran_r_1.no_rekam_medik AS registration_code,
    pendaftaran_r_1.no_pendaftaran AS number_admission,
    \'-\'::text AS manufacture,
    pendaftaran_r_1.tglpasienpulang::date AS discharge_date,
    pegawai_1.spesialis_nama AS specialization_primary,
    tp_paket.is_sent,
    tp_paket.is_sending,
    tp_paket.id,
    \'PAKET_DETAIL\'::text AS jenis,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY[\'ACCRUAL REVERSAL\'::character varying::text, \'ACCRUAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS status_bill,
        CASE
            WHEN tp_paket.keterangan::text = ANY (ARRAY[\'ACCRUAL REVERSAL\'::character varying::text, \'ACCRUAL\'::character varying::text]) THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
        CASE
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = true THEN \'SUKSES\'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false THEN \'MENUNGGU PROSES\'::text
            WHEN tp_paket.is_sending = true AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
            WHEN tp_paket.is_sending = false AND tp_paket.is_sent = false AND tp_paket.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
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
                  WHERE pendaftaran_r_2.keterangan::text = \'INSERT\'::text
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
     LEFT JOIN tindakanpelayanan_t tin_pel ON tin_pel.daftartindakan_id = paketpelayanan_mp.daftartindakan_id AND tin_pel.ruangan_id = paketpelayanan_mp.ruangan_id AND tin_pel.pendaftaran_id = tp_paket.pendaftaran_id
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
            item_paket.daftartindakan_id::integer AS daftartindakan_id,
            item_paket.harga_satuan::double precision AS harga_satuan,
            item_paket.harga_cyto::double precision AS harga_cyto,
            item_paket.harga_penyulit::double precision AS harga_penyulit,
            item_paket.harga_total::double precision AS harga_total
           FROM tindakanpelayanan_t,
            LATERAL ( SELECT (tt.additional_data::json -> \'detail_paket\'::text)::jsonb AS detail_paket
                   FROM tindakanpelayanan_t tt
                  WHERE tt.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) arr_paket,
            LATERAL ( SELECT (jsonb_array_elements.value -> \'daftartindakan_id\'::text)::text AS daftartindakan_id,
                    (jsonb_array_elements.value -> \'harga_satuan\'::text)::text AS harga_satuan,
                    (jsonb_array_elements.value -> \'harga_cyto\'::text)::text AS harga_cyto,
                    (jsonb_array_elements.value -> \'harga_penyulit\'::text)::text AS harga_penyulit,
                    (jsonb_array_elements.value -> \'harga_total\'::text)::text AS harga_total
                   FROM jsonb_array_elements(arr_paket.detail_paket) jsonb_array_elements(value)
                  WHERE jsonb_typeof(arr_paket.detail_paket) = \'array\'::text AND ((jsonb_array_elements.value -> \'daftartindakan_id\'::text)::text) <> \'null\'::text) item_paket) paket_detail ON tp_paket.tindakanpelayanan_id = paket_detail.tindakanpelayanan_id AND daftartindakan_m.daftartindakan_id = paket_detail.daftartindakan_id
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1_1.pembayaran_id,
            pembayaranpelayanan_t_1_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1_1) no_pembayaran_1 ON tp_paket.pembayaran_id = no_pembayaran_1.pembayaran_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM pegawai_m
             JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai_1 ON tp_paket.dokterpenanggungjawab_id = pegawai_1.pegawai_id
     LEFT JOIN pasienmasukpenunjang_t ON tp_paket.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN int_billing_r ON tp_paket.pembayaran_id = int_billing_r.pembayaran_id
     LEFT JOIN konsulpoli_t ON konsulpoli_t.konsulpoli_id = tp_paket.konsulpoli_id
     LEFT JOIN ( SELECT tindakanpelayanan_r.id,
            tindakanpelayanan_r.tarif_dijamin / (tindakanpelayanan_r.tarif_tindakan - tindakanpelayanan_r.tarif_diskon) AS persen_dijamin,
            tindakanpelayanan_r.tarif_dibayarkan / (tindakanpelayanan_r.tarif_tindakan - tindakanpelayanan_r.tarif_diskon) AS persen_dibayarkan,
            tindakanpelayanan_r.tarif_diskon / tindakanpelayanan_r.tarif_tindakan AS persen_diskon,
            tindakanpelayanan_r.tarif_dijamin,
            tindakanpelayanan_r.tarif_dibayarkan
           FROM tindakanpelayanan_r) persen ON tp_paket.id = persen.id
  WHERE (tp_paket.keterangan::text <> ALL (ARRAY[\'ACCRUAL\'::text, \'ACCRUAL REVERSAL\'::text])) OR (pendaftaran_r_1.instalasi_id = ANY (ARRAY[2, 3]));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221121_074121_migrate_odoo_view_saleorder_linedetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221121_074121_migrate_odoo_view_saleorder_linedetail_v cannot be reverted.\n";

        return false;
    }
    */
}
