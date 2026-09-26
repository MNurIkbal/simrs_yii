<?php

use yii\db\Migration;

/**
 * Class m230308_121815_migrate_GLBJ83_dashboardbed_v
 */
class m230308_121815_migrate_GLBJ83_dashboardbed_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."dashboardbed_v";');
        $this->execute("CREATE OR REPLACE VIEW public.dashboardbed_v
        AS SELECT bed.kelas_pelayanan,
            sum(bed.total_bed) AS total_bed,
            sum(bed.total_terisi) AS total_terisi,
            sum(bed.total_bed) - sum(bed.total_terisi) AS total_kosong
           FROM ( SELECT kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
                    count(*) AS total_bed,
                    0 AS total_terisi,
                    0 AS total_kosong
                   FROM kamartempattidur_m
                     JOIN ( SELECT a.kamarruangan_id,
                            a.kelaspelayanan_id
                           FROM kamarruangan_m a
                             JOIN ( SELECT b.ruangan_id,
                                    b.instalasi_id
                                   FROM ruangan_m b) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                          WHERE ruangan_m.instalasi_id = 3) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                     JOIN ( SELECT DISTINCT a.kamarruangan_id
                           FROM tariftindakan_m a) tariftindakan_m ON kamartempattidur_m.kamarruangan_id = tariftindakan_m.kamarruangan_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                  WHERE kamartempattidur_m.is_rekapkinerjaprofesi = true AND kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true
                  GROUP BY kelaspelayanan_m.kelaspelayanan_nama
                UNION ALL
                 SELECT kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
                    0 AS total_bed,
                    count(*) AS total_terisi,
                    0 AS total_kosong
                   FROM kamartempattidur_m
                     JOIN ( SELECT a.kamarruangan_id,
                            a.kelaspelayanan_id
                           FROM kamarruangan_m a
                             JOIN ( SELECT b.ruangan_id,
                                    b.instalasi_id
                                   FROM ruangan_m b) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                          WHERE ruangan_m.instalasi_id = 3) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                     JOIN ( SELECT DISTINCT a.kamarruangan_id
                           FROM tariftindakan_m a) tariftindakan_m ON kamartempattidur_m.kamarruangan_id = tariftindakan_m.kamarruangan_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                  WHERE kamartempattidur_m.status_isi = true AND kamartempattidur_m.is_rekapkinerjaprofesi = true AND kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true
                  GROUP BY kelaspelayanan_m.kelaspelayanan_nama) bed
          GROUP BY bed.kelas_pelayanan;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230308_121815_migrate_GLBJ83_dashboardbed_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230308_121815_migrate_GLBJ83_dashboardbed_v cannot be reverted.\n";

        return false;
    }
    */
}
