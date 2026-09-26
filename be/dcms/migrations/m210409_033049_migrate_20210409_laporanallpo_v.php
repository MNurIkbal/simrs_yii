<?php

use yii\db\Migration;

/**
 * Class m210409_033049_migrate_20210409_laporanallpo_v
 */
class m210409_033049_migrate_20210409_laporanallpo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."laporanallpo_v";');

    $this->execute("
        CREATE VIEW \"public\".\"laporanallpo_v\" AS  SELECT 'OBAT'::text AS type,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipoobat_t.created_date AS tanggal_po,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
    validasipoobatdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipoobatdetail_t.harga AS price,
    validasipoobatdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS gross_amount,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr
   FROM validasipoobatdetail_t
     JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT pen_det.validasipoobatdetail_id,
            pen_det.obatalkes_id,
            pen_det.s_konversiobt_id,
            pen_det.qty_diterima,
            pen_det.jumlah,
            pen_det.discount_rp,
            pen_det.po_balance
           FROM penerimaanobatdetail_t pen_det
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON pen_det.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE pen_det.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT penerimaanobat_t_1.penerimaanobat_id,
            penerimaanobat_t_1.validasipoobat_id,
            penerimaanobat_t_1.tgl_penerimaan,
            penerimaanobat_t_1.no_penerimaan
           FROM penerimaanobat_t penerimaanobat_t_1
             JOIN ( SELECT penerimaanobat_t_2.validasipoobat_id,
                    max(penerimaanobat_t_2.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t penerimaanobat_t_2
                  GROUP BY penerimaanobat_t_2.validasipoobat_id) max_pen ON penerimaanobat_t_1.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
UNION ALL
 SELECT 'BARANG'::text AS type,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipobarang_t.created_date AS tanggal_po,
        CASE validasipobarang_t.is_validasi
            WHEN true THEN validasipobarang_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipobarang_t.no_pobarang::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    barang_m.barang_kode::character varying(100) AS item_code,
    barang_m.barang_nama::character varying(255) AS item_name,
    validasipobarangdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipobarangdetail_t.qty_penerimaan, 0) - COALESCE(returdetailjumlah.qty_retur::integer, 0) AS po_balance,
    COALESCE(validasipobarangdetail_t.qty_sisa, 0) + COALESCE(returdetailjumlah.qty_retur::integer, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversibrg_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipobarangdetail_t.harga AS price,
    validasipobarangdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp AS gross_amount,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * validasipobarang_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipobarang_t.status_penerimaan)::text) AS status_po,
    btrim(validasipobarang_t.catatan1) AS catatan_1,
    btrim(validasipobarang_t.catatan2) AS catatan_2,
    purchasereqbrg_t.is_prcyto AS cyto,
        CASE purchasereqbrg_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipobarang_t.catatan IS NOT NULL THEN validasipobarang_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipobarang_t.catatan) AS reject_remarks,
    btrim(penerimaanbarang_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanbarang_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereqbrg_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status_pr
   FROM validasipobarangdetail_t
     JOIN validasipobarang_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN purchasereqbrgdetail_t ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id
     LEFT JOIN purchasereqbrg_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     JOIN barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT pen_det.validasipobarangdetail_id,
            pen_det.penerimaanbarangdetail_id,
            pen_det.barang_id,
            pen_det.s_konversibrg_id,
            pen_det.qty_diterima,
            pen_det.jumlah,
            pen_det.discount_rp,
            pen_det.po_balance
           FROM penerimaanbarangdetail_t pen_det
             JOIN ( SELECT penerimaanbarangdetail_t_1.validasipobarangdetail_id,
                    max(penerimaanbarangdetail_t_1.penerimaanbarangdetail_id) AS penerimaanbarangdetail_id
                   FROM penerimaanbarangdetail_t penerimaanbarangdetail_t_1
                  GROUP BY penerimaanbarangdetail_t_1.validasipobarangdetail_id) max_det ON pen_det.penerimaanbarangdetail_id = max_det.penerimaanbarangdetail_id
          WHERE pen_det.is_deleted = false) penerimaanbarangdetail_t ON validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id
     LEFT JOIN ( SELECT penerimaanbarang_t_1.penerimaanbarang_id,
            penerimaanbarang_t_1.validasipobarang_id,
            penerimaanbarang_t_1.tgl_penerimaan,
            penerimaanbarang_t_1.no_penerimaan
           FROM penerimaanbarang_t penerimaanbarang_t_1
             JOIN ( SELECT penerimaanbarang_t_2.validasipobarang_id,
                    max(penerimaanbarang_t_2.penerimaanbarang_id) AS penerimaanbarang_id
                   FROM penerimaanbarang_t penerimaanbarang_t_2
                  GROUP BY penerimaanbarang_t_2.validasipobarang_id) max_pen ON penerimaanbarang_t_1.penerimaanbarang_id = max_pen.penerimaanbarang_id) penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     LEFT JOIN satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN manufaktur_m ON barang_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT pt.validasipobarangdetail_id,
            sum(returpenerimaanbarangdetail_t.qty_input) AS qty_retur
           FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t
             LEFT JOIN penerimaanbarangdetail_t pt ON pt.penerimaanbarangdetail_id = returpenerimaanbarangdetail_t.penerimaanbarangdetail_id
          GROUP BY pt.validasipobarangdetail_id) returdetailjumlah ON validasipobarangdetail_t.validasipobarangdetail_id = returdetailjumlah.validasipobarangdetail_id;");

    $this->execute('ALTER TABLE "public"."laporanallpo_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210409_033049_migrate_20210409_laporanallpo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210409_033049_migrate_20210409_laporanallpo_v cannot be reverted.\n";

        return false;
    }
    */
}
