<?php

use yii\db\Migration;

/**
 * Class m210312_121517_migrate_20210312_3362_view_sensuspasienranap_v
 */
class m210312_121517_migrate_20210312_3362_view_sensuspasienranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sensuspasienranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sensuspasienranap_v\" AS
            SELECT sensuspasienranap_r.id,
            sensuspasienranap_r.ruangan_id,
            sensuspasienranap_r.kelaspelayanan_id,
            sensuspasienranap_r.tgl_sensus,
            sensuspasienranap_r.pasien_awal,
            sensuspasienranap_r.pasien_masuk,
            sensuspasienranap_r.pasien_pindahan,
            sensuspasienranap_r.pasien_keluarhidup,
            sensuspasienranap_r.pasien_keluardipindahkan,
            sensuspasienranap_r.pasien_keluarmeninggalkur48 AS pasien_keluarmeniggalkur48,
            sensuspasienranap_r.pasien_keluarmeninggalleb48 AS pasien_keluarmeniggalleb48,
            sensuspasienranap_r.pasien_akhir,
            ruangan_m.ruangan_nama,
            kelaspelayanan_m.kelaspelayanan_nama
            FROM ((sensuspasienranap_r
            LEFT JOIN ruangan_m ON ((sensuspasienranap_r.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN kelaspelayanan_m ON ((sensuspasienranap_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            ;");
            $this->execute('
                ALTER TABLE public.sensuspasienranap_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_121517_migrate_20210312_3362_view_sensuspasienranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_121517_migrate_20210312_3362_view_sensuspasienranap_v cannot be reverted.\n";

        return false;
    }
    */
}
