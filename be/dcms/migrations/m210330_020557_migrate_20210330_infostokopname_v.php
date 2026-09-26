<?php

use yii\db\Migration;

/**
 * Class m210330_020557_migrate_20210330_infostokopname_v
 */
class m210330_020557_migrate_20210330_infostokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infostokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopname_v\" AS  SELECT instalasi_m.instalasi_id,
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
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    stokopname_t.is_verifikasi,
    stokopname_t.tglverifikasi,
    pegawaiverifikasi.pegawai_id AS pegawaiverifikasi_id,
    pegawaiverifikasi.nomorindukpegawai AS pegawaiverifikasi_nip,
    pegawaiverifikasi.noidentitas AS pegawaiverifikasi_noidentitas,
    pegawaiverifikasi.nama_pegawai AS pegawaiverifikasi_nama
   FROM stokopname_t
     LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN pegawai_m petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaiverifikasi ON stokopname_t.pegawaiverifikasi_id = pegawaiverifikasi.pegawai_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."infostokopname_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210330_020557_migrate_20210330_infostokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_020557_migrate_20210330_infostokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
