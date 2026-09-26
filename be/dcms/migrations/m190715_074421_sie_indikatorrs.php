<?php

use yii\db\Migration;

/**
 * Class m190715_074421_sie_indikatorrs
 */
class m190715_074421_sie_indikatorrs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW if exists public.sie_indikatorrs;
        ');


        $this->execute("
          CREATE OR REPLACE VIEW public.sie_indikatorrs AS 
 SELECT x.tahun,
    round(sum(x.bor) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bor,
    round(sum(x.avlos) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS avlos,
    round(sum(x.toi) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS toi,
    round(sum(x.bto) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bto
   FROM ( SELECT to_char(indikatorrs_r.created_date, 'YYYY-MM-DD'::text)::date AS created_date,
            to_char(indikatorrs_r.created_date, 'MM'::text) AS bulan,
            to_char(indikatorrs_r.created_date, 'YYYY'::text) AS tahun,
            round(indikatorrs_r.hari_perawatan::numeric * 1::numeric / (indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric), 2) AS bor,
            round(indikatorrs_r.lama_dirawat::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS avlos,
            round((indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric - indikatorrs_r.hari_perawatan::numeric) / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS toi,
            round(indikatorrs_r.jumlah_pasien_keluar::numeric / indikatorrs_r.jumlah_tempat_tidur::numeric, 2) AS bto,
            round(indikatorrs_r.pasien_mati_48_jam::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS ndr,
            round(indikatorrs_r.pasien_mati_all::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS gdr
           FROM indikatorrs_r) x
  WHERE to_char(x.created_date::timestamp with time zone, 'YYYY'::text) = date_part('year'::text, CURRENT_DATE)::text
  GROUP BY x.tahun;
        ");


        $this->execute('
          ALTER TABLE public.sie_indikatorrs
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_074421_sie_indikatorrs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_074421_sie_indikatorrs cannot be reverted.\n";

        return false;
    }
    */
}
