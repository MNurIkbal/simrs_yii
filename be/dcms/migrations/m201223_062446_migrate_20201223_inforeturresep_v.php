<?php

use yii\db\Migration;

/**
 * Class m201223_062446_migrate_20201223_inforeturresep_v
 */
class m201223_062446_migrate_20201223_inforeturresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."inforeturresep_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inforeturresep_v\" AS  SELECT returresep_t.returresep_id,
    returresep_t.tgl_retur,
    returresep_t.no_returresep,
    penjualanresep_t.pasien_id,
    pasien_m.nama_pasien,
    returresep_t.penjualanresep_id,
    penjualanresep_t.noresep,
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjualanresep_t.penjamin_id,
    penjamin_m.penjamin_nama,
    returresep_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS nama_dokter
   FROM returresep_t
     JOIN penjualanresep_t ON returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON returresep_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
  WHERE returresep_t.is_active = true AND returresep_t.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."inforeturresep_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201223_062446_migrate_20201223_inforeturresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201223_062446_migrate_20201223_inforeturresep_v cannot be reverted.\n";

        return false;
    }
    */
}
