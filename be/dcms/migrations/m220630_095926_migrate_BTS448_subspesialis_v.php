<?php

use yii\db\Migration;

/**
 * Class m220630_095926_migrate_BTS448_subspesialis_v
 */
class m220630_095926_migrate_BTS448_subspesialis_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.subspesialis_v;');
        $this->execute("
            CREATE VIEW \"public\".\"subspesialis_v\" AS
            SELECT spesialisruangan_mp.ruangan_id,
            ruangan_m.ruangan_nama,
            spesialisruangan_mp.spesialis_id,
            spesialis_m.spesialis_kode,
            spesialis_m.spesialis_nama,
            subspesialis_m.subspesialis_kode,
            subspesialis_m.subspesialis_nama,
            ruangan_m.is_online,
            subspesialis_m.subspesialis_id,
            subspesialis_m.subspesialis_image
            FROM (((spesialisruangan_mp
            JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama,
            ruangan_m_1.is_online
            FROM ruangan_m ruangan_m_1
            WHERE (ruangan_m_1.is_active = true)) ruangan_m ON ((spesialisruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT spesialis_m_1.spesialis_id,
            spesialis_m_1.spesialis_kode,
            spesialis_m_1.spesialis_nama
            FROM spesialis_m spesialis_m_1
            WHERE (spesialis_m_1.is_active = true)) spesialis_m ON ((spesialisruangan_mp.spesialis_id = spesialis_m.spesialis_id)))
            JOIN ( SELECT subspesialis_m_1.spesialis_id,
            subspesialis_m_1.subspesialis_id,
            subspesialis_m_1.subspesialis_kode,
            subspesialis_m_1.subspesialis_nama,
            subspesialis_m_1.subspesialis_image
            FROM subspesialis_m subspesialis_m_1
            WHERE (subspesialis_m_1.is_active = true)) subspesialis_m ON ((spesialisruangan_mp.subspesialis_id = subspesialis_m.subspesialis_id)))
            WHERE ((spesialisruangan_mp.is_active = true) AND (spesialisruangan_mp.is_deleted = false))
            ORDER BY ruangan_m.ruangan_nama, subspesialis_m.subspesialis_nama
            ;");
        $this->execute('
            ALTER TABLE public.subspesialis_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220630_095926_migrate_BTS448_subspesialis_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220630_095926_migrate_BTS448_subspesialis_v cannot be reverted.\n";

        return false;
    }
    */
}
