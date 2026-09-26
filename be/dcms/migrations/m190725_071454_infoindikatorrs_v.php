<?php

use yii\db\Migration;

/**
 * Class m190725_071454_infoindikatorrs_v
 */
class m190725_071454_infoindikatorrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          DROP VIEW if exists public.infoindikatorrs_v;
        ');

          $this->execute("
          CREATE OR REPLACE VIEW public.infoindikatorrs_v AS 
 SELECT indikatorrs_r.bulan,
    indikatorrs_r.tahun,
    round(indikatorrs_r.hari_perawatan::numeric * 1::numeric / (indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric), 2) AS bor,
    round(indikatorrs_r.lama_dirawat::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS avlos,
    round((indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric - indikatorrs_r.hari_perawatan::numeric) / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS toi,
    round(indikatorrs_r.jumlah_pasien_keluar::numeric / indikatorrs_r.jumlah_tempat_tidur::numeric, 2) AS bto,
    round(indikatorrs_r.pasien_mati_48_jam::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS ndr,
    round(indikatorrs_r.pasien_mati_all::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS gdr,
    indikatorrs_r.hari_perawatan,
    indikatorrs_r.lama_dirawat,
    indikatorrs_r.jumlah_tempat_tidur,
    avg_rj.total AS avg_pasienrj
   FROM indikatorrs_r
     LEFT JOIN ( SELECT (count(x.pendaftaran_id)::double precision / x.periode)::numeric(15,2) AS total,
            x.tahun,
            x.bulan,
            x.periode
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
                    date_part('days'::text, date_trunc('month'::text, pendaftaran_t.tgl_pendaftaran::date::timestamp with time zone) + '1 mon'::interval - '1 day'::interval) AS periode
                   FROM pendaftaran_t
                  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false
                UNION
                 SELECT pendaftaran_t.pendaftaran_id,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
                    to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
                    date_part('days'::text, date_trunc('month'::text, pendaftaran_t.tgl_pendaftaran::date::timestamp with time zone) + '1 mon'::interval - '1 day'::interval) AS periode
                   FROM pendaftaran_t
                     JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id AND konsulpoli_t.is_deleted = false
                     JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
                  WHERE ruangan_m.instalasi_id = 1 AND pendaftaran_t.is_deleted = false) x
          GROUP BY x.tahun, x.bulan, x.periode) avg_rj ON avg_rj.bulan = indikatorrs_r.bulan::text;
        ");

           $this->execute('
         ALTER TABLE public.infoindikatorrs_v
        OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_071454_infoindikatorrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_071454_infoindikatorrs_v cannot be reverted.\n";

        return false;
    }
    */
}
