<?php

use yii\db\Migration;

/**
 * Class m221031_115634_migrate_acn_190_gt_purchaseorderdetail_v
 */
class m221031_115634_migrate_acn_190_gt_purchaseorderdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_purchaseorderdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."gt_purchaseorderdetail_v" AS  SELECT DISTINCT ON (penerimaanobatdetail_t.penerimaanobatdetail_id) \'obat\'::text AS item,
    gt_akuntansi_t.id::integer AS id,
    1 AS company_id,
    validasipoobat_t.no_poobat AS bill_number,
    NULL::text AS order_number,
    validasipoobatdetail_t.validasipoobatdetail_id,
    validasipoobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS item_name,
    obatalkes_m.obatalkes_kode AS sku,
    penerimaanobatdetail_t.qty_diterima AS quantity,
    penerimaanobatdetail_t.harga AS price,
    penerimaanobatdetail_t.jumlah AS total,
    konfigfarmasi_k.persenppn AS tax,
    konfigfarmasi_k.konfigfarmasi_id AS tax_id,
    NULL::integer AS tax_account_id,
    NULL::integer AS d_account_id,
    NULL::integer AS category_id,
    penerimaanobatdetail_t.discount,
    penerimaanobat_t.tgl_penerimaan AS created_at,
    validasipoobatdetail_t.last_modified_date AS updated_at,
    validasipoobatdetail_t.deleted_date AS deleted_at,
    penerimaanobatdetail_t.discount_rp AS discount_amount,
    gt_akuntansi_t.is_send,
    gt_akuntansi_t.is_sending,
    gt_akuntansi_t.sync_respon,
    penerimaanobat_t.no_penerimaan,
    penerimaanobatdetail_t.penerimaanobatdetail_id
   FROM penerimaanobatdetail_t
     JOIN ( SELECT b.penerimaanobat_id,
            b.validasipoobat_id,
            b.no_penerimaan,
            b.tgl_penerimaan
           FROM penerimaanobat_t b
             LEFT JOIN ( SELECT a.id,
                    a.no_referensi,
                    a.is_send,
                    a.is_sending,
                    a.sync_respon
                   FROM gt_akuntansi_t a) gt_akuntansi_t_1 ON gt_akuntansi_t_1.no_referensi::text = b.no_penerimaan::text
          WHERE b.is_deleted = false AND gt_akuntansi_t_1.is_send = true) penerimaanobat_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
     LEFT JOIN validasipoobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT obatalkes_m_1.obatalkes_id,
            obatalkes_m_1.obatalkes_kode,
            obatalkes_m_1.obatalkes_nama
           FROM obatalkes_m obatalkes_m_1) obatalkes_m ON penerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT a.validasipoobat_id,
            a.no_poobat,
            a.tgl_validasi
           FROM validasipoobat_t a
          WHERE a.is_deleted = false
          ORDER BY a.no_poobat) validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN ( SELECT a.id,
            a.no_referensi,
            a.is_send,
            a.is_sending,
            a.sync_respon
           FROM gt_akuntansi_t a
          WHERE a.type_account = \'penerimaan_po_obat\'::text) gt_akuntansi_t ON gt_akuntansi_t.no_referensi::integer = penerimaanobatdetail_t.penerimaanobatdetail_id
  WHERE validasipoobatdetail_t.is_deleted = false
UNION ALL
 SELECT DISTINCT ON (penerimaanbarangdetail_t.penerimaanbarangdetail_id) \'barang\'::text AS item,
    gt_akuntansi_t.id::integer AS id,
    1 AS company_id,
    validasipobarang_t.no_pobarang AS bill_number,
    NULL::text AS order_number,
    validasipobarangdetail_t.validasipobarangdetail_id AS validasipoobatdetail_id,
    validasipobarangdetail_t.barang_id AS obatalkes_id,
    barang_m.barang_nama AS item_name,
    barang_m.barang_kode AS sku,
    penerimaanbarangdetail_t.qty_diterima AS quantity,
    penerimaanbarangdetail_t.harga AS price,
    penerimaanbarangdetail_t.jumlah AS total,
    konfigfarmasi_k.persenppn AS tax,
    konfigfarmasi_k.konfigfarmasi_id AS tax_id,
    NULL::integer AS tax_account_id,
    NULL::integer AS d_account_id,
    NULL::integer AS category_id,
    penerimaanbarangdetail_t.discount,
    penerimaanbarang_t.tgl_penerimaan AS created_at,
    validasipobarangdetail_t.last_modified_date AS updated_at,
    validasipobarangdetail_t.deleted_date AS deleted_at,
    penerimaanbarangdetail_t.discount_rp AS discount_amount,
    gt_akuntansi_t.is_send,
    gt_akuntansi_t.is_sending,
    gt_akuntansi_t.sync_respon,
    penerimaanbarang_t.no_penerimaan,
    penerimaanbarangdetail_t.penerimaanbarangdetail_id AS penerimaanobatdetail_id
   FROM penerimaanbarangdetail_t
     JOIN ( SELECT b.penerimaanbarang_id,
            b.validasipobarang_id,
            b.no_penerimaan,
            b.tgl_penerimaan
           FROM penerimaanbarang_t b
             LEFT JOIN ( SELECT a.id,
                    a.no_referensi,
                    a.is_send,
                    a.is_sending,
                    a.sync_respon
                   FROM gt_akuntansi_t a) gt_akuntansi_t_1 ON gt_akuntansi_t_1.no_referensi::text = b.no_penerimaan::text
          WHERE b.is_deleted = false AND gt_akuntansi_t_1.is_send = true) penerimaanbarang_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id
     LEFT JOIN validasipobarangdetail_t ON validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id
     LEFT JOIN ( SELECT a.barang_id,
            a.barang_kode,
            a.barang_nama
           FROM barang_m a) barang_m ON penerimaanbarangdetail_t.barang_id = barang_m.barang_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN ( SELECT a.validasipobarang_id,
            a.no_pobarang,
            a.tgl_validasi
           FROM validasipobarang_t a
          WHERE a.is_deleted = false
          ORDER BY a.no_pobarang) validasipobarang_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN ( SELECT a.id,
            a.no_referensi,
            a.is_send,
            a.is_sending,
            a.sync_respon
           FROM gt_akuntansi_t a
          WHERE a.type_account = \'penerimaan_po_barang\'::text) gt_akuntansi_t ON gt_akuntansi_t.no_referensi::integer = penerimaanbarangdetail_t.penerimaanbarangdetail_id
  WHERE validasipobarangdetail_t.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221031_115634_migrate_acn_190_gt_purchaseorderdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221031_115634_migrate_acn_190_gt_purchaseorderdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
