<?php

use yii\db\Migration;

/**
 * Class m190614_071719_instalasi_v_update
 */
class m190614_071719_instalasi_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
     {
        $this->execute('
    DROP VIEW instalasi_v;
        ');
        

        $this->execute('
  CREATE OR REPLACE VIEW instalasi_v AS 
 SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    instalasi_m.instalasi_singkatan,
    instalasi_m.profilers_id,
    profilrumahsakit_m.nama_rumahsakit,
    instalasi_m.is_sync,
    instalasi_m.is_active,
    instalasi_m.is_deleted
   FROM instalasi_m
     LEFT JOIN profilrumahsakit_m ON instalasi_m.profilers_id = profilrumahsakit_m.profilrs_id
  WHERE instalasi_m.is_active = true AND instalasi_m.is_deleted = false;

        ');

        $this->execute('
ALTER TABLE instalasi_v
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190614_071719_instalasi_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190614_071719_instalasi_v_update cannot be reverted.\n";

        return false;
    }
    */
}
