<?php

use yii\db\Migration;

/**
 * Class m220420_174758_migrate_sobarang_laporanformsobarang_v
 */
class m220420_174758_migrate_sobarang_laporanformsobarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanformsobarang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanformsobarang_v" AS  SELECT formsobarang_t.formsobarang_id,
    formsobarang_t.stokopnamebarang_id,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.tglformulir,
    formsobarang_t.noformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.totalharga_fisik,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_so,
    stokopnamebarang_t.totalharga_sistem,
    formsobarang_t.total_harganetto,
    pegawaiverifikasi.nama_pegawai AS pegawaiverifikasi
   FROM formsobarang_t
     LEFT JOIN stokopnamebarang_t ON stokopnamebarang_t.stokopnamebarang_id = formsobarang_t.stokopnamebarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN formsobarangdetail_t ON formsobarang_t.formsobarang_id = formsobarangdetail_t.formsobarang_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) pegawaiverifikasi ON stokopnamebarang_t.pegawaiverifikasi_id = pegawaiverifikasi.pegawai_id
  WHERE formsobarang_t.is_active = true AND formsobarang_t.is_deleted = false
  GROUP BY formsobarang_t.formsobarang_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, stokopnamebarang_t.nostokopname, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), formsobarang_t.stokopnamebarang_id, formsobarang_t.ruangan_id, formsobarang_t.tglformulir, formsobarang_t.noformulir, formsobarang_t.total_harganetto, pegawaiverifikasi.nama_pegawai;
        ');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_174758_migrate_sobarang_laporanformsobarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_174758_migrate_sobarang_laporanformsobarang_v cannot be reverted.\n";

        return false;
    }
    */
}
