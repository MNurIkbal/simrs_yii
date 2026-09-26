<?php

use yii\db\Migration;

/**
 * Class m220609_093554_migrate_BTS377_ruangan_v
 */
class m220609_093554_migrate_BTS377_ruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.ruangan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"ruangan_v\" AS
            SELECT ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            ruangan_m.ruangan_singkatan,
            ruangan_m.is_active,
            ruangan_m.is_sync,
            ruangan_m.is_online,
            ruangan_m.ruangan_image AS ruangan_gambar
            FROM (ruangan_m
            JOIN ( SELECT instalasi_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama
            FROM instalasi_m instalasi_m_1) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (ruangan_m.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.ruangan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220609_093554_migrate_BTS377_ruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220609_093554_migrate_BTS377_ruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
