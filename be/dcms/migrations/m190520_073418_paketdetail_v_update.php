<?php

use yii\db\Migration;

/**
 * Class m190520_073418_paketdetail_v_update
 */
class m190520_073418_paketdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
     {
        $this->execute('
     DROP VIEW paketdetail_v;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW paketdetail_v AS 
 SELECT tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    daftartindakan_m.daftartindakan_nama,
    paketpelayanan_mp.daftartindakan_id,
    paketpelayanan_mp.is_deleted,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama
   FROM tipepaket_m
     JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id;
        ');

        $this->execute('
ALTER TABLE paketdetail_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190520_073418_paketdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_073418_paketdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
