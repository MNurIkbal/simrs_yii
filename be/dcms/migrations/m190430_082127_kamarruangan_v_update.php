<?php

use yii\db\Migration;

/**
 * Class m190430_082127_kamarruangan_v_update
 */
class m190430_082127_kamarruangan_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('
       DROP VIEW kamarruangan_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW kamarruangan_v AS 
 SELECT kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.kamarruangan_id,
    kamarruangan_m.ruangan_id,
    kasuspenyakitruangan_mp.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
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
    kamarruangan_m.jeniskasuspenyakit_id AS jkpkamar_id,
    jkpkamar.jeniskasuspenyakit_nama AS jkpkamar_nama,
    ( SELECT pasienri.jeniskelamin
           FROM infopasienri_v pasienri
          WHERE pasienri.kamarruangan_id = kamarruangan_m.kamarruangan_id AND pasienri.pasienpulang_id IS NULL AND (pasienri.status_ranap = ANY (ARRAY[440, 441]))
         LIMIT 1) AS isi_jk
   FROM kamartempattidur_m
     JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id AND kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN kasuspenyakitruangan_mp ON ruangan_m.ruangan_id = kasuspenyakitruangan_mp.ruangan_id AND kasuspenyakitruangan_mp.is_deleted = false
     JOIN jeniskasuspenyakit_m ON kasuspenyakitruangan_mp.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id AND jeniskasuspenyakit_m.is_deleted = false
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
     JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id AND kettempattidur_m.is_deleted = false
     LEFT JOIN jeniskasuspenyakit_m jkpkamar ON kamarruangan_m.jeniskasuspenyakit_id = jkpkamar.jeniskasuspenyakit_id AND jkpkamar.is_deleted = false
  WHERE kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true
  ORDER BY jeniskasuspenyakit_m.jeniskasuspenyakit_id, ruangan_m.ruangan_id;
        ');

        $this->execute('
        ALTER TABLE kamarruangan_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_082127_kamarruangan_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_082127_kamarruangan_v_update cannot be reverted.\n";

        return false;
    }
    */
}
