<?php

use yii\db\Migration;

/**
 * Class m210301_063207_migrate_20210301_laporanpooutstanding_v
 */
class m210301_063207_migrate_20210301_laporanpooutstanding_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpooutstanding_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpooutstanding_v\" AS  SELECT pr.no_pr,
    validasipoobat_t.no_poobat AS no_po,
    validasipoobat_t.created_date AS tgl_po_dibuat,
    validasipoobat_t.tgl_validasi,
    supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    obatalkes_m.obatalkes_kode AS kode_item,
    obatalkes_m.obatalkes_nama AS nama_item,
    validasipoobatdetail_t.qty_input AS qty_po,
    kecil.satuanunit_nama AS satuan_kecil,
    besar.satuanunit_nama AS satuan_besar,
    validasipoobatdetail_t.qty_po AS qty,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_penerimaan,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount,
    validasipoobatdetail_t.discount_rp,
    validasipoobat_t.ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS sub_total,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS total,
    validasipoobat_t.catatan1,
    validasipoobat_t.catatan2,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS uom,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS status,
    pr.tgl_pr,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    jenisobatalkes_m.jenisobatalkes_nama,
    manufaktur_m.nama,
    manufaktur_m.nama AS manufaktur_nama
   FROM validasipoobat_t
     JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN ( SELECT pen_det.validasipoobatdetail_id,
            pen_det.penerimaanobatdetail_id,
            pen_det.po_balance
           FROM penerimaanobatdetail_t pen_det
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON pen_det.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE pen_det.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT purchasereq_t.no_pr,
            purchasereq_t.tgl_pr::timestamp without time zone AS tgl_pr,
            validasipoobatdetail_t_1.validasipoobat_id,
            purchasereqdetail_t.qty_input,
            kecil_1.satuanunit_nama AS satuan,
            besar_1.satuanunit_nama AS satuan_konversi,
            purchasereqdetail_t.obatalkes_id
           FROM purchasereq_t
             JOIN purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
             JOIN validasipoobatdetail_t validasipoobatdetail_t_1 ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id
             LEFT JOIN satuanunit_m kecil_1 ON purchasereqdetail_t.satuan_id = kecil_1.satuanunit_id
             LEFT JOIN satuanunit_m besar_1 ON purchasereqdetail_t.satuankonversi_id = besar_1.satuanunit_id
          WHERE purchasereq_t.is_deleted = false
          GROUP BY purchasereq_t.no_pr, purchasereq_t.tgl_pr, validasipoobatdetail_t_1.validasipoobat_id, purchasereqdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqdetail_t.obatalkes_id) pr ON validasipoobat_t.validasipoobat_id = pr.validasipoobat_id AND validasipoobatdetail_t.obatalkes_id = pr.obatalkes_id
  WHERE (validasipoobat_t.status_penerimaan <> ALL (ARRAY[574, 580])) AND validasipoobat_t.is_validasi = true;");

        $this->execute('ALTER TABLE "public"."laporanpooutstanding_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210301_063207_migrate_20210301_laporanpooutstanding_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210301_063207_migrate_20210301_laporanpooutstanding_v cannot be reverted.\n";

        return false;
    }
    */
}
