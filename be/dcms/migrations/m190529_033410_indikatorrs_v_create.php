<?php

use yii\db\Migration;

/**
 * Class m190529_033410_indikatorrs_v_create
 */
class m190529_033410_indikatorrs_v_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists indikatorrs_v;');

        $this->execute('CREATE OR REPLACE VIEW indikatorrs_v AS 
 SELECT indikatorrs_r.bulan,
    indikatorrs_r.tahun,
    round(indikatorrs_r.hari_perawatan::numeric * 100::numeric / (indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric), 2) AS bor,
    round(indikatorrs_r.lama_dirawat::numeric * 100::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS avlos,
    round((indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric - indikatorrs_r.hari_perawatan::numeric) / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS toi,
    round(indikatorrs_r.jumlah_pasien_keluar::numeric / indikatorrs_r.jumlah_tempat_tidur::numeric, 2) AS bto,
    round(indikatorrs_r.pasien_mati_48_jam::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS ndr,
    round(indikatorrs_r.pasien_mati_all::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS gdr
   FROM indikatorrs_r;

');

        $this->execute('ALTER TABLE indikatorrs_v
  OWNER TO postgres;');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190529_033410_indikatorrs_v_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190529_033410_indikatorrs_v_create cannot be reverted.\n";

        return false;
    }
    */
}
