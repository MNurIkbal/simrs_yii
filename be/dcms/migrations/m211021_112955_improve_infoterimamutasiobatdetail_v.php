<?php

use yii\db\Migration;

/**
 * Class m211021_112955_improve_infoterimamutasiobatdetail_v
 */
class m211021_112955_improve_infoterimamutasiobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    terimamutasiobatdetail_t.tglkadaluarsa,
    obatalkes_m.obatalkes_kode,
    pesanobatdetail_t.jumlah_input AS qty_pesan,
    satuan_besar.satuanunit_nama AS satuan_besar,
    pesanobatdetail_t.satuanbesar_id
   FROM terimamutasiobatdetail_t
     JOIN terimamutasiobat_t ON terimamutasiobatdetail_t.terimamutasiobat_id = terimamutasiobat_t.terimamutasiobat_id
     JOIN obatalkes_m ON terimamutasiobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON terimamutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ruangan_m ruang_terima ON terimamutasiobat_t.ruanganpenerima_id = ruang_terima.ruangan_id
     JOIN ruangan_m ruang_asal ON terimamutasiobat_t.ruanganasal_id = ruang_asal.ruangan_id
     LEFT JOIN pegawai_m pegawai_terima ON terimamutasiobat_t.pegawaipenerima_id = pegawai_terima.pegawai_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON terimamutasiobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN ( SELECT a.mutasiobatdetail_id,
            a.jumlah_pesan,
            a.pesanobatdetail_id
           FROM mutasiobatdetail_t a
          WHERE a.is_deleted = false) mutasiobatdetail_t ON terimamutasiobatdetail_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id
     LEFT JOIN ( SELECT a.pesanobatdetail_id,
            a.jumlah_input,
            a.satuanbesar_id
           FROM pesanobatdetail_t a) pesanobatdetail_t ON mutasiobatdetail_t.pesanobatdetail_id = pesanobatdetail_t.pesanobatdetail_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_besar ON pesanobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE terimamutasiobatdetail_t.is_deleted = false AND terimamutasiobatdetail_t.is_active = true;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211021_112955_improve_infoterimamutasiobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_112955_improve_infoterimamutasiobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
