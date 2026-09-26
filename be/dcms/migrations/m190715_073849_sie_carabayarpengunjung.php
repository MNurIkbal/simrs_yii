<?php

use yii\db\Migration;

/**
 * Class m190715_073849_sie_carabayarpengunjung
 */
class m190715_073849_sie_carabayarpengunjung extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW IF exists public.sie_carabayarpengunjung;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_carabayarpengunjung AS 
 SELECT x.periode,
    x.instalasi_nama AS \"Instalasi\",
    x.carabayar_nama AS \"Cara Bayar\",
    sum(x.jumlah) AS \"Total\"
   FROM ( SELECT pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pendaftaran_t.instalasi_id) AS jumlah,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS periode
           FROM pendaftaran_t
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text
          GROUP BY pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)), (to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)), carabayar_m.carabayar_nama, carabayar_m.carabayar_id, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pasienadmisi_t.pasienadmisi_id) AS jumlah,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS periode
           FROM pasienadmisi_t
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
          WHERE pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453
          GROUP BY ruangan_m.instalasi_id, instalasi_m.instalasi_nama, (to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY'::text)), (to_char(pasienadmisi_t.tgl_pendaftaran, 'MM'::text)), carabayar_m.carabayar_id, carabayar_m.carabayar_nama, (to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)) x
  GROUP BY x.periode, x.instalasi_nama, x.carabayar_nama
  ORDER BY x.instalasi_nama;
        ");

        $this->execute('
          ALTER TABLE public.sie_carabayarpengunjung
            OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_073849_sie_carabayarpengunjung cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_073849_sie_carabayarpengunjung cannot be reverted.\n";

        return false;
    }
    */
}
