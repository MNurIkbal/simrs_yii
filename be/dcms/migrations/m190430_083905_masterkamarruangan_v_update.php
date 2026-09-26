<?php

use yii\db\Migration;

/**
 * Class m190430_083905_masterkamarruangan_v_update
 */
class m190430_083905_masterkamarruangan_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW masterkamarruangan_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW masterkamarruangan_v AS 
 SELECT kamarruangan_m.ruangan_id,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamarruangan_m.kamarruangan_jenis,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS jenis_kamar,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    NULL::unknown AS status_isi,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.is_dashboard,
    kamarruangan_m.is_active
   FROM kamarruangan_m
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id AND jeniskasuspenyakit_m.is_deleted = false
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
  WHERE kamarruangan_m.is_deleted = false
  ORDER BY jeniskasuspenyakit_m.jeniskasuspenyakit_id, ruangan_m.ruangan_id;

        ');

        $this->execute('
      ALTER TABLE masterkamarruangan_v
        OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_083905_masterkamarruangan_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_083905_masterkamarruangan_v_update cannot be reverted.\n";

        return false;
    }
    */
}
