<?php

use yii\db\Migration;

/**
 * Class m190430_083621_kamar_v_update
 */
class m190430_083621_kamar_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW kamar_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW kamar_v AS 
 SELECT kamarruangan_m.kamarruangan_id,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamarruangan_m.kamarruangan_jenis,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS jenis_kamar,
    kamarruangan_m.is_active,
    kamartempattidur_m.status_isi
   FROM kamarruangan_m
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
     LEFT JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id AND jeniskasuspenyakit_m.is_deleted = false
     LEFT JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = false
  WHERE kamarruangan_m.is_deleted = false;

        ');

        $this->execute('
       ALTER TABLE kamar_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_083621_kamar_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_083621_kamar_v_update cannot be reverted.\n";

        return false;
    }
    */
}
