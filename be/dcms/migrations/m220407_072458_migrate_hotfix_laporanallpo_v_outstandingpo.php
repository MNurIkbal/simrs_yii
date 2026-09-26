<?php

use yii\db\Migration;

/**
 * Class m220407_072458_migrate_hotfix_laporanallpo_v_outstandingpo
 */
class m220407_072458_migrate_hotfix_laporanallpo_v_outstandingpo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanallpo_v";');
        $this->execute("CREATE VIEW \"public\".\"laporanallpo_v\" AS  
			SELECT 'OBAT'::text AS type,
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
    COALESCE(validasipoobatdetail_t.qty_sisa, validasipoobatdetail_t.qty_input, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipoobatdetail_t.harga
        END AS price,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipoobatdetail_t.discount::double precision
        END AS deduction_percent,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE pajak_m.pajak_persen::double precision
        END AS addition_percent,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision
        END AS gross_amount,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision
        END AS nett_amount,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 'Dibatalkan'::text
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = 0 THEN 'Belum Diterima'::text
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) < validasipoobatdetail_t.qty_input THEN 'Belum Semua Diterima'::text
            WHEN (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = validasipoobatdetail_t.qty_input THEN 'Sudah Diterima'::text
            ELSE 'Belum Diterima'::text
        END AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE validasipoobat_t.tgl_batal_po
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    validasipoobat_t.tgl_validasi AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr,
        CASE
            WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipoobatdetail_t.discount_rp
        END AS deduction_rupiah,
    supplier_m.supplier_id
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.obatalkes_id,
            a.validasipoobatdetail_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_penerimaan,
            a.qty_retur,
            a.qty_sisa,
            a.harga,
            a.discount,
            a.discount_rp,
            a.is_deleted
           FROM validasipoobatdetail_t a) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.status
           FROM purchasereqdetail_t a) purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereq_t a) purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN ( SELECT a.obatalkes_id,
            a.manufaktur_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.is_deleted
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama,
            a.supplier_kode
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.validasipoobatdetail_id,
                    max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t a1
                  GROUP BY a1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.penerimaanobat_id,
            a.validasipoobat_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanobat_t a
             JOIN ( SELECT a1.validasipoobat_id,
                    max(a1.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t a1
                  GROUP BY a1.validasipoobat_id) max_pen ON a.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
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
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipobarangdetail_t.harga
        END AS price,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipobarangdetail_t.discount::double precision
        END AS deduction_percent,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE pajak_m.pajak_persen::double precision
        END AS addition_percent,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp
        END AS gross_amount,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * validasipobarang_t.ppn_persen::double precision / 100::double precision
        END AS nett_amount,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 'Dibatalkan'::text
            ELSE btrim(fgetnamalookup(validasipobarang_t.status_penerimaan)::text)
        END AS status_po,
    btrim(validasipobarang_t.catatan1) AS catatan_1,
    btrim(validasipobarang_t.catatan2) AS catatan_2,
    purchasereqbrg_t.is_prcyto AS cyto,
        CASE purchasereqbrg_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipobarang_t.catatan IS NOT NULL THEN validasipobarang_t.last_modified_date
            ELSE validasipobarang_t.tgl_batal_po
        END AS reject_date,
    btrim(validasipobarang_t.catatan) AS reject_remarks,
    btrim(penerimaanbarang_t.no_penerimaan::text) AS no_penerimaan,
    validasipobarang_t.tgl_validasi AS tanggal_penerimaan,
    purchasereqbrg_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status_pr,
        CASE
            WHEN validasipobarangdetail_t.is_deleted = true THEN 0::double precision
            ELSE validasipobarangdetail_t.discount_rp
        END AS deduction_rupiah,
    supplier_m.supplier_id
   FROM validasipobarangdetail_t
     JOIN ( SELECT a.validasipobarang_id,
            a.supplier_id,
            a.pajak_id,
            a.created_date,
            a.is_validasi,
            a.tgl_validasi,
            a.no_pobarang,
            a.ppn_persen,
            a.status_penerimaan,
            a.catatan1,
            a.catatan2,
            a.catatan,
            a.last_modified_date,
            a.tgl_batal_po
           FROM validasipobarang_t a) validasipobarang_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN ( SELECT a.purchasereqbrgdetail_id,
            a.purchasereqbrg_id,
            a.status
           FROM purchasereqbrgdetail_t a) purchasereqbrgdetail_t ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id
     LEFT JOIN ( SELECT a.purchasereqbrg_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereqbrg_t a) purchasereqbrg_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     JOIN ( SELECT a.barang_id,
            a.barang_nama,
            a.manufaktur_id,
            a.barang_kode
           FROM barang_m a) barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipobarangdetail_id,
            a.penerimaanbarangdetail_id,
            a.barang_id,
            a.s_konversibrg_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanbarangdetail_t a
             JOIN ( SELECT a1.validasipobarangdetail_id,
                    max(a1.penerimaanbarangdetail_id) AS penerimaanbarangdetail_id
                   FROM penerimaanbarangdetail_t a1
                  GROUP BY a1.validasipobarangdetail_id) max_det ON a.penerimaanbarangdetail_id = max_det.penerimaanbarangdetail_id
          WHERE a.is_deleted = false) penerimaanbarangdetail_t ON validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id
     LEFT JOIN ( SELECT a.penerimaanbarang_id,
            a.validasipobarang_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanbarang_t a
             JOIN ( SELECT a1.validasipobarang_id,
                    max(a1.penerimaanbarang_id) AS penerimaanbarang_id
                   FROM penerimaanbarang_t a1
                  GROUP BY a1.validasipobarang_id) max_pen ON a.penerimaanbarang_id = max_pen.penerimaanbarang_id) penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     LEFT JOIN ( SELECT a.satuankonversibrg_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversibrg_m a) satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON barang_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT penerimaanbarang_detail.validasipobarangdetail_id,
            sum(a.qty_input) AS qty_retur
           FROM returpenerimaanbarangdetail_t a
             LEFT JOIN ( SELECT a1.penerimaanbarangdetail_id,
                    a1.validasipobarangdetail_id
                   FROM penerimaanbarangdetail_t a1) penerimaanbarang_detail ON penerimaanbarang_detail.penerimaanbarangdetail_id = a.penerimaanbarangdetail_id
          GROUP BY penerimaanbarang_detail.validasipobarangdetail_id) returdetailjumlah ON validasipobarangdetail_t.validasipobarangdetail_id = returdetailjumlah.validasipobarangdetail_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220407_072458_migrate_hotfix_laporanallpo_v_outstandingpo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_072458_migrate_hotfix_laporanallpo_v_outstandingpo cannot be reverted.\n";

        return false;
    }
    */
}
