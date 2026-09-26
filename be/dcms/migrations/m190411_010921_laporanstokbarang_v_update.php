<?php

use yii\db\Migration;

/**
 * Class m190411_010921_laporanstokbarang_v_update
 */
class m190411_010921_laporanstokbarang_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW laporanstokbarang_v;
        ');

        $this->execute("
     CREATE OR REPLACE VIEW laporanstokbarang_v AS 
 SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
    periodestokobat_m.periodestok_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokbarang_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokbarang_r.barang_id,
    barang_m.barang_nama,
    stokbarang_r.qty_masuk,
    stokbarang_r.qty_keluar,
    stokbarang_r.qty_dipesan,
    stokbarang_r.qty_tersedia,
    stokbarang_r.qty_sisa AS qty_stok,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir
   FROM stokbarang_r
     JOIN ruangan_m ON stokbarang_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN barang_m ON stokbarang_r.barang_id = barang_m.barang_id
     LEFT JOIN periodestokobat_m ON stokbarang_r.periodestokbarang_id = periodestokobat_m.periodestokobat_id
  WHERE stokbarang_r.is_active = true AND stokbarang_r.is_deleted = false;

               ");
        
        $this->execute('
       ALTER TABLE laporanstokbarang_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_010921_laporanstokbarang_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_010921_laporanstokbarang_v_update cannot be reverted.\n";

        return false;
    }
    */
}
