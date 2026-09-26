<?php

use yii\db\Migration;

/**
 * Class m221031_115428_migrate_acn_190_gt_purchaseorder_v
 */
class m221031_115428_migrate_acn_190_gt_purchaseorder_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_purchaseorder_v";
        ');

        $this->execute('
            CREATE VIEW "public"."gt_purchaseorder_v" AS  SELECT \'obat\'::text AS item,
    gt_akuntansi_t.id::integer AS id,
    1 AS company_id,
    validasipoobat_t.no_poobat AS bill_number,
    NULL::text AS order_number,
    NULL::integer AS bill_status_code,
    NULL::text AS bill_state,
    validasipoobat_t.tgl_validasi AS bill_at,
    NULL::text AS due_at,
    NULL::double precision AS amount,
    NULL::text AS currency_code,
    NULL::double precision AS currenncy_rate,
    validasipoobat_t.supplier_id AS vendor_id,
    supplier_m.supplier_nama AS vendor_name,
    supplier_m.email AS vendor_email,
    supplier_m.no_npwp AS vendor_tax_number,
    NULL::text AS vendor_phone,
    supplier_m.supplier_alamat AS vendor_address,
    validasipoobat_t.catatan1 AS notes,
    validasipoobat_t.ruangan_id,
    NULL::integer AS h_account_id,
    validasipoobat_t.payterm_id,
    payterm_m.jumlah_hari AS hari,
    validasipoobat_t.tgl_validasi AS created_at,
    validasipoobat_t.last_modified_date AS updated_at,
    validasipoobat_t.deleted_date AS deleted_at,
    NULL::integer AS kategory_id,
    NULL::integer AS parent_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS tax_id,
    NULL::integer AS tax_account_id,
    penerimaanobat_t.no_faktur,
    supplier_m.supplier_kode,
    gt_akuntansi_t.is_send,
    gt_akuntansi_t.is_sending,
    gt_akuntansi_t.sync_respon,
    penerimaanobat_t.no_penerimaan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM penerimaanobat_t
     JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     JOIN ( SELECT supplier_m_1.supplier_id,
            supplier_m_1.supplier_kode,
            supplier_m_1.supplier_nama,
            supplier_m_1.supplier_alamat,
            supplier_m_1.email,
            supplier_m_1.no_tlp,
            supplier_m_1.no_npwp
           FROM supplier_m supplier_m_1) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT payterm_m_1.payterm_id,
            payterm_m_1.payterm_kode,
            payterm_m_1.payterm_nama,
            payterm_m_1.jumlah_hari
           FROM payterm_m payterm_m_1) payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama,
            ruangan_m_1.instalasi_id
           FROM ruangan_m ruangan_m_1) ruangan_m ON penerimaanobat_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN ( SELECT a.id,
            a.no_referensi,
            a.is_send,
            a.is_sending,
            a.sync_respon
           FROM gt_akuntansi_t a) gt_akuntansi_t ON gt_akuntansi_t.no_referensi::text = penerimaanobat_t.no_penerimaan::text
  WHERE validasipoobat_t.is_deleted = false AND penerimaanobat_t.is_deleted = false AND penerimaanobat_t.is_verifikasi = 1
UNION ALL
 SELECT \'barang\'::text AS item,
    gt_akuntansi_t.id::integer AS id,
    1 AS company_id,
    validasipobarang_t.no_pobarang AS bill_number,
    NULL::text AS order_number,
    NULL::integer AS bill_status_code,
    NULL::text AS bill_state,
    validasipobarang_t.tgl_validasi AS bill_at,
    NULL::text AS due_at,
    NULL::double precision AS amount,
    NULL::text AS currency_code,
    NULL::double precision AS currenncy_rate,
    validasipobarang_t.supplier_id AS vendor_id,
    supplier_m.supplier_nama AS vendor_name,
    supplier_m.email AS vendor_email,
    supplier_m.no_npwp AS vendor_tax_number,
    NULL::text AS vendor_phone,
    supplier_m.supplier_alamat AS vendor_address,
    validasipobarang_t.catatan1 AS notes,
    validasipobarang_t.ruangan_id,
    NULL::integer AS h_account_id,
    validasipobarang_t.payterm_id,
    payterm_m.jumlah_hari AS hari,
    validasipobarang_t.tgl_validasi AS created_at,
    validasipobarang_t.last_modified_date AS updated_at,
    validasipobarang_t.deleted_date AS deleted_at,
    NULL::integer AS kategory_id,
    NULL::integer AS parent_id,
    ruangan_m.ruangan_nama,
    NULL::integer AS tax_id,
    NULL::integer AS tax_account_id,
    penerimaanbarang_t.no_faktur,
    supplier_m.supplier_kode,
    gt_akuntansi_t.is_send,
    gt_akuntansi_t.is_sending,
    gt_akuntansi_t.sync_respon,
    penerimaanbarang_t.no_penerimaan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM penerimaanbarang_t
     JOIN validasipobarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama,
            a.supplier_alamat,
            a.email,
            a.no_tlp,
            a.no_npwp
           FROM supplier_m a) supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.payterm_id,
            a.payterm_kode,
            a.payterm_nama,
            a.jumlah_hari
           FROM payterm_m a) payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON penerimaanbarang_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN ( SELECT a.id,
            a.no_referensi,
            a.is_send,
            a.is_sending,
            a.sync_respon
           FROM gt_akuntansi_t a) gt_akuntansi_t ON gt_akuntansi_t.no_referensi::text = penerimaanbarang_t.no_penerimaan::text
  WHERE validasipobarang_t.is_deleted = false AND penerimaanbarang_t.is_deleted = false AND penerimaanbarang_t.is_verifikasi = 1;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221031_115428_migrate_acn_190_gt_purchaseorder_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221031_115428_migrate_acn_190_gt_purchaseorder_v cannot be reverted.\n";

        return false;
    }
    */
}
