<?php

use yii\db\Migration;

/**
 * Class m210218_095819_migrate_20210218_3164_laporanrekapkinerjaheader_v
 */
class m210218_095819_migrate_20210218_3164_laporanrekapkinerjaheader_v extends Migration
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
            NULL::bigint AS tersedia
            FROM (((ruangan_m
            JOIN kamarruangan_m ON (((ruangan_m.ruangan_id = kamarruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 3))))
            JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
            WHERE ((kamartempattidur_m.is_deleted = false) AND (kamartempattidur_m.is_active = true) AND (kamarruangan_m.is_active = true))
            GROUP BY kamarruangan_m.kamarruangan_id, kelaspelayanan_m.kelaspelayanan_id, kamarruangan_m.kamarruangan_nokamar, kelaspelayanan_m.kelaspelayanan_nama
            UNION ALL
            SELECT kamarruangan_m.kamarruangan_id,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.urutankelas,
            kamarruangan_m.kamarruangan_nokamar,
            kelaspelayanan_m.kelaspelayanan_nama,
            NULL::bigint AS kapasitas,
            count(kamartempattidur_m.status_isi) AS tersedia
            FROM (((ruangan_m
            JOIN kamarruangan_m ON (((ruangan_m.ruangan_id = kamarruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 3))))
            JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN kamartempattidur_m ON (((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id) AND (kamartempattidur_m.status_isi = false))))
            WHERE ((kamartempattidur_m.is_deleted = false) AND (kamartempattidur_m.is_active = true) AND (kamarruangan_m.is_active = true))
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
        echo "m210218_095819_migrate_20210218_3164_laporanrekapkinerjaheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210218_095819_migrate_20210218_3164_laporanrekapkinerjaheader_v cannot be reverted.\n";

        return false;
    }
    */
}
