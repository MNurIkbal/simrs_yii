<?php

use yii\db\Migration;

/**
 * Class m210430_175317_improvment_rekap_throughput
 */
class m210430_175317_improvment_rekap_throughput extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS rekapthroughput_v;
        ');

        $this->execute(' 
            CREATE VIEW "public"."rekapthroughput_v" AS  SELECT \'OPD\'::text AS tipe,
                count(pendaftaran_t.pendaftaran_id) AS total
               FROM pendaftaran_t
              WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            UNION ALL 
             SELECT \'IPD_ADMISSION\'::text AS tipe,
                sum(sensuspasienranap_r.pasien_masuk) AS total
               FROM sensuspasienranap_r
            UNION ALL
             SELECT \'EMERGENCY\'::text AS tipe,
                count(pendaftaran_t.pendaftaran_id) AS total
               FROM pendaftaran_t
              WHERE ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND ((pendaftaran_t.status_periksa)::text <> \'402\'::text))
            UNION ALL
             SELECT \'PHARMACY\'::text AS tipe,
                count(t.total) AS total
               FROM ( SELECT reseptur_t.noresep AS total
                       FROM ((reseptur_t
                         JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
                         JOIN ruangan_m ON ((reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id)))
                      WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.status_reseptur <> 432) AND (penjualanresep_t.status_reseptur = \'660\'::smallint))
                    UNION ALL
                     SELECT penjualanresep_t.noresep AS total
                       FROM (penjualanresep_t
                         JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                      WHERE ((penjualanresep_t.is_deleted = false) AND (penjualanresep_t.reseptur_id IS NULL) AND (penjualanresep_t.status_reseptur = \'660\'::smallint))) t
            UNION ALL
             SELECT \'LABORATORY\'::text AS tipe,
                count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
               FROM (pasienmasukpenunjang_t
                 JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 4))))
              WHERE ((pasienmasukpenunjang_t.is_bayar = true) AND ((pasienmasukpenunjang_t.status_periksa)::text <> \'476\'::text))
            UNION ALL
             SELECT \'RADIOLOGY\'::text AS tipe,
                count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
               FROM (pasienmasukpenunjang_t
                 JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 5))))
              WHERE ((pasienmasukpenunjang_t.is_bayar = true) AND ((pasienmasukpenunjang_t.status_periksa)::text <> \'476\'::text))
            UNION ALL
             SELECT \'OPERATING_THEATRE\'::text AS tipe,
                count(pasienmasukpenunjang_t.no_masukpenunjang) AS total
               FROM (pasienmasukpenunjang_t
                 JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 12))))
              WHERE ((pasienmasukpenunjang_t.is_bayar = true) AND ((pasienmasukpenunjang_t.status_periksa)::text <> \'476\'::text));

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210430_175317_improvment_rekap_throughput cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210430_175317_improvment_rekap_throughput cannot be reverted.\n";

        return false;
    }
    */
}
