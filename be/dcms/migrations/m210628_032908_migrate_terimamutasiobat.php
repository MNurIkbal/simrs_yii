<?php

use yii\db\Migration;

/**
 * Class m210628_032908_migrate_terimamutasiobat
 */
class m210628_032908_migrate_terimamutasiobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoterimamutasiobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoterimamutasiobat_v\" AS  SELECT terimamutasiobat_t.terimamutasiobat_id,
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
     LEFT JOIN pegawai_m pegawai_mengetahui ON pegawai_mengetahui.pegawai_id = terimamutasiobat_t.pegawaimengetahui_id
     LEFT JOIN pegawai_m pegawai_penerima ON pegawai_penerima.pegawai_id = terimamutasiobat_t.pegawaipenerima_id
  WHERE terimamutasiobat_t.is_deleted = false AND terimamutasiobat_t.is_active = true;");

        $this->execute('DROP VIEW if exists "public"."infoterimamutasiobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoterimamutasiobatdetail_v\" AS  SELECT terimamutasiobatdetail_t.terimamutasiobat_id,
    terimamutasiobatdetail_t.terimamutasiobatdetail_id,
    terimamutasiobat_t.noterimamutasi,
    terimamutasiobat_t.ruanganpenerima_id,
    ruang_terima.ruangan_nama AS ruang_penerima,
    terimamutasiobat_t.ruanganasal_id,
    ruang_asal.ruangan_nama AS ruang_asal,
    terimamutasiobat_t.pegawaipenerima_id,
    pegawai_terima.nama_pegawai AS pegawai_penerima,
    terimamutasiobat_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    terimamutasiobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    terimamutasiobatdetail_t.satuankecil_id,
    satuanunit_m.satuanunit_nama,
    terimamutasiobatdetail_t.jmlterima,
    terimamutasiobatdetail_t.harganettoterima,
    terimamutasiobatdetail_t.hargajualterima,
    terimamutasiobatdetail_t.tglkadaluarsa
   FROM terimamutasiobatdetail_t
     JOIN terimamutasiobat_t ON terimamutasiobatdetail_t.terimamutasiobat_id = terimamutasiobat_t.terimamutasiobat_id
     JOIN obatalkes_m ON terimamutasiobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON terimamutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ruangan_m ruang_terima ON terimamutasiobat_t.ruanganpenerima_id = ruang_terima.ruangan_id
     JOIN ruangan_m ruang_asal ON terimamutasiobat_t.ruanganasal_id = ruang_asal.ruangan_id
     LEFT JOIN pegawai_m pegawai_terima ON terimamutasiobat_t.pegawaipenerima_id = pegawai_terima.pegawai_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON terimamutasiobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
  WHERE terimamutasiobatdetail_t.is_deleted = false AND terimamutasiobatdetail_t.is_active = true;");
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210628_032908_migrate_terimamutasiobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210628_032908_migrate_terimamutasiobat cannot be reverted.\n";

        return false;
    }
    */
}
