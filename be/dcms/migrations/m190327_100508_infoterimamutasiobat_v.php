<?php

use yii\db\Migration;

/**
 * Class m190327_100508_infoterimamutasiobat_v
 */
class m190327_100508_infoterimamutasiobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infoterimamutasiobat_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infoterimamutasiobat_v AS 
             SELECT terimamutasiobat_t.terimamutasiobat_id,
                terimamutasiobat_t.tglterima,
                terimamutasiobat_t.noterimamutasi,
                terimamutasiobat_t.ruanganasal_id,
                ruangan_m.ruangan_nama AS ruangan_pengirim,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama AS instalasi_pengirim,
                mutasiobatruangan_t.nomutasioa,
                pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
                pegawai_penerima.nama_pegawai AS pegawai_penerima,
                mutasiobatruangan_t.tglmutasioa AS tgl_mutasi
               FROM terimamutasiobat_t
                 JOIN ruangan_m ON terimamutasiobat_t.ruanganpenerima_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN mutasiobatruangan_t ON mutasiobatruangan_t.mutasiobatruangan_id = terimamutasiobat_t.mutasiobatruangan_id
                 JOIN pegawai_m pegawai_mengetahui ON pegawai_mengetahui.pegawai_id = terimamutasiobat_t.pegawaimengetahui_id
                 JOIN pegawai_m pegawai_penerima ON pegawai_penerima.pegawai_id = terimamutasiobat_t.pegawaipenerima_id
              WHERE terimamutasiobat_t.is_deleted = false AND terimamutasiobat_t.is_active = true;
        ');

        $this->execute('
            ALTER TABLE infoterimamutasiobat_v
              OWNER TO postgres;
        ');

        $this->execute('
            GRANT ALL ON TABLE infoterimamutasiobat_v TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_100508_infoterimamutasiobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_100508_infoterimamutasiobat_v cannot be reverted.\n";

        return false;
    }
    */
}
