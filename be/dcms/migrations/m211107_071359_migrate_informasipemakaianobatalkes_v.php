<?php

use yii\db\Migration;

/**
 * Class m211107_071359_migrate_informasipemakaianobatalkes_v
 */
class m211107_071359_migrate_informasipemakaianobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists "public"."informasipemakaianobatalkes_v";');
       
       $this->execute("
        CREATE VIEW \"public\".\"informasipemakaianobatalkes_v\" AS  SELECT pemakaianobat_t.created_date AS tglpemakaianobat,
    pemakaianobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pemakaianobat_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemakaianobat_t.nopemakaian_obat,
    pemakaianobat_t.keterangan_pemakaianobat,
    pemakaianobat_t.pemakaianobat_id
   FROM pemakaianobat_t
     JOIN pegawai_m ON pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON pemakaianobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE pemakaianobat_t.is_active = true AND pemakaianobat_t.is_deleted = false;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211107_071359_migrate_informasipemakaianobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211107_071359_migrate_informasipemakaianobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
