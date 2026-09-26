<?php

use yii\db\Migration;

/**
 * Class m190806_034646_sie_tagihanklaim
 */
class m190806_034646_sie_tagihanklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW if exists public.sie_tagihanklaim;
        ');

        $this->execute("   
      CREATE OR REPLACE VIEW public.sie_tagihanklaim AS 
 SELECT x.jenis,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.instalasi_id,
    x.instalasi_nama,
    x.carabayar_id,
    x.carabayar_nama,
    x.tagihan
   FROM ( SELECT 'Tagihan RS'::text AS jenis,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            sum(pembayaran.jmlpembayaran) AS tagihan
           FROM pendaftaran_t
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
                    pembayaranpelayanan_t.pendaftaran_id,
                    tandabuktibayar_t.jmlpembayaran
                   FROM pembayaranpelayanan_t
                     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
                  WHERE pembayaranpelayanan_t.is_deleted = false AND pembayaranpelayanan_t.is_active = true) pembayaran ON pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id
          GROUP BY 'Tagihan RS'::text, pendaftaran_t.no_pendaftaran, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date), instalasi_m.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.carabayar_id, carabayar_m.carabayar_nama
        UNION ALL
         SELECT 'Klaim BPJS'::text AS jenis,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            klaimgroup_t.total
           FROM pendaftaran_t
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.is_deleted = false
             JOIN klaimgroup_t ON klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id AND klaimgroup_t.is_deleted = false) x;
        ");

        $this->execute('
      ALTER TABLE public.sie_tagihanklaim
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_034646_sie_tagihanklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_034646_sie_tagihanklaim cannot be reverted.\n";

        return false;
    }
    */
}
