<?php

use yii\db\Migration;

/**
 * Class m190408_041007_rekomendasiorderobat_v_update
 */
class m190408_041007_rekomendasiorderobat_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW rekomendasiorderobat_v;
        ');

        $this->execute("
        CREATE OR REPLACE VIEW rekomendasiorderobat_v AS 
 SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
    periodestokobat_m.periodestok_nama,
    instalasi_m.instalasi_nama,
    stokobatalkes_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokobatalkes_r.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.nilai_ro,
    stokobatalkes_r.qty_tersedia AS sisa_stok,
    stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po AS stok,
    obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po) AS ro_stok,
        CASE
            WHEN (obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)) = obatalkes_m.min_order THEN obatalkes_m.min_order
            WHEN (obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)) > obatalkes_m.max_order AND obatalkes_m.max_order = 0 THEN obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)
            WHEN obatalkes_m.min_order = 0 AND obatalkes_m.max_order = 0 THEN obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)
            WHEN (obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)) < obatalkes_m.min_order THEN obatalkes_m.min_order
            WHEN (obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)) > obatalkes_m.max_order THEN obatalkes_m.max_order
            WHEN (obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)) > obatalkes_m.min_order THEN obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)
            WHEN obatalkes_m.min_order = 0 THEN obatalkes_m.nilai_ro - (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po)
            ELSE 0
        END AS rekomendasi,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.on_ro,
    obatalkes_m.on_po
   FROM stokobatalkes_r
     JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true AND stokobatalkes_r.ruangan_id = 25 AND obatalkes_m.nilai_ro > (stokobatalkes_r.qty_tersedia + obatalkes_m.on_ro + obatalkes_m.on_po);

               ");
        
        $this->execute('
       ALTER TABLE rekomendasiorderobat_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_041007_rekomendasiorderobat_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_041007_rekomendasiorderobat_v_update cannot be reverted.\n";

        return false;
    }
    */
}
