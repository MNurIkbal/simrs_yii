<?php

use yii\db\Migration;

/**
 * Class m220323_120436_migrate_BTS205_laporanrekapkinerjaheader_v
 */
class m220323_120436_migrate_BTS205_laporanrekapkinerjaheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekapkinerjaheader_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekapkinerjaheader_v\" AS
            SELECT kamarruangan_m.kamarruangan_id,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.urutankelas,
            kamarruangan_m.kamarruangan_nokamar,
            kelaspelayanan_m.kelaspelayanan_nama,
            count(kamartempattidur_m.status_isi) AS kapasitas,
            NULL::bigint AS tersedia,
            kamarruangan_m.ruangan_id
            FROM ((kamartempattidur_m
            JOIN kamarruangan_m ON (((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id) AND (kamarruangan_m.is_rekapkinerjaprofesi = true) AND (kamarruangan_m.is_deleted = false) AND (kamarruangan_m.is_active = true))))
            JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((kamartempattidur_m.is_deleted = false) AND (kamartempattidur_m.is_active = true))
            GROUP BY kamarruangan_m.kamarruangan_id, kelaspelayanan_m.kelaspelayanan_id, kamarruangan_m.kamarruangan_nokamar, kelaspelayanan_m.kelaspelayanan_nama
            UNION ALL
            SELECT kamarruangan_m.kamarruangan_id,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.urutankelas,
            kamarruangan_m.kamarruangan_nokamar,
            kelaspelayanan_m.kelaspelayanan_nama,
            NULL::bigint AS kapasitas,
            count(kamartempattidur_m.status_isi) AS tersedia,
            kamarruangan_m.ruangan_id
            FROM (((ruangan_m
            JOIN kamarruangan_m ON (((ruangan_m.ruangan_id = kamarruangan_m.ruangan_id) AND (kamarruangan_m.is_ttrekaptersedia = true) AND (kamarruangan_m.is_deleted = false) AND (kamarruangan_m.is_active = true))))
            JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
            WHERE ((kamartempattidur_m.is_deleted = false) AND (kamartempattidur_m.is_active = true))
            GROUP BY kamarruangan_m.kamarruangan_id, kelaspelayanan_m.kelaspelayanan_id, kamarruangan_m.kamarruangan_nokamar, kelaspelayanan_m.kelaspelayanan_nama
            ;");
        $this->execute('
            ALTER TABLE public.laporanrekapkinerjaheader_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_120436_migrate_BTS205_laporanrekapkinerjaheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_120436_migrate_BTS205_laporanrekapkinerjaheader_v cannot be reverted.\n";

        return false;
    }
    */
}
