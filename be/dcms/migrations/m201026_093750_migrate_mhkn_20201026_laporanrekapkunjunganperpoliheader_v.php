<?php

use yii\db\Migration;

/**
 * Class m201026_093750_migrate_mhkn_20201026_laporanrekapkunjunganperpoliheader_v
 */
class m201026_093750_migrate_mhkn_20201026_laporanrekapkunjunganperpoliheader_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekapkunjunganperpoliheader_v;');
        $this->execute("CREATE VIEW \"public\".\"laporanrekapkunjunganperpoliheader_v\" AS
             SELECT ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM (((ruangan_m
     JOIN ruanganpegawai_mp ON ((ruangan_m.ruangan_id = ruanganpegawai_mp.ruangan_id)))
     JOIN pegawai_m ON ((ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
  WHERE ((ruangan_m.is_deleted = false) AND (ruangan_m.is_active = true) AND (instalasi_m.is_active = true) AND (instalasi_m.is_deleted = false) AND (instalasi_m.is_penunjang = false) AND (instalasi_m.is_pelayanan = true))
            ;");

        $this->execute('ALTER TABLE public.laporanrekapkunjunganperpoliheader_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201026_093750_migrate_mhkn_20201026_laporanrekapkunjunganperpoliheader_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_093750_migrate_mhkn_20201026_laporanrekapkunjunganperpoliheader_v cannot be reverted.\n";

        return false;
    }
    */
}
