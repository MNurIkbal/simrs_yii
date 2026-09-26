<?php

use yii\db\Migration;

/**
 * Class m201125_084057_migrate_mhkn_20201125_view_inforuanganinstalasipeg_v_3029
 */
class m201125_084057_migrate_mhkn_20201125_view_inforuanganinstalasipeg_v_3029 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.inforuanganinstalasipeg_v;');
        $this->execute("CREATE VIEW \"public\".\"inforuanganinstalasipeg_v\" AS
             SELECT instalasi_m.instalasi_id,
    ruanganpegawai_mp.ruangan_id,
    ruanganpegawai_mp.pegawai_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai
   FROM (((ruanganpegawai_mp
     JOIN ruangan_m ON ((ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pegawai_m ON ((ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((instalasi_m.is_active = true) AND (ruangan_m.is_active = true) AND ruanganpegawai_mp.is_active AND (pegawai_m.is_active = true))
            ;");
            $this->execute('ALTER TABLE public.inforuanganinstalasipeg_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_084057_migrate_mhkn_20201125_view_inforuanganinstalasipeg_v_3029 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_084057_migrate_mhkn_20201125_view_inforuanganinstalasipeg_v_3029 cannot be reverted.\n";

        return false;
    }
    */
}
