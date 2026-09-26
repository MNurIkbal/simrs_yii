<?php

use yii\db\Migration;

/**
 * Class m210225_063150_migrate_20210225_profilrumahsakit_v
 */
class m210225_063150_migrate_20210225_profilrumahsakit_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."profilrumahsakit_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"profilrumahsakit_v\" AS  SELECT profilrumahsakit_m.nokode_rumahsakit,
    profilrumahsakit_m.tglregistrasi,
    profilrumahsakit_m.nama_rumahsakit,
    fgetnamalookup(profilrumahsakit_m.jenis_rumahsakit) AS jenis_rs,
    fgetnamalookup(profilrumahsakit_m.kelas_rumahsakit::integer) AS kelas_rs,
    profilrumahsakit_m.nama_penyelenggara,
    profilrumahsakit_m.kode_pos,
    profilrumahsakit_m.no_telp_profilrs,
    profilrumahsakit_m.no_faksimili,
    profilrumahsakit_m.email,
    profilrumahsakit_m.notelphumas,
    profilrumahsakit_m.website,
    profilrumahsakit_m.luastanah,
    profilrumahsakit_m.luasbangunan,
    profilrumahsakit_m.nomor_suratizin,
    profilrumahsakit_m.tgl_suratizin,
    profilrumahsakit_m.oleh_suratizin,
    profilrumahsakit_m.sifat_suratizin,
    profilrumahsakit_m.masaberlaku_dari,
    profilrumahsakit_m.masaberlaku_sampai,
    profilrumahsakit_m.statuskepemilikanrs,
    profilrumahsakit_m.pentahapanakreditasrs,
    profilrumahsakit_m.statusakreditasrs,
    profilrumahsakit_m.tglakreditasi,
    profilrumahsakit_m.status_penyelenggara,
    profilrumahsakit_m.profilrs_id,
    profilrumahsakit_m.is_active,
    profilrumahsakit_m.kabupaten_id,
    kabupaten_m.kabupaten_nama AS kota,
    profilrumahsakit_m.alamatlokasi_rumahsakit
   FROM profilrumahsakit_m
     JOIN kabupaten_m ON profilrumahsakit_m.kabupaten_id = kabupaten_m.kabupaten_id
  WHERE profilrumahsakit_m.is_active = true AND profilrumahsakit_m.is_deleted = false;
");

        $this->execute('ALTER TABLE "public"."profilrumahsakit_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210225_063150_migrate_20210225_profilrumahsakit_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210225_063150_migrate_20210225_profilrumahsakit_v cannot be reverted.\n";

        return false;
    }
    */
}
