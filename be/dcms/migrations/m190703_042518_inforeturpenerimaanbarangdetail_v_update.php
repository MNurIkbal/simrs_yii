<?php

use yii\db\Migration;

/**
 * Class m190703_042518_inforeturpenerimaanbarangdetail_v_update
 */
class m190703_042518_inforeturpenerimaanbarangdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
             $this->execute('
   DROP VIEW IF exists public.inforeturpenerimaanbarangdetail_v;
        ');

              $this->execute('
   CREATE OR REPLACE VIEW public.inforeturpenerimaanbarangdetail_v AS 
 SELECT returpenerimaanbarang_t.returpenerimaanbarang_id,
    returpenerimaanbarang_t.tgl_retur,
    returpenerimaanbarang_t.no_returpenerimaanbarang,
    penerimaanbarang_t.no_penerimaan,
    penerimaanbarang_t.no_faktur,
    supplier_m.supplier_nama,
    barang_m.barang_nama,
    satuanunit_m.satuanunit_nama,
    COALESCE(returpenerimaanbarangdetail_t.qty_retur) AS qty_retur,
    COALESCE(penerimaanbarangdetail_t.qty_diterima) AS qty_diterima,
    COALESCE(returdetailjumlah.on_retur, 0::bigint) AS on_retur,
    returpenerimaanbarangdetail_t.tgl_kadaluarsa,
    returpenerimaanbarang_t.alasan_retur,
    penerimaanbarangdetail_t.no_batch,
    validasipobarang_t.no_pobarang,
    returpenerimaanbarangdetail_t.returpenerimaanbarangdetail_id,
    barang_m.barang_id,
    penerimaanbarangdetail_t.penerimaanbarangdetail_id,
    returpenerimaanbarangdetail_t.qty_input
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
          GROUP BY returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id) returdetailjumlah ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id;

        ');

               $this->execute('
   ALTER TABLE public.inforeturpenerimaanbarangdetail_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_042518_inforeturpenerimaanbarangdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_042518_inforeturpenerimaanbarangdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
