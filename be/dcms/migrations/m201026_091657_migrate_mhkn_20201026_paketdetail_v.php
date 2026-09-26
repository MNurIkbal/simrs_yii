<?php

use yii\db\Migration;

/**
 * Class m201026_091657_migrate_mhkn_20201026_paketdetail_v
 */
class m201026_091657_migrate_mhkn_20201026_paketdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.paketdetail_v;');
        $this->execute("CREATE VIEW \"public\".\"paketdetail_v\" AS
             SELECT tipepaket_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    daftartindakan_m.daftartindakan_nama,
    paketpelayanan_mp.daftartindakan_id,
    paketpelayanan_mp.is_deleted,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    tipepaket_m.tipepaket_kode
   FROM (((tipepaket_m
     JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)));");

        $this->execute('ALTER TABLE public.paketdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201026_091657_migrate_mhkn_20201026_paketdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_091657_migrate_mhkn_20201026_paketdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
