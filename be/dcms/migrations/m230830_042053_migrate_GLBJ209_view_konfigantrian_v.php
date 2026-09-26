<?php

use yii\db\Migration;

/**
 * Class m230830_042053_migrate_GLBJ209_view_konfigantrian_v
 */
class m230830_042053_migrate_GLBJ209_view_konfigantrian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS konfigantrian_v;
        '); 

        $this->execute('
            CREATE VIEW "public"."konfigantrian_v" AS  SELECT konfigantrian_m.konfigantrian_id,
    konfigantrian_m.layarantrian_id,
    konfigantrian_m.jenisantrian_id,
    konfigantrian_m.fungsiantrian_id,
    konfigantrian_m.carabayar_id,
    fgetnamalookup(konfigantrian_m.jenisantrian_id) AS jenis_antrian,
    fgetnamalookup(konfigantrian_m.fungsiantrian_id) AS fungsi_antrian,
    fgetvaluelookup(konfigantrian_m.fungsiantrian_id) AS lookup_value, 
    carabayar_m.carabayar_nama,
    konfigantrian_m.is_active AS is_default,
    carabayar_m.is_penjamin,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.instalasi_id,
    instalasi_m.instalasi_nama,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(konfigantrian_m.groupcarabayar_id) AS group_carabayar,
    konfigantrian_m.penomoran_id,
    konfigantrian_m.klasifikasipasien_id,
    klasifikasipasien_m.klasifikasipasien_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    konfigantrian_m.groupcarabayar_id AS group_id,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter
   FROM konfigantrian_m
     LEFT JOIN carabayar_m ON konfigantrian_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN instalasi_m ON konfigantrian_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON konfigantrian_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN klasifikasipasien_m ON konfigantrian_m.klasifikasipasien_id = klasifikasipasien_m.klasifikasipasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON konfigantrian_m.pegawai_id = pegawai_m.pegawai_id
  WHERE konfigantrian_m.is_deleted = false AND (konfigantrian_m.jenisantrian_id = ANY (ARRAY[176, 177, 178, 179, 312]))
  ORDER BY konfigantrian_m.jenisantrian_id;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_042053_migrate_GLBJ209_view_konfigantrian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_042053_migrate_GLBJ209_view_konfigantrian_v cannot be reverted.\n";

        return false;
    }
    */
}
