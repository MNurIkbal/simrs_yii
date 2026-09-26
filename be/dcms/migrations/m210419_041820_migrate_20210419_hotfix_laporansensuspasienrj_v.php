<?php

use yii\db\Migration;

/**
 * Class m210419_041820_migrate_20210419_hotfix_laporansensuspasienrj_v
 */
class m210419_041820_migrate_20210419_hotfix_laporansensuspasienrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensuspasienrj_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensuspasienrj_v\" AS
            SELECT ruangan_m.ruangan_id,
            ruangan_m.jenis_ruangan,
            fgetnamalookup((ruangan_m.jenis_ruangan)::integer) AS jenis_ruangan_nama,
            ruangan_m.ruangan_nama,
            ruangan_m.instalasi_id
            FROM ruangan_m
            WHERE (ruangan_m.jenis_ruangan IS NOT NULL)
            ORDER BY ruangan_m.instalasi_id
            ;");
        $this->execute('
            ALTER TABLE public.laporansensuspasienrj_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210419_041820_migrate_20210419_hotfix_laporansensuspasienrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210419_041820_migrate_20210419_hotfix_laporansensuspasienrj_v cannot be reverted.\n";

        return false;
    }
    */
}
