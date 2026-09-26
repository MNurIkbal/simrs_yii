<?php

use yii\db\Migration;

/**
 * Class m230106_160605_migrate_GB425_laporanrekapkinerjaheader_v
 */
class m230106_160605_migrate_GB425_laporanrekapkinerjaheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekapkinerjaheader_v";
        ');

        $this->execute('
            CREATE OR REPLACE VIEW public.laporanrekapkinerjaheader_v
                AS SELECT kamarruangan_m.kamarruangan_id,
                    kelaspelayanan_m.kelaspelayanan_id,
                    kelaspelayanan_m.urutankelas,
                    kamarruangan_m.kamarruangan_nokamar,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    count(kamartempattidur_m.status_isi) AS kapasitas,
                    NULL::bigint AS tersedia,
                    kamarruangan_m.ruangan_id
                FROM kamartempattidur_m
                    JOIN (SELECT a.kamarruangan_id,
                            a.is_rekapkinerjaprofesi,
                            a.is_deleted,
                            a.is_active,
                            a.kelaspelayanan_id,
                            a.kamarruangan_nokamar,
                            a.ruangan_id
                        FROM kamarruangan_m a
                        JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                        WHERE ruangan_m.instalasi_id = 3
                    ) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id and kamarruangan_m.is_rekapkinerjaprofesi = true AND kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true
                    JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true
                GROUP BY kamarruangan_m.kamarruangan_id, kelaspelayanan_m.kelaspelayanan_id, kamarruangan_m.kamarruangan_nokamar, kelaspelayanan_m.kelaspelayanan_nama, kamarruangan_m.ruangan_id
                UNION ALL
                SELECT kamarruangan_m.kamarruangan_id,
                    kelaspelayanan_m.kelaspelayanan_id,
                    kelaspelayanan_m.urutankelas,
                    kamarruangan_m.kamarruangan_nokamar,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    NULL::bigint AS kapasitas,
                    count(kamartempattidur_m.status_isi) AS tersedia,
                    kamarruangan_m.ruangan_id
                FROM ruangan_m
                    JOIN kamarruangan_m ON ruangan_m.ruangan_id = kamarruangan_m.ruangan_id AND kamarruangan_m.is_ttrekaptersedia = true AND kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true
                    JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
                WHERE kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true AND ruangan_m.instalasi_id = 3
                GROUP BY kamarruangan_m.kamarruangan_id, kelaspelayanan_m.kelaspelayanan_id, kamarruangan_m.kamarruangan_nokamar, kelaspelayanan_m.kelaspelayanan_nama;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230106_160605_migrate_GB425_laporanrekapkinerjaheader_v cannot be reverted.\n";
        return false;
    }
}
