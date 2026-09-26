<?php

use yii\db\Migration;

/**
 * Class m201123_113005_migrate_mhkn_20201123_view_laporansensuspasienrj_v_2989
 */
class m201123_113005_migrate_mhkn_20201123_view_laporansensuspasienrj_v_2989 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensuspasienrj_v;');
        $this->execute("CREATE VIEW \"public\".\"laporansensuspasienrj_v\" AS
             SELECT ruangan_m.ruangan_id,
    ruangan_m.jenis_ruangan,
    fgetnamalookup((ruangan_m.jenis_ruangan)::integer) AS jenis_ruangan_nama,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id
   FROM ruangan_m
  WHERE ((ruangan_m.is_deleted = false) AND (ruangan_m.is_active = true) AND (ruangan_m.jenis_ruangan IS NOT NULL))
  ORDER BY ruangan_m.instalasi_id
        ;");
            $this->execute('ALTER TABLE public.laporansensuspasienrj_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_113005_migrate_mhkn_20201123_view_laporansensuspasienrj_v_2989 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_113005_migrate_mhkn_20201123_view_laporansensuspasienrj_v_2989 cannot be reverted.\n";

        return false;
    }
    */
}
