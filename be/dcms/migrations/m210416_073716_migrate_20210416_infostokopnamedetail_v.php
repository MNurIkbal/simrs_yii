<?php

use yii\db\Migration;

/**
 * Class m210416_073716_migrate_20210416_infostokopnamedetail_v
 */
class m210416_073716_migrate_20210416_infostokopnamedetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infostokopnamedetail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopnamedetail_v\" AS  SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopnamedetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    stokopnamedetail_t.tglkadaluarsa,
    stokopnamedetail_t.volume_fisik,
    stokopnamedetail_t.volume_sistem,
    stokopnamedetail_t.volume_fisik * stokopnamedetail_t.harganetto AS harga_netto_fisik,
    stokopnamedetail_t.volume_sistem * stokopnamedetail_t.harganetto AS harga_netto_sistem,
    stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem AS selisih_jumlah,
    stokopnamedetail_t.jmlselisihstok * stokopnamedetail_t.harganetto AS selisih_harganetto,
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
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    obatalkes_m.obatalkes_nama,
    fgetnamalookup(stokopnamedetail_t.kondisibarang::integer) AS kondisibarang_nama,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir,
    rakobat_m.rakobat_nama,
    stok.qty_sisa AS stok_sistem,
    stok.qty_sisa - stokopnamedetail_t.volume_fisik AS stok_selisih,
    stokopnamedetail_t.stokopnamedetail_id,
    stokopnamedetail_t.harganetto,
    stokopnamedetail_t.stokobatalkes_id
   FROM stokopnamedetail_t
     JOIN stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN pegawai_m petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN pegawai_m petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
     JOIN formstokopname_r ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_r.stokopnamedetail_id
     LEFT JOIN periodestokobat_m ON formstokopname_r.periodestok_id = periodestokobat_m.periodestokobat_id
     LEFT JOIN konfigrak_m ON stokopnamedetail_t.obatalkes_id = konfigrak_m.obatalkes_id AND stokopname_t.ruangan_id = konfigrak_m.ruangan_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_sisa
           FROM stokobatalkes_r) stok ON stokopnamedetail_t.obatalkes_id = stok.obatalkes_id AND stokopname_t.ruangan_id = stok.ruangan_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."infostokopnamedetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210416_073716_migrate_20210416_infostokopnamedetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210416_073716_migrate_20210416_infostokopnamedetail_v cannot be reverted.\n";

        return false;
    }
    */
}
