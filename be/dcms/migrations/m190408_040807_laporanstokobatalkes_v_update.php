<?php

use yii\db\Migration;

/**
 * Class m190408_040807_laporanstokobatalkes_v_update
 */
class m190408_040807_laporanstokobatalkes_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW laporanstokobatalkes_v;
        ');

        $this->execute("
        CREATE OR REPLACE VIEW laporanstokobatalkes_v AS 
 SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
    periodestokobat_m.periodestok_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokobatalkes_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokobatalkes_r.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.hargajual,
    obatalkes_m.hargajual * obatalkes_m.ppn_persen / 100::double precision AS ppn,
    stokobatalkes_r.qty_masuk,
    stokobatalkes_r.qty_keluar,
    stokobatalkes_r.qty_dipesan,
    stokobatalkes_r.qty_tersedia,
    stokobatalkes_r.qty_sisa AS qty_stok,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir
   FROM stokobatalkes_r
     JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
  WHERE stokobatalkes_r.is_active = true AND stokobatalkes_r.is_deleted = false AND stokobatalkes_r.is_periode = true;


               ");
        
        $this->execute('
       ALTER TABLE laporanstokobatalkes_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_040807_laporanstokobatalkes_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_040807_laporanstokobatalkes_v_update cannot be reverted.\n";

        return false;
    }
    */
}
