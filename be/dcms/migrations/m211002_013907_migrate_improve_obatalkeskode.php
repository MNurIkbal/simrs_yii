<?php

use yii\db\Migration;

/**
 * Class m211002_013907_migrate_improve_obatalkeskode
 */
class m211002_013907_migrate_improve_obatalkeskode extends Migration
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
    obatalkes_m.obatalkes_kode
   FROM terimamutasiobatdetail_t
     JOIN terimamutasiobat_t ON terimamutasiobatdetail_t.terimamutasiobat_id = terimamutasiobat_t.terimamutasiobat_id
     JOIN obatalkes_m ON terimamutasiobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON terimamutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     JOIN ruangan_m ruang_terima ON terimamutasiobat_t.ruanganpenerima_id = ruang_terima.ruangan_id
     JOIN ruangan_m ruang_asal ON terimamutasiobat_t.ruanganasal_id = ruang_asal.ruangan_id
     LEFT JOIN pegawai_m pegawai_terima ON terimamutasiobat_t.pegawaipenerima_id = pegawai_terima.pegawai_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON terimamutasiobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
  WHERE terimamutasiobatdetail_t.is_deleted = false AND terimamutasiobatdetail_t.is_active = true;
");

        $this->execute('DROP VIEW if exists "public"."detailmutasiobatalkes_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"detailmutasiobatalkes_v\" AS  SELECT mutasiobatdetail_t.mutasiobatdetail_id,
    mutasiobatruangan_t.mutasiobatruangan_id,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.pesanobatalkes_id,
    pesanobatalkes_t.nopemesanan,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasiobatdetail_t.jumlah_mutasi,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obatalkes_namalain,
    obatalkes_m.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    mutasiobatdetail_t.harga_netto,
    mutasiobatdetail_t.harga_jualsatuan,
    pegawaimutasi.nama_pegawai AS pegawai_mutasi,
    pegawai_m.nama_pegawai AS pegawai_mengetahui,
        CASE
            WHEN mutasiobatdetail_t.pesanobatdetail_id IS NULL THEN mutasiobatdetail_t.satuanbesar_id
            ELSE pesanobatdetail_t.satuanbesar_id
        END AS satuanbesar_id,
        CASE
            WHEN mutasiobatdetail_t.pesanobatdetail_id IS NULL THEN satuan_besar.satuanunit_nama
            ELSE pesanobatdetail_t.satuanbesar_nama
        END AS satuanbesar_nama,
    pesanobatdetail_t.satuan_pemesanan,
        CASE
            WHEN mutasiobatdetail_t.pesanobatdetail_id IS NULL THEN mutasiobatdetail_t.jumlah_pesan
            ELSE pesanobatdetail_t.jumlah_pesan
        END AS jumlah_pesan,
        CASE
            WHEN mutasiobatdetail_t.pesanobatdetail_id IS NULL THEN mutasiobatdetail_t.jumlah_input
            ELSE pesanobatdetail_t.jumlah_input
        END AS jumlah_input,
    obatalkes_m.harganetto,
    obatalkes_m.hargajual,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.obatalkes_nama,
    mutasiobatdetail_t.satuankecil_id AS satuanmutasi_id,
    satuan_mutasi.satuanunit_nama AS satuan_mutasi,
    mutasiobatdetail_t.tgl_kadaluarsa AS expired,
    obatalkes_m.obatalkes_kode
   FROM mutasiobatdetail_t
     JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
     LEFT JOIN pesanobatalkes_t ON mutasiobatruangan_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id
     LEFT JOIN ( SELECT pesan_detail.pesanobatdetail_id,
            pesan_detail.jumlah_pesan,
            pesan_detail.jumlah_input,
            pesan_detail.satuan_pemesanan,
            pesan_detail.satuanbesar_id,
            satuan_besar_1.satuanunit_nama AS satuanbesar_nama
           FROM pesanobatdetail_t pesan_detail
             LEFT JOIN satuanunit_m satuan_besar_1 ON pesan_detail.satuanbesar_id = satuan_besar_1.satuanunit_id) pesanobatdetail_t ON mutasiobatdetail_t.pesanobatdetail_id = pesanobatdetail_t.pesanobatdetail_id
     JOIN obatalkes_m ON mutasiobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ruangan_m ON mutasiobatruangan_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasiobatruangan_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN pegawai_m ON mutasiobatruangan_t.pegawaimengetahui_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m pegawaimutasi ON mutasiobatruangan_t.created_by = pegawaimutasi.pegawai_id
     LEFT JOIN satuanunit_m satuan_besar ON mutasiobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN satuanunit_m satuan_mutasi ON mutasiobatdetail_t.satuankecil_id = satuan_mutasi.satuanunit_id
  WHERE mutasiobatruangan_t.is_active = true AND mutasiobatdetail_t.is_deleted = false;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211002_013907_migrate_improve_obatalkeskode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211002_013907_migrate_improve_obatalkeskode cannot be reverted.\n";

        return false;
    }
    */
}
