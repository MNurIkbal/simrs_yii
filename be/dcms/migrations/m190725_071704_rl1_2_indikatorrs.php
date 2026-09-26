<?php

use yii\db\Migration;

/**
 * Class m190725_071704_rl1_2_indikatorrs
 */
class m190725_071704_rl1_2_indikatorrs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP VIEW public.rl1_2_indikatorrs;
        ');

        $this->execute("
         CREATE OR REPLACE VIEW public.rl1_2_indikatorrs AS 
 SELECT x.tahun,
    round(sum(x.bor) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bor,
    round(sum(x.avlos) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS avlos,
    round(sum(x.toi) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS toi,
    round(sum(x.bto) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS bto,
    round(sum(x.ndr) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS ndr,
    round(sum(x.gdr) / count(to_char(x.created_date::timestamp with time zone, 'YYYY'::text))::numeric, 2) AS gdr,
    x.avg_pasienrj AS avg_kunjungan
   FROM ( SELECT to_char(indikatorrs_r.created_date, 'YYYY-MM-DD'::text)::date AS created_date,
            to_char(indikatorrs_r.created_date, 'MM'::text) AS bulan,
            to_char(indikatorrs_r.created_date, 'YYYY'::text) AS tahun,
            round(indikatorrs_r.hari_perawatan::numeric * 1::numeric / (indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric), 2) AS bor,
            round(indikatorrs_r.lama_dirawat::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS avlos,
            round((indikatorrs_r.jumlah_tempat_tidur::numeric * indikatorrs_r.jumlah_hari_periode::numeric - indikatorrs_r.hari_perawatan::numeric) / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS toi,
            indikatorrs_r.jumlah_pasien_keluar::numeric / indikatorrs_r.jumlah_tempat_tidur::numeric AS bto,
            round(indikatorrs_r.pasien_mati_48_jam::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS ndr,
            round(indikatorrs_r.pasien_mati_all::numeric * 10::numeric / indikatorrs_r.jumlah_pasien_keluar::numeric, 2) AS gdr,
            avg_rj.total AS avg_pasienrj
           FROM indikatorrs_r
             LEFT JOIN ( SELECT (count(x_1.pendaftaran_id)::double precision / x_1.periode)::numeric(15,2) AS total,
                    x_1.tahun,
                    x_1.bulan,
                    x_1.periode
                   FROM ( SELECT pendaftaran_t.pendaftaran_id,
                            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
                            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
                            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
                            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
                            date_part('days'::text, date_trunc('month'::text, pendaftaran_t.tgl_pendaftaran::date::timestamp with time zone) + '1 mon'::interval - '1 day'::interval) AS periode
                           FROM pendaftaran_t
                          WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text
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
                          WHERE ruangan_m.instalasi_id = 1 AND pendaftaran_t.is_deleted = false) x_1
                  GROUP BY x_1.tahun, x_1.bulan, x_1.periode) avg_rj ON avg_rj.bulan = indikatorrs_r.bulan::text) x
  GROUP BY x.tahun, x.avg_pasienrj;
        ");

        $this->execute('
         ALTER TABLE public.rl1_2_indikatorrs
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_071704_rl1_2_indikatorrs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_071704_rl1_2_indikatorrs cannot be reverted.\n";

        return false;
    }
    */
}
