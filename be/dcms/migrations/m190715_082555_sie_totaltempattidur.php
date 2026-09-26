<?php

use yii\db\Migration;

/**
 * Class m190715_082555_sie_totaltempattidur
 */
class m190715_082555_sie_totaltempattidur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          DROP VIEW if exists public.sie_totaltempattidur;
        ');

          $this->execute("
          CREATE OR REPLACE VIEW public.sie_totaltempattidur AS 
 SELECT kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.kamarruangan_id,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamarruangan_m.kamarruangan_jenis,
    kamartempattidur_m.no_tempattidur,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kamartempattidur_m.status_isi,
    kamartempattidur_m.kettempattidur_id,
    kettempattidur_m.kettempattidur_nama,
    kettempattidur_m.kettempattidur_warna,
    kettempattidur_m.kode_warna,
    kamarruangan_m.is_dashboard
   FROM kamartempattidur_m
     JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id AND kamarruangan_m.is_deleted = false
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
     JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id AND kettempattidur_m.is_deleted = false
  WHERE kamartempattidur_m.is_active = true AND kamartempattidur_m.is_deleted = false
  ORDER BY kamarruangan_m.ruangan_id;
        ");

           $this->execute('
         ALTER TABLE public.sie_totaltempattidur
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_082555_sie_totaltempattidur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_082555_sie_totaltempattidur cannot be reverted.\n";

        return false;
    }
    */
}
