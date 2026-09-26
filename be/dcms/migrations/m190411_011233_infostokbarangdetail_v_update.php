<?php

use yii\db\Migration;

/**
 * Class m190411_011233_infostokbarangdetail_v_update
 */
class m190411_011233_infostokbarangdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW infostokbarangdetail_v;
        ');

        $this->execute("
    CREATE OR REPLACE VIEW infostokbarangdetail_v AS 
 SELECT proses.barang_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok_sistem,
    proses.barang_nama,
    proses.nobatch,
    proses.tglkadaluarsa,
    proses.barang_harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokbarang_id,
    proses.tglperiodestok_awal,
    proses.tglperiodestok_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_barang_id,
    proses.sop_sopbarangdetail_id,
    proses.id_stok
   FROM ( SELECT
                CASE
                    WHEN stokbarang_t.stokbarangasal_id IS NULL THEN stokbarang_t.stokbarang_id
                    ELSE stokbarang_t.stokbarangasal_id
                END AS id_stok,
            stokbarang_t.barang_id,
            stokbarang_t.qtystok_in,
            stokbarang_t.qtystok_out,
            stokbarang_t.nobatch,
            stokbarang_t.tglkadaluarsa,
            barang_m.barang_nama,
            barang_m.barang_harganetto,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokbarang_r.periodestokbarang_id,
            periodestokbarang_m.tglperiodestok_awal,
            periodestokbarang_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formsobarangdetail_t.barang_id AS sop_barang_id,
            formsobarangdetail_t.stokopnamebarangdetail_id AS sop_sopbarangdetail_id
           FROM stokbarang_t
             JOIN barang_m ON stokbarang_t.barang_id = barang_m.barang_id
             JOIN ruangan_m ON stokbarang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formsobarangdetail_t ON stokbarang_t.barang_id = formsobarangdetail_t.barang_id AND stokbarang_t.ruangan_id = formsobarangdetail_t.ruangan_id
             JOIN stokbarang_r ON stokbarang_t.barang_id = stokbarang_t.barang_id AND stokbarang_t.ruangan_id = stokbarang_r.ruangan_id
             LEFT JOIN periodestokbarang_m ON stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id
          WHERE stokbarang_t.stokbarang_aktif = true AND (formsobarangdetail_t.barang_id IS NOT NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NOT NULL OR formsobarangdetail_t.barang_id IS NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NULL)) proses
  GROUP BY proses.barang_id, proses.barang_nama, proses.nobatch, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.barang_harganetto, proses.periodestokbarang_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_barang_id, proses.sop_sopbarangdetail_id, proses.id_stok;

               ");
        
        $this->execute('
             ALTER TABLE infostokbarangdetail_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_011233_infostokbarangdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_011233_infostokbarangdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
