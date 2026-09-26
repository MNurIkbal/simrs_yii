<?php

use yii\db\Migration;

/**
 * Class m190411_010512_rekomendasiorderbarang_v_update
 */
class m190411_010512_rekomendasiorderbarang_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DROP VIEW rekomendasiorderbarang_v;
        ');

        $this->execute("
      CREATE OR REPLACE VIEW rekomendasiorderbarang_v AS 
 SELECT periodestokbarang_m.periodestokbarang_id AS periodestok_id,
    periodestokbarang_m.periodestokbarang_nama AS periodestok_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokbarang_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokbarang_r.barang_id,
    barang_m.barang_nama,
    barang_m.barang_kode,
    barang_m.nilai_ro,
    barang_m.min_order,
    barang_m.max_order,
    barang_m.on_ro,
    barang_m.on_po,
    stokbarang_r.qty_tersedia AS sisa_stok,
    stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po AS stok,
    barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po) AS ro_stok,
        CASE
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) = barang_m.min_order THEN barang_m.min_order
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) > barang_m.max_order AND barang_m.max_order = 0 THEN barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)
            WHEN barang_m.min_order = 0 AND barang_m.max_order = 0 THEN barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) < barang_m.min_order THEN barang_m.min_order
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) > barang_m.max_order THEN barang_m.max_order
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) > barang_m.min_order THEN barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)
            WHEN barang_m.min_order = 0 THEN barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)
            WHEN (barang_m.nilai_ro - (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po)) = 0 THEN barang_m.min_order
            ELSE 0
        END AS rekomendasi
   FROM stokbarang_r
     JOIN ruangan_m ON stokbarang_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN barang_m ON stokbarang_r.barang_id = barang_m.barang_id
     LEFT JOIN periodestokbarang_m ON stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id
  WHERE barang_m.is_active = true AND barang_m.is_deleted = false AND stokbarang_r.is_periode = true AND barang_m.nilai_ro > (stokbarang_r.qty_tersedia + barang_m.on_ro + barang_m.on_po);

               ");
        
        $this->execute('
       ALTER TABLE rekomendasiorderbarang_v
        OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_010512_rekomendasiorderbarang_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_010512_rekomendasiorderbarang_v_update cannot be reverted.\n";

        return false;
    }
    */
}
