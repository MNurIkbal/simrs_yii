<?php

use yii\db\Migration;

/**
 * Class m190510_025745_partografpemantauan_v_create
 */
class m190510_025745_partografpemantauan_v_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW if exists partografpemantauan_v;
        ');

        $this->execute('
    CREATE OR REPLACE VIEW partografpemantauan_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    persalinandetail_t.persalinandetail_id,
    persalinandetail_t.persalinan_id,
    persalinandetail_t.jam_ke,
    persalinandetail_t.waktu,
    persalinandetail_t.td_systolic,
    persalinandetail_t.td_diastolic,
    persalinandetail_t.detak_nadi,
    persalinandetail_t.suhu,
    persalinandetail_t.tinggi_fundus,
    persalinandetail_t.kontraksi_uterus,
    persalinandetail_t.kandung_kemih,
    persalinandetail_t.darah_keluar
   FROM pendaftaran_t
     JOIN persalinandetail_t ON pendaftaran_t.pendaftaran_id = persalinandetail_t.pendaftaran_id AND persalinandetail_t.is_deleted = false;
        ');

        $this->execute('
 ALTER TABLE partografpemantauan_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190510_025745_partografpemantauan_v_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_025745_partografpemantauan_v_create cannot be reverted.\n";

        return false;
    }
    */
}
