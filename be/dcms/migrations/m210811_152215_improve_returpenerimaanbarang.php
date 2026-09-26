<?php

use yii\db\Migration;

/**
 * Class m210811_152215_improve_returpenerimaanbarang
 */
class m210811_152215_improve_returpenerimaanbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."inforeturpenerimaanbarangdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforeturpenerimaanbarangdetail_v\" AS  SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarang_t.no_penerimaan
            ELSE penerimaaan_supp.no_penerimaan
        END AS no_penerimaan,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarang_t.no_faktur
            ELSE penerimaaan_supp.no_faktur
        END AS no_faktur,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN supplier_m.supplier_nama
            ELSE penerimaaan_supp.supplier_nama
        END AS supplier_nama,
    barang_m.barang_nama,
    satuanunit_m.satuanunit_nama,
    COALESCE(returpenerimaanbarangdetail_t.qty_retur) AS qty_retur,
    COALESCE(penerimaanbarangdetail_t.qty_diterima) AS qty_diterima,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    returpenerimaanbarangdetail_t.tgl_kadaluarsa,
    returpenerimaanbarang_t.alasan_retur,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id IS NULL THEN penerimaanbarangdetail_t.no_batch
            ELSE penerimaaan_supp.no_batch
        END AS no_batch,
    validasipobarang_t.no_pobarang,
    returpenerimaanbarangdetail_t.returpenerimaanbarangdetail_id,
    barang_m.barang_id,
    penerimaanbarangdetail_t.penerimaanbarangdetail_id,
    returpenerimaanbarangdetail_t.qty_input,
        CASE
            WHEN returpenerimaanbarangdetail_t.penerimaanbarang_id IS NOT NULL THEN penerimaanbarangdetail_t.qty_diterima
            ELSE penerimaaan_supp.qty_besar
        END AS qty_besar,
    penerimaaan_supp.qty_besar - COALESCE(returpenerimaanbarangdetail_t.qty_retur::bigint, 0::bigint) AS qty_sisa
   FROM returpenerimaanbarang_t
     JOIN returpenerimaanbarangdetail_t ON returpenerimaanbarang_t.returpenerimaanbarang_id = returpenerimaanbarangdetail_t.returpenerimaanbarang_id
     LEFT JOIN penerimaanbarang_t ON returpenerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
     LEFT JOIN penerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_t.penerimaanbarangdetail_id
     LEFT JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
     JOIN barang_m ON barang_m.barang_id = returpenerimaanbarangdetail_t.barang_id
     JOIN satuanunit_m ON satuanunit_m.satuanunit_id = returpenerimaanbarangdetail_t.satuanbesar_id
     LEFT JOIN supplier_m ON penerimaanbarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
            sum(returpenerimaanbarangdetail_t_1.qty_retur) AS on_retur
           FROM returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1
          GROUP BY returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id) returdetailjumlah ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id
     LEFT JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.no_faktur,
            penerimaansuppdetail_t.no_batch,
            penerimaansuppdetail_t.qty_besar,
            supplier_m_1.supplier_nama
           FROM penerimaansuppdetail_t
             LEFT JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             LEFT JOIN supplier_m supplier_m_1 ON penerimaansupp_t.supplier_id = supplier_m_1.supplier_id) penerimaaan_supp ON penerimaaan_supp.penerimaansuppdetail_id = returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210811_152215_improve_returpenerimaanbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210811_152215_improve_returpenerimaanbarang cannot be reverted.\n";

        return false;
    }
    */
}
