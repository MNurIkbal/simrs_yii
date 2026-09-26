<?php

use yii\db\Migration;

/**
 * Class m190411_011100_infostokopnamebarang_v_update
 */
class m190411_011100_infostokopnamebarang_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('
      DROP VIEW infostokopnamebarang_v;
        ');

        $this->execute("
    CREATE OR REPLACE VIEW infostokopnamebarang_v AS 
 SELECT stokopnamebarang_t.stokopnamebarang_id,
    stokopnamebarang_t.formsobarang_id,
    stokopnamebarang_t.ruangan_id,
    stokopnamebarang_t.pegmengetahui_id,
    stokopnamebarang_t.petugas_id,
    ruangan_m.instalasi_id,
    stokopnamebarang_t.jenisstokopname,
    instalasi_m.instalasi_nama,
    jenis_stokopname.lookup_name AS jenis_stokopname,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.totalharga_fisik,
    stokopnamebarang_t.totalharga_sistem,
    stokopnamebarang_t.totalharga_sistem - stokopnamebarang_t.totalharga_fisik AS selisih,
    formsobarang_t.noformulir,
    min(periodestokbarang_m.tglperiodestok_awal) AS periode_awal,
    max(periodestokbarang_m.tglperiodestok_akhir) AS periode_akhir
   FROM stokopnamebarang_t
     JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN lookup_m jenis_stokopname ON stokopnamebarang_t.jenisstokopname::integer = jenis_stokopname.lookup_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.petugas_id = pegawai_petugas.pegawai_id
     LEFT JOIN formsobarang_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN formsobarangdetail_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN periodestokbarang_m ON formsobarangdetail_t.periodestok_id = periodestokbarang_m.periodestokbarang_id
  WHERE stokopnamebarang_t.is_deleted = false AND stokopnamebarang_t.is_active = true
  GROUP BY stokopnamebarang_t.stokopnamebarang_id, stokopnamebarang_t.formsobarang_id, stokopnamebarang_t.ruangan_id, stokopnamebarang_t.pegmengetahui_id, stokopnamebarang_t.petugas_id, ruangan_m.instalasi_id, stokopnamebarang_t.jenisstokopname, instalasi_m.instalasi_nama, jenis_stokopname.lookup_name, ruangan_m.ruangan_nama, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.nostokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, formsobarang_t.noformulir;

               ");
        
        $this->execute('
              ALTER TABLE infostokopnamebarang_v
        OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190411_011100_infostokopnamebarang_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_011100_infostokopnamebarang_v_update cannot be reverted.\n";

        return false;
    }
    */
}
