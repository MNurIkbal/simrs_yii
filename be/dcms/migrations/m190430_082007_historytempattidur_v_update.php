<?php

use yii\db\Migration;

/**
 * Class m190430_082007_historytempattidur_v_update
 */
class m190430_082007_historytempattidur_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW historytempattidur_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW historytempattidur_v AS 
 SELECT kamartempattidur_r.tthistory_id,
    kamartempattidur_r.tgl_tthistory,
    kamartempattidur_r.ruangan_id,
    ruangan_m.ruangan_nama,
    kamartempattidur_r.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_r.no_tempattidur,
    kamartempattidur_r.is_activehistory AS status,
    kamartempattidur_r.keterangan AS keterangan_id,
    fgetnamalookup(kamartempattidur_r.keterangan::integer) AS keterangan
   FROM kamartempattidur_r
     JOIN ruangan_m ON kamartempattidur_r.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kamarruangan_m ON kamartempattidur_r.kamarruangan_id = kamarruangan_m.kamarruangan_id AND kamarruangan_m.is_deleted = false;


        ');

        $this->execute('
         ALTER TABLE historytempattidur_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_082007_historytempattidur_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_082007_historytempattidur_v_update cannot be reverted.\n";

        return false;
    }
    */
}
