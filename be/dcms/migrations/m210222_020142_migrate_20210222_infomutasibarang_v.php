<?php

use yii\db\Migration;

/**
 * Class m210222_020142_migrate_20210222_infomutasibarang_v
 */
class m210222_020142_migrate_20210222_infomutasibarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infomutasibarang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infomutasibarang_v\" AS  SELECT mutasibarang_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS statusmutasi,
    pesanbarang_t.no_pemesanan,
    pegawaimengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawaimutasi.nama_pegawai AS pegawai_mutasi,
    COALESCE(pegawaiterima.nama_pegawai, '-'::character varying(50)) AS pegawai_terima
   FROM mutasibarang_t
     JOIN ruangan_m ON mutasibarang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN pesanbarang_t ON pesanbarang_t.pesanbarang_id = mutasibarang_t.pesanbarang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON pegawaimengetahui.pegawai_id = mutasibarang_t.pegawaimengetahui_id
     LEFT JOIN pegawai_m pegawaimutasi ON pegawaimutasi.pegawai_id = mutasibarang_t.pegawaipengirim_id
     LEFT JOIN terimamutasibarang_t ON terimamutasibarang_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     LEFT JOIN pegawai_m pegawaiterima ON terimamutasibarang_t.pegawaipenerima_id = pegawaiterima.pegawai_id
  WHERE mutasibarang_t.is_deleted = false AND mutasibarang_t.is_active = true;");
        
        $this->execute('ALTER TABLE "public"."infomutasibarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210222_020142_migrate_20210222_infomutasibarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210222_020142_migrate_20210222_infomutasibarang_v cannot be reverted.\n";

        return false;
    }
    */
}
