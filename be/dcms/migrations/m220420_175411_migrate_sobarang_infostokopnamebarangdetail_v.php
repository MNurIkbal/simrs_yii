<?php

use yii\db\Migration;

/**
 * Class m220420_175411_migrate_sobarang_infostokopnamebarangdetail_v
 */
class m220420_175411_migrate_sobarang_infostokopnamebarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infostokopnamebarangdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infostokopnamebarangdetail_v" AS   SELECT stokopnamebarangdetail_t.stokopnamebarangdetail_id,
    stokopnamebarangdetail_t.stokopnamebarang_id,
    stokopnamebarangdetail_t.barang_id,
    stokopnamebarang_t.ruangan_id,
    stokopnamebarang_t.pegmengetahui_id,
    stokopnamebarang_t.pegawai_id AS petugas_id,
    ruangan_m.ruangan_nama,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawai_petugas.nama_pegawai AS pegawai_petugas,
    stokopnamebarang_t.nostokopname,
    barang_m.barang_nama,
    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
    COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) AS volume_fisik,
    stokopnamebarangdetail_t.volume_sistem,
    stokopnamebarangdetail_t.harganetto,
    stokopnamebarangdetail_t.kondisibarang,
    stokopnamebarangdetail_t.tglkadaluarsa,
    COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) - stokopnamebarangdetail_t.volume_sistem AS selisih_so,
    COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) * stokopnamebarangdetail_t.harganetto AS harga_netto_fisik,
    COALESCE(stokopnamebarangdetail_t.stok_akhir, kartustok.total) AS stok_sistem,
    COALESCE(stokopnamebarangdetail_t.selisih_akhir, COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) - kartustok.total) AS stok_selisih,
    stokopnamebarangdetail_t.harganetto * (COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) - kartustok.total) AS total_selisih,
    stokopnamebarang_t.is_verifikasi,
    stokopnamebarangdetail_t.satuankecil_id
   FROM stokopnamebarangdetail_t
     JOIN stokopnamebarang_t ON stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
     JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.pegawai_id = pegawai_petugas.pegawai_id
     JOIN barang_m ON stokopnamebarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
     LEFT JOIN ( SELECT st.ruangan_id,
            st.barang_id,
            sum(st.qtystok_in - st.qtystok_out) AS total
           FROM stokbarang_t st
          GROUP BY st.ruangan_id, st.barang_id) kartustok ON kartustok.ruangan_id = stokopnamebarang_t.ruangan_id AND kartustok.barang_id = stokopnamebarangdetail_t.barang_id
  WHERE stokopnamebarangdetail_t.is_deleted = false AND stokopnamebarangdetail_t.is_active = true;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_175411_migrate_sobarang_infostokopnamebarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_175411_migrate_sobarang_infostokopnamebarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
