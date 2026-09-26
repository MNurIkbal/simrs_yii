<?php

use yii\db\Migration;

/**
 * Class m211217_120831_migrate_US2409_RM_RL1_1
 */
class m211217_120831_migrate_US2409_RM_RL1_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rl1_1_datars_tempattidur_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl1_1_datars_tempattidur_v\" AS
            SELECT t.kelaspelayanan_id,
            t.kelaspelayanan_nama,
            ( SELECT count(kr.kamarruangan_id) AS jumlah_tempattidur
            FROM (kamarruangan_m kr
            JOIN ( SELECT ktt_1.kamarruangan_id,
            ktt_1.is_active,
            ktt_1.is_deleted
            FROM kamartempattidur_m ktt_1
            GROUP BY ktt_1.kamarruangan_id, ktt_1.is_active, ktt_1.is_deleted) ktt ON ((kr.kamarruangan_id = ktt.kamarruangan_id)))
            WHERE ((kr.kelaspelayanan_id = t.kelaspelayanan_id) AND (kr.is_active = true) AND (kr.is_deleted = false) AND (kr.is_kamarthruput = true))) AS jumlah_tempattidur
            FROM kelaspelayanan_m t
            WHERE ((t.is_deleted = false) AND (t.is_active = true))
            ;");
            $this->execute('
                ALTER TABLE public.rl1_1_datars_tempattidur_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211217_120831_migrate_US2409_RM_RL1_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_120831_migrate_US2409_RM_RL1_1 cannot be reverted.\n";

        return false;
    }
    */
}
