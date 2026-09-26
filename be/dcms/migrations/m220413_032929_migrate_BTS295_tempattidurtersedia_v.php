<?php

use yii\db\Migration;

/**
 * Class m220413_032929_migrate_BTS295_tempattidurtersedia_v
 */
class m220413_032929_migrate_BTS295_tempattidurtersedia_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.tempattidurtersedia_v;');
        $this->execute("
            CREATE VIEW \"public\".\"tempattidurtersedia_v\" AS
            SELECT ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            count(kamarruangan_m.kamarruangan_id) AS jumlah_bed
            FROM (((kamarruangan_m
            JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
            JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
            WHERE ((kamarruangan_m.is_active = true) AND (kamarruangan_m.is_deleted = false) AND (kamarruangan_m.is_rekapkinerjaprofesi = true) AND (kamartempattidur_m.is_active = true) AND (kamartempattidur_m.is_deleted = false))
            GROUP BY ruangan_m.ruangan_id, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama
            ORDER BY ruangan_m.ruangan_nama
            ;");
        $this->execute('
            ALTER TABLE public.tempattidurtersedia_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_032929_migrate_BTS295_tempattidurtersedia_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_032929_migrate_BTS295_tempattidurtersedia_v cannot be reverted.\n";

        return false;
    }
    */
}
