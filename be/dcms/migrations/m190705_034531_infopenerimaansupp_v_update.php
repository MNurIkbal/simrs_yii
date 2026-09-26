<?php

use yii\db\Migration;

/**
 * Class m190705_034531_infopenerimaansupp_v_update
 */
class m190705_034531_infopenerimaansupp_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW public.infopenerimaansupp_v;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.infopenerimaansupp_v AS 
 SELECT penerimaansupp_t.penerimaansupp_id,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.tgl_penerimaan,
    penerimaansupp_t.no_faktur,
    penerimaansupp_t.supplier_id,
    supplier_m.supplier_nama,
    penerimaansupp_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
    penerimaansupp_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
    penerimaansupp_t.ruanganpenerima_id,
    ruangan_m.ruangan_nama,
    concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
    payterm_m.payterm_nama
   FROM penerimaansupp_t
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
     LEFT JOIN payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id;
        ");

        $this->execute('
           ALTER TABLE public.infopenerimaansupp_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190705_034531_infopenerimaansupp_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190705_034531_infopenerimaansupp_v_update cannot be reverted.\n";

        return false;
    }
    */
}
