<?php

use yii\db\Migration;

/**
 * Class m220420_175213_migrate_sobarang_infostokopnamebarang_v
 */
class m220420_175213_migrate_sobarang_infostokopnamebarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infostokopnamebarang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infostokopnamebarang_v" AS   SELECT a.stokopnamebarang_id,
    a.formsobarang_id,
    a.pegmengetahui_id,
    a.petugas_id,
    a.instalasi_id,
    a.jenisstokopname,
    a.instalasi_nama,
    a.jenis_stokopname,
    a.ruangan_nama,
    a.tglstokopname,
    a.nostokopname,
    a.totalharga_fisik,
    a.totalharga_sistem,
    a.selisih,
    a.noformulir,
    a.tglformulir,
    sum(a.harga_netto_fisik) AS harga_netto_fisik,
    sum(a.total_harga_sistem) AS total_harga_sistem,
    sum(a.total_selisih) AS total_selisih,
    a.is_verifikasi
   FROM ( SELECT stokopnamebarang_t.stokopnamebarang_id,
            stokopnamebarang_t.formsobarang_id,
            stokopnamebarang_t.ruangan_id,
            stokopnamebarang_t.pegmengetahui_id,
            stokopnamebarang_t.pegawai_id AS petugas_id,
            ruangan_m.instalasi_id,
            stokopnamebarang_t.jenisstokopname,
            instalasi_m.instalasi_nama,
            fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_stokopname,
            ruangan_m.ruangan_nama,
            stokopnamebarang_t.tglstokopname,
            stokopnamebarang_t.nostokopname,
            stokopnamebarang_t.totalharga_fisik,
            stokopnamebarang_t.totalharga_sistem,
            stokopnamebarang_t.totalharga_sistem - stokopnamebarang_t.totalharga_fisik AS selisih,
            formsobarang_t.noformulir,
            formsobarang_t.tglformulir,
            COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) * stokopnamebarangdetail_t.harganetto AS harga_netto_fisik,
            stokopnamebarangdetail_t.harganetto * stokopnamebarangdetail_t.volume_sistem AS total_harga_sistem,
            stokopnamebarangdetail_t.harganetto * COALESCE(stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem) - stokopnamebarangdetail_t.harganetto * stokopnamebarangdetail_t.volume_sistem AS total_selisih,
            stokopnamebarang_t.is_verifikasi
           FROM stokopnamebarang_t
             JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
             LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.pegawai_id = pegawai_petugas.pegawai_id
             LEFT JOIN formsobarang_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
             LEFT JOIN stokopnamebarangdetail_t ON stokopnamebarang_t.stokopnamebarang_id = stokopnamebarangdetail_t.stokopnamebarang_id
          WHERE stokopnamebarang_t.is_deleted = false
          GROUP BY stokopnamebarang_t.stokopnamebarang_id, stokopnamebarangdetail_t.revisi_stok, stokopnamebarangdetail_t.volume_fisik, stokopnamebarangdetail_t.volume_sistem, stokopnamebarangdetail_t.harganetto, stokopnamebarang_t.formsobarang_id, stokopnamebarang_t.ruangan_id, stokopnamebarang_t.pegmengetahui_id, stokopnamebarang_t.pegawai_id, ruangan_m.instalasi_id, stokopnamebarang_t.jenisstokopname, instalasi_m.instalasi_nama, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), ruangan_m.ruangan_nama, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.nostokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, formsobarang_t.noformulir, formsobarang_t.tglformulir) a
  GROUP BY a.stokopnamebarang_id, a.formsobarang_id, a.pegmengetahui_id, a.petugas_id, a.instalasi_id, a.jenisstokopname, a.instalasi_nama, a.jenis_stokopname, a.ruangan_nama, a.tglstokopname, a.nostokopname, a.totalharga_fisik, a.totalharga_sistem, a.selisih, a.noformulir, a.tglformulir, a.is_verifikasi;  ');
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_175213_migrate_sobarang_infostokopnamebarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_175213_migrate_sobarang_infostokopnamebarang_v cannot be reverted.\n";

        return false;
    }
    */
}
