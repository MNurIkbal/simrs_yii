<?php

use yii\db\Migration;

/**
 * Class m220715_070237_migrate_mhg_1780_laporanstokopname_v
 */
class m220715_070237_migrate_mhg_1780_laporanstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('DROP VIEW if exists "public"."laporanstokopname_v";');

	    $this->execute("
	              CREATE VIEW \"public\".\"laporanstokopname_v\" AS   SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopname_t.is_stokawal AS isstokawal,
    stokopname_t.jenisstokopname,
    stokopname_t.keterangan_opname,
    stokopname_t.totalharga_fisik,
    stokopname_t.totalharga_sistem,
    petugas1.pegawai_id AS petugas1_id,
    petugas1.nomorindukpegawai AS petugas1_nip,
    petugas1.noidentitas AS petugas1_noidentitas,
    petugas1.gelardepan AS petugas1_gelardepan,
    petugas1.nama_pegawai AS petugas1_nama,
    gelarbelakangpetugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    gelarbelakangpetugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang
   FROM stokopname_t
     LEFT JOIN ( SELECT a.formulirstokopname_id,
            a.tglformulir,
            a.noformulir
           FROM formulirstokopname_t a) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan
           FROM pegawai_m a) petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan
           FROM pegawai_m a) petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN ( SELECT a.gelarbelakang_id,
            a.gelarbelakang_nama
           FROM gelarbelakang_m a) gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN ( SELECT a.gelarbelakang_id,
            a.gelarbelakang_nama
           FROM gelarbelakang_m a) gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.gelarbelakang,
            a.nomorindukpegawai,
            a.noidentitas,
            a.gelardepan
           FROM pegawai_m a) pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN ( SELECT a.gelarbelakang_id,
            a.gelarbelakang_nama
           FROM gelarbelakang_m a) gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_070237_migrate_mhg_1780_laporanstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_070237_migrate_mhg_1780_laporanstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
