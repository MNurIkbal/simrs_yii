<?php

use yii\db\Migration;

/**
 * Class m211224_030347_migrate_tindakanruangan_v
 */
class m211224_030347_migrate_tindakanruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."tindakanruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"tindakanruangan_v\" AS  SELECT tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanruangan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    daftartindakan_m.daftartindakan_namalainnya,
    kategoritindakan_m.kategoritindakan_nama,
    kelompoktindakan_m.kelompoktindakan_nama,
    jeniskegiatantindakan_m.jeniskegiatantindakan_nama,
    groupinacbg_m.groupinacbg_nama,
    tindakanruangan_mp.is_deleted,
    tindakanruangan_mp.is_active,
    tindakanruangan_mp.created_date,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM tindakanruangan_mp
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_kode,
            a.daftartindakan_nama,
            a.daftartindakan_namalainnya,
            a.kelompoktindakan_id,
            a.kategoritindakan_id,
            a.groupinacbg_id,
            a.jeniskegiatantindakan_id
           FROM daftartindakan_m a
          WHERE a.is_deleted = false AND a.is_active = true) daftartindakan_m ON tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.kategoritindakan_id,
            a.kategoritindakan_nama
           FROM kategoritindakan_m a) kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     LEFT JOIN ( SELECT a.kelompoktindakan_id,
            a.kelompoktindakan_nama
           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT a.jeniskegiatantindakan_id,
            a.jeniskegiatantindakan_nama
           FROM jeniskegiatantindakan_m a) jeniskegiatantindakan_m ON daftartindakan_m.jeniskegiatantindakan_id = jeniskegiatantindakan_m.jeniskegiatantindakan_id
     LEFT JOIN ( SELECT a.groupinacbg_id,
            a.groupinacbg_nama
           FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
  WHERE tindakanruangan_mp.is_deleted = false AND tindakanruangan_mp.is_active = true;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211224_030347_migrate_tindakanruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211224_030347_migrate_tindakanruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
