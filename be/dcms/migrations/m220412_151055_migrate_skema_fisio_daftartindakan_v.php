<?php

use yii\db\Migration;

/**
 * Class m220412_151055_migrate_skema_fisio_daftartindakan_v
 */
class m220412_151055_migrate_skema_fisio_daftartindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.daftartindakan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"daftartindakan_v\" AS
            SELECT daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id,
            kelompoktindakan_m.kelompoktindakan_persencyto,
            kelompoktindakan_m.kelompoktindakan_persendiskon,
            daftartindakan_m.is_active,
            daftartindakan_m.is_paketfisio
            FROM (daftartindakan_m
            JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
            WHERE ((daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true))
            ;");
        $this->execute('
            ALTER TABLE public.daftartindakan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220412_151055_migrate_skema_fisio_daftartindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220412_151055_migrate_skema_fisio_daftartindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
