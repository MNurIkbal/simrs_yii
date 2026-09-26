<?php

use yii\db\Migration;

/**
 * Class m200116_030407_sie_bpjsklaim
 */
class m200116_030407_sie_bpjsklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists sie_bpjsklaim;');

        $this->execute("
            CREATE OR REPLACE VIEW sie_bpjsklaim AS 
 SELECT x.periode,
    x.jumlah,
    0 AS jumlah_klaim
   FROM ( SELECT to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS periode,
            count(pendaftaran_t.carabayar_id) AS jumlah
           FROM pendaftaran_t
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.carabayar_id = 6
          GROUP BY to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date
        UNION ALL
         SELECT to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS periode,
            count(pasienadmisi_t.carabayar_id) AS jumlah
           FROM pasienadmisi_t
          WHERE pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.carabayar_id = 6
          GROUP BY to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date) x
UNION ALL
 SELECT x.periode,
    0 AS jumlah,
    x.jumlah_klaim
   FROM ( SELECT to_char(klaiminacbg_t.created_date, 'YYYY-MM-DD'::text)::date AS periode,
            count(pendaftaran_t.carabayar_id) AS jumlah_klaim
           FROM klaiminacbg_t
             JOIN pendaftaran_t ON klaiminacbg_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::text <> '402'::text AND pendaftaran_t.carabayar_id = 6
          GROUP BY to_char(klaiminacbg_t.created_date, 'YYYY-MM-DD'::text)
        UNION ALL
         SELECT to_char(klaiminacbg_t.created_date, 'YYYY-MM-DD'::text)::date AS periode,
            count(pasienadmisi_t.carabayar_id) AS jumlah
           FROM klaiminacbg_t
             JOIN pasienadmisi_t ON klaiminacbg_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
          WHERE pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.carabayar_id = 6
          GROUP BY to_char(klaiminacbg_t.created_date, 'YYYY-MM-DD'::text)) x;");

        $this->execute('ALTER TABLE sie_bpjsklaim
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200116_030407_sie_bpjsklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200116_030407_sie_bpjsklaim cannot be reverted.\n";

        return false;
    }
    */
}
