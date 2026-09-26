<?php

use yii\db\Migration;

/**
 * Class m190430_081819_tempattidur_v_update
 */
class m190430_081819_tempattidur_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DROP VIEW tempattidur_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW tempattidur_v AS 
 SELECT kamarruangan_m.kamarruangan_id,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    kettempattidur_m.kettempattidur_nama,
    kamartempattidur_m.status_isi,
    kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.is_active
   FROM kamarruangan_m
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = false
     LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id AND kettempattidur_m.is_deleted = false
  WHERE kamarruangan_m.is_deleted = false;
        ');

        $this->execute('
          ALTER TABLE tempattidur_v
        OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_081819_tempattidur_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_081819_tempattidur_v_update cannot be reverted.\n";

        return false;
    }
    */
}
