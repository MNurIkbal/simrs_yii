<?php

use yii\db\Migration;

/**
 * Class m190625_074416_sie_10besarbedah
 */
class m190625_074416_sie_10besarbedah extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
    DROP VIEW IF exists public.sie_10besarbedah;
        ');

          $this->execute('
    CREATE OR REPLACE VIEW public.sie_10besarbedah AS 
 SELECT x.tindakan_id,
    x.tindakan_nama AS "Nama Tindakan",
    sum(x.qty_tindakan) AS "Jumlah Tindakan",
    x.tahun_tindakan
   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
            fgetpasien_nama(tindakanpelayanan_t.pasien_id) AS pasien_nama,
            tindakanpelayanan_t.tgl_tindakan,
            to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY\'::text)::character varying AS tahun_tindakan,
            to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text)::date AS tanggal_pelayanan,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_nama,
            jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
            tindakanpelayanan_t.qty_tindakan
           FROM tindakanpelayanan_t
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN jeniskegiatantindakan_m ON daftartindakan_m.jeniskegiatantindakan_id = jeniskegiatantindakan_m.jeniskegiatantindakan_id
          WHERE jeniskegiatantindakan_m.jeniskegiatantindakan_id = 1 AND tindakanpelayanan_t.tipepaket_id IS NULL AND tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT tindakanpelayanan_t.pendaftaran_id,
            fgetpasien_nama(tindakanpelayanan_t.pasien_id) AS pasien_nama,
            tindakanpelayanan_t.tgl_tindakan,
            to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY\'::text)::character varying AS tahun_tindakan,
            to_char(tindakanpelayanan_t.tgl_tindakan, \'YYYY-MM-DD\'::text)::date AS tanggal_pelayanan,
            tindakanpelayanan_t.tipepaket_id AS daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
            tindakanpelayanan_t.qty_tindakan
           FROM tindakanpelayanan_t
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN jeniskegiatantindakan_m ON daftartindakan_m.jeniskegiatantindakan_id = jeniskegiatantindakan_m.jeniskegiatantindakan_id
          WHERE jeniskegiatantindakan_m.jeniskegiatantindakan_id = 1 AND tindakanpelayanan_t.daftartindakan_id IS NULL AND tindakanpelayanan_t.is_deleted = false) x
  WHERE to_char(x.tgl_tindakan::timestamp with time zone, \'YYYY\'::text) = date_part(\'year\'::text, CURRENT_DATE)::text
  GROUP BY x.tindakan_id, x.tindakan_nama, x.tahun_tindakan
  ORDER BY (count(x.tindakan_id)) DESC
 LIMIT 10;
        ');

           $this->execute('
    ALTER TABLE public.sie_10besarbedah
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190625_074416_sie_10besarbedah cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_074416_sie_10besarbedah cannot be reverted.\n";

        return false;
    }
    */
}
