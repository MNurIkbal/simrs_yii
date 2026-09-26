<?php

use yii\db\Migration;

/**
 * Class m190719_093409_ruanganpelayanan_v
 */
class m190719_093409_ruanganpelayanan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP VIEW if exists public.ruanganpelayanan_v;
        ');

          $this->execute('
         CREATE OR REPLACE VIEW public.ruanganpelayanan_v AS 
 SELECT ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelasruangan_mp.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama
   FROM ruangan_m
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kasuspenyakitruangan_mp ON ruangan_m.ruangan_id = kasuspenyakitruangan_mp.ruangan_id
     JOIN jeniskasuspenyakit_m ON kasuspenyakitruangan_mp.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelasruangan_mp ON ruangan_m.ruangan_id = kelasruangan_mp.ruangan_id
     JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = kelasruangan_mp.kelaspelayanan_id;
        ');

           $this->execute('
         ALTER TABLE public.ruanganpelayanan_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_093409_ruanganpelayanan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_093409_ruanganpelayanan_v cannot be reverted.\n";

        return false;
    }
    */
}
