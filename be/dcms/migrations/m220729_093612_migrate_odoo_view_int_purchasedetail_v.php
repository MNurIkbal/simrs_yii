<?php

use yii\db\Migration;

/**
 * Class m220729_093612_migrate_odoo_view_int_purchasedetail_v
 */
class m220729_093612_migrate_odoo_view_int_purchasedetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_purchasedetail_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_purchasedetail_v" AS  SELECT \'obat\'::text AS jenis,
                concat(\'POS\', penerimaansuppdetail_r.id) AS sync_id_api,
                concat(\'POS\', penerimaansupp_r.penerimaansupp_id) AS picking_id,
                concat(\'POS\', penerimaansupp_r.penerimaansupp_id) AS order_id,
                NULL::text AS sequence,
                concat(\'SUP\', penerimaansupp_r.supplier_id) AS partner_id,
                concat(\'OBT\', penerimaansuppdetail_r.obatalkes_id) AS product_id,
                concat(\'OBT\', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
                obatalkes_m.obatalkes_nama AS name,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS normal_price,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_unit,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * (penerimaansuppdetail_r.diskon / 100::numeric)::double precision AS discount_value,
                penerimaansuppdetail_r.diskon AS discount_persen,
                penerimaansuppdetail_r.satuankecil_id AS product_uom,
                false AS is_conversion,
                obatalkes_m.is_consigment AS is_consignment,
                false AS converted,
                1 AS convertion_rate,
                penerimaansuppdetail_r.qty_kecil AS product_qty,
                penerimaansuppdetail_r.qty_kecil AS qty_received,
                    CASE COALESCE(pajak_m.pajak_persen::integer, 0)
                        WHEN 0 THEN NULL::text
                        ELSE \'ppn\'::text
                    END AS taxes_id,
                (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_tax,
                (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS has_tax,
                penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_total,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision + (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_subtotal,
                penerimaansupp_r.tgl_penerimaan AS date_planned,
                13 AS currency_id,
                \'draft\'::text AS state,
                penerimaansuppdetail_r.id,
                penerimaansuppdetail_r.is_sent,
                penerimaansuppdetail_r.is_sending,
                \'POS\'::text AS tipe_rekap,
                \'6\'::text AS sync_type,
                    CASE
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL AND penerimaansuppdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN penerimaansuppdetail_r.is_sending = false AND penerimaansuppdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS batch_no,
                NULL::text AS batch_id,
                penerimaansuppdetail_t.tgl_kadaluarsa AS expiry_date,
                pajak_m.pajak_persen AS ppn,
                0 AS pph
               FROM penerimaansuppdetail_r
                 JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_r.penerimaansuppdetail_id AND penerimaansuppdetail_t.is_deleted = false
                 JOIN penerimaansupp_r ON penerimaansuppdetail_r.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id AND penerimaansupp_r.is_sent = true
                 JOIN obatalkes_m ON penerimaansuppdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN satuanunit_m satuan_kecil ON penerimaansuppdetail_r.satuankecil_id = satuan_kecil.satuanunit_id
                 JOIN pajak_m ON penerimaansupp_r.pajak_id = pajak_m.pajak_id
                 JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                concat(\'POM\', penerimaanobatdetail_r.id) AS sync_id_api,
                concat(\'POM\', penerimaanobat_r.penerimaanobat_id) AS picking_id,
                concat(\'POM\', penerimaanobat_r.penerimaanobat_id) AS order_id,
                NULL::text AS sequence,
                concat(\'SUP\', penerimaanobat_r.supplier_id) AS partner_id,
                concat(\'OBT\', penerimaanobatdetail_r.obatalkes_id) AS product_id,
                concat(\'OBT\', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
                obatalkes_m.obatalkes_nama AS name,
                qty_konversi.harga_konversi AS normal_price,
                qty_konversi.harga_konversi AS price_unit,
                qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision AS discount_value,
                penerimaanobatdetail_r.discount AS discount_persen,
                satuan.satuankecil_id AS product_uom,
                false AS is_conversion,
                obatalkes_m.is_consigment AS is_consignment,
                false AS converted,
                1 AS convertion_rate,
                qty_konversi.qty_konversi AS product_qty,
                qty_konversi.qty_konversi AS qty_received,
                    CASE COALESCE(po.pajak_persen::integer, 0)
                        WHEN 0 THEN NULL::text
                        ELSE \'ppn\'::text
                    END AS taxes_id,
                (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_tax,
                (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS has_tax,
                qty_konversi.qty_konversi * qty_konversi.harga_konversi AS price_total,
                qty_konversi.harga_konversi * qty_konversi.qty_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision * qty_konversi.qty_konversi + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_subtotal,
                penerimaanobat_r.tgl_penerimaan AS date_planned,
                13 AS currency_id,
                \'draft\'::text AS state,
                penerimaanobatdetail_r.id,
                penerimaanobatdetail_r.is_sent,
                penerimaanobatdetail_r.is_sending,
                \'POM\'::text AS tipe_rekap,
                \'6\'::text AS sync_type,
                    CASE
                        WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = false AND penerimaanobatdetail_r.id_sync_sercon IS NOT NULL AND penerimaanobatdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN penerimaanobatdetail_r.is_sending = false AND penerimaanobatdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN penerimaanobatdetail_r.is_sending = true AND penerimaanobatdetail_r.is_sent = false AND penerimaanobatdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS batch_no,
                NULL::text AS batch_id,
                penerimaanobatdetail_t.tgl_kadaluarsa AS expiry_date,
                po.pajak_persen AS ppn,
                0 AS pph
               FROM penerimaanobatdetail_r
                 JOIN penerimaanobatdetail_t ON penerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_r.penerimaanobatdetail_id AND penerimaanobatdetail_t.is_deleted = false
                 JOIN penerimaanobat_r ON penerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id AND penerimaanobat_r.is_sent = true
                 JOIN obatalkes_m ON penerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
                 LEFT JOIN satuankonversi_m ON penerimaanobatdetail_r.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                 LEFT JOIN satuanunit_m satuan_kecil ON satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id
                 JOIN supplier_m ON penerimaanobat_r.supplier_id = supplier_m.supplier_id
                 LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
                        pajak_m.pajak_persen
                       FROM validasipoobat_t
                         JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
                      WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_r.validasipoobat_id = po.validasipoobat_id
                 LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
                        satuankonversi_m_1.satuankecil_id,
                        satuan_kecil_1.satuanunit_nama AS satuan_kecil
                       FROM satuankonversi_m satuankonversi_m_1
                         JOIN satuanunit_m satuan_kecil_1 ON satuankonversi_m_1.satuankecil_id = satuan_kecil_1.satuanunit_id
                      WHERE satuankonversi_m_1.is_deleted = false) satuan ON penerimaanobatdetail_r.s_konversiobt_id = satuan.satuankonversi_id
                 LEFT JOIN ( SELECT penerimaanobatdetail_r_1.penerimaanobatdetail_id,
                        penerimaanobatdetail_r_1.qty_diterima::double precision * satuankonversi_m_1.nilai_konversi AS qty_konversi,
                        penerimaanobatdetail_r_1.harga / satuankonversi_m_1.nilai_konversi AS harga_konversi
                       FROM penerimaanobatdetail_r penerimaanobatdetail_r_1
                         JOIN satuankonversi_m satuankonversi_m_1 ON penerimaanobatdetail_r_1.s_konversiobt_id = satuankonversi_m_1.satuankonversi_id
                      WHERE satuankonversi_m_1.is_deleted = false) qty_konversi ON penerimaanobatdetail_r.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                concat(\'PBM\', penerimaanbarangdetail_r.id) AS sync_id_api,
                concat(\'PBM\', penerimaanbarang_r.penerimaanbarang_id) AS picking_id,
                concat(\'PBM\', penerimaanbarang_r.penerimaanbarang_id) AS order_id,
                NULL::text AS sequence,
                concat(\'SUP\', penerimaanbarang_r.supplier_id) AS partner_id,
                concat(\'BRG\', penerimaanbarangdetail_r.barang_id) AS product_id,
                concat(\'BRG\', barang_m.kelompokbarang_id) AS product_categ_id,
                barang_m.barang_nama AS name,
                qty_konversi.harga_konversi AS normal_price,
                qty_konversi.harga_konversi AS price_unit,
                qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision AS discount_value,
                penerimaanbarangdetail_r.discount AS discount_persen,
                satuan.satuankecil_id AS product_uom,
                false AS is_conversion,
                    CASE
                        WHEN "left"(barang_m.barang_kode::text, 3) = \'CGN\'::text THEN true
                        ELSE false
                    END AS is_consignment,
                false AS converted,
                1 AS convertion_rate,
                qty_konversi.qty_konversi AS product_qty,
                qty_konversi.qty_konversi AS qty_received,
                    CASE COALESCE(po.pajak_persen::integer, 0)
                        WHEN 0 THEN NULL::text
                        ELSE \'ppn\'::text
                    END AS taxes_id,
                (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_tax,
                (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS has_tax,
                qty_konversi.qty_konversi * qty_konversi.harga_konversi AS price_total,
                qty_konversi.harga_konversi * qty_konversi.qty_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision * qty_konversi.qty_konversi + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_r.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi AS price_subtotal,
                penerimaanbarang_r.tgl_penerimaan AS date_planned,
                13 AS currency_id,
                \'draft\'::text AS state,
                penerimaanbarangdetail_r.id,
                penerimaanbarangdetail_r.is_sent,
                penerimaanbarangdetail_r.is_sending,
                \'PBM\'::text AS tipe_rekap,
                \'6\'::text AS sync_type,
                    CASE
                        WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = false AND penerimaanbarangdetail_r.id_sync_sercon IS NOT NULL AND penerimaanbarangdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN penerimaanbarangdetail_r.is_sending = false AND penerimaanbarangdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN penerimaanbarangdetail_r.is_sending = true AND penerimaanbarangdetail_r.is_sent = false AND penerimaanbarangdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS batch_no,
                NULL::text AS batch_id,
                penerimaanbarangdetail_t.tgl_kadaluarsa AS expiry_date,
                po.pajak_persen AS ppn,
                0 AS pph
               FROM penerimaanbarangdetail_r
                 JOIN penerimaanbarangdetail_t ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_r.penerimaanbarangdetail_id AND penerimaanbarangdetail_t.is_deleted = false
                 JOIN penerimaanbarang_r ON penerimaanbarangdetail_r.penerimaanbarang_id = penerimaanbarang_r.penerimaanbarang_id AND penerimaanbarang_r.is_sent = true
                 JOIN barang_m ON penerimaanbarangdetail_r.barang_id = barang_m.barang_id
                 LEFT JOIN satuankonversibrg_m ON penerimaanbarangdetail_r.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                 LEFT JOIN satuanunit_m satuan_kecil ON satuankonversibrg_m.satuankecil_id = satuan_kecil.satuanunit_id
                 JOIN supplier_m ON penerimaanbarang_r.supplier_id = supplier_m.supplier_id
                 LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
                        pajak_m.pajak_persen
                       FROM validasipobarang_t
                         JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
                      WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_r.validasipobarang_id = po.validasipobarang_id
                 LEFT JOIN ( SELECT satuankonversibrg_m_1.satuankonversibrg_id,
                        satuankonversibrg_m_1.satuankecil_id,
                        satuan_kecil_1.satuanunit_nama AS satuan_kecil
                       FROM satuankonversibrg_m satuankonversibrg_m_1
                         JOIN satuanunit_m satuan_kecil_1 ON satuankonversibrg_m_1.satuankecil_id = satuan_kecil_1.satuanunit_id
                      WHERE satuankonversibrg_m_1.is_deleted = false) satuan ON penerimaanbarangdetail_r.s_konversibrg_id = satuan.satuankonversibrg_id
                 LEFT JOIN ( SELECT penerimaanbarangdetail_r_1.penerimaanbarangdetail_id,
                        penerimaanbarangdetail_r_1.qty_diterima::double precision * satuankonversibrg_m_1.nilai_konversi AS qty_konversi,
                        penerimaanbarangdetail_r_1.harga / satuankonversibrg_m_1.nilai_konversi AS harga_konversi
                       FROM penerimaanbarangdetail_r penerimaanbarangdetail_r_1
                         JOIN satuankonversibrg_m satuankonversibrg_m_1 ON penerimaanbarangdetail_r_1.s_konversibrg_id = satuankonversibrg_m_1.satuankonversibrg_id
                      WHERE satuankonversibrg_m_1.is_deleted = false) qty_konversi ON penerimaanbarangdetail_r.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                concat(\'PBS\', penerimaansuppdetail_r.id) AS sync_id_api,
                concat(\'PBS\', penerimaansupp_r.penerimaansupp_id) AS picking_id,
                concat(\'PBS\', penerimaansupp_r.penerimaansupp_id) AS order_id,
                NULL::text AS sequence,
                concat(\'SUP\', penerimaansupp_r.supplier_id) AS partner_id,
                concat(\'BRG\', penerimaansuppdetail_r.barang_id) AS product_id,
                concat(\'BRG\', barang_m.kelompokbarang_id) AS product_categ_id,
                barang_m.barang_nama AS name,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS normal_price,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_unit,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * (penerimaansuppdetail_r.diskon / 100::numeric)::double precision AS discount_value,
                penerimaansuppdetail_r.diskon AS discount_persen,
                penerimaansuppdetail_r.satuankecil_id AS product_uom,
                false AS is_conversion,
                    CASE
                        WHEN "left"(barang_m.barang_kode::text, 3) = \'CGN\'::text THEN true
                        ELSE false
                    END AS is_consignment,
                false AS converted,
                1 AS convertion_rate,
                penerimaansuppdetail_r.qty_kecil AS product_qty,
                penerimaansuppdetail_r.qty_kecil AS qty_received,
                    CASE COALESCE(pajak_m.pajak_persen::integer, 0)
                        WHEN 0 THEN NULL::text
                        ELSE \'ppn\'::text
                    END AS taxes_id,
                (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_tax,
                (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS has_tax,
                penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision AS price_total,
                penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision + (penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision - penerimaansuppdetail_r.harga_netto / penerimaansuppdetail_r.qty_kecil::double precision * penerimaansuppdetail_r.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * penerimaansuppdetail_r.qty_kecil::double precision AS price_subtotal,
                penerimaansupp_r.tgl_penerimaan AS date_planned,
                13 AS currency_id,
                \'draft\'::text AS state,
                penerimaansuppdetail_r.id,
                penerimaansuppdetail_r.is_sent,
                penerimaansuppdetail_r.is_sending,
                \'PBS\'::text AS tipe_rekap,
                \'6\'::text AS sync_type,
                    CASE
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL AND penerimaansuppdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN penerimaansuppdetail_r.is_sending = false AND penerimaansuppdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN penerimaansuppdetail_r.is_sending = true AND penerimaansuppdetail_r.is_sent = false AND penerimaansuppdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                NULL::text AS batch_no,
                NULL::text AS batch_id,
                penerimaansuppdetail_t.tgl_kadaluarsa AS expiry_date,
                pajak_m.pajak_persen AS ppn,
                0 AS pph
               FROM penerimaansuppdetail_r
                 JOIN penerimaansuppdetail_t ON penerimaansuppdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_r.penerimaansuppdetail_id AND penerimaansuppdetail_t.is_deleted = false
                 JOIN penerimaansupp_r ON penerimaansuppdetail_r.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id AND penerimaansupp_r.is_sent = true
                 JOIN barang_m ON penerimaansuppdetail_r.barang_id = barang_m.barang_id
                 JOIN satuanunit_m satuan_kecil ON penerimaansuppdetail_r.satuankecil_id = satuan_kecil.satuanunit_id
                 JOIN pajak_m ON penerimaansupp_r.pajak_id = pajak_m.pajak_id
                 JOIN supplier_m ON penerimaansupp_r.supplier_id = supplier_m.supplier_id;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093612_migrate_odoo_view_int_purchasedetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093612_migrate_odoo_view_int_purchasedetail_v cannot be reverted.\n";

        return false;
    }
    */
}
