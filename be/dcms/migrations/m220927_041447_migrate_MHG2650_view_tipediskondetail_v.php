<?php

use yii\db\Migration;

/**
 * Class m220927_041447_migrate_MHG2650_view_tipediskondetail_v
 */
class m220927_041447_migrate_MHG2650_view_tipediskondetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."tipediskondetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."tipediskondetail_v" AS  
            SELECT tipediskondetail_m.tipediskondetail_id,
                tipediskon_m.tipediskon_id,
                tipediskondetail_m.jenislayanan_id, 
                lookup_m.lookup_name AS jenis_layanan,
                tipediskondetail_m.layanan_id,
                    CASE
                        WHEN tipediskondetail_m.jenislayanan_id = 1033 THEN kelaspelayanan_m.kelaspelayanan_nama
                        WHEN tipediskondetail_m.jenislayanan_id = 1025 THEN kelompoktindakan_m.kelompoktindakan_nama
                        WHEN tipediskondetail_m.jenislayanan_id = 1028 THEN obatalkes_m.obatalkes_nama
                        WHEN tipediskondetail_m.jenislayanan_id = 1290 THEN jenisobatalkes_m.jenisobatalkes_nama
                        WHEN tipediskondetail_m.jenislayanan_id = 1297 THEN lookup_m.lookup_name
                        ELSE daftartindakan_m.daftartindakan_nama
                    END AS layanan,
                tipediskondetail_m.disc_persen,
                tipediskondetail_m.max_dijamin
               FROM tipediskondetail_m
                 JOIN ( SELECT a.tipediskon_id,
                        a.tipediskon_nama
                       FROM tipediskon_m a
                      WHERE a.is_deleted = false) tipediskon_m ON tipediskondetail_m.tipediskon_id = tipediskon_m.tipediskon_id
                 LEFT JOIN ( SELECT e.kelaspelayanan_id,
                        e.kelaspelayanan_nama
                       FROM kelaspelayanan_m e) kelaspelayanan_m ON tipediskondetail_m.jenislayanan_id = 1033 AND tipediskondetail_m.layanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT b.kelompoktindakan_id,
                        b.kelompoktindakan_nama
                       FROM kelompoktindakan_m b) kelompoktindakan_m ON tipediskondetail_m.jenislayanan_id = 1025 AND tipediskondetail_m.layanan_id = kelompoktindakan_m.kelompoktindakan_id
                 LEFT JOIN ( SELECT c.daftartindakan_id,
                        c.daftartindakan_nama
                       FROM daftartindakan_m c) daftartindakan_m ON tipediskondetail_m.jenislayanan_id = 1026 AND tipediskondetail_m.layanan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN ( SELECT a.obatalkes_id,
                        a.obatalkes_nama
                       FROM obatalkes_m a) obatalkes_m ON tipediskondetail_m.layanan_id = obatalkes_m.obatalkes_id AND tipediskondetail_m.jenislayanan_id = 1028
                 LEFT JOIN ( SELECT a.jenisobatalkes_id,
                        a.jenisobatalkes_nama
                       FROM jenisobatalkes_m a) jenisobatalkes_m ON tipediskondetail_m.layanan_id = jenisobatalkes_m.jenisobatalkes_id AND tipediskondetail_m.jenislayanan_id = 1290
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) lookup_m ON tipediskondetail_m.jenislayanan_id = lookup_m.lookup_id
              WHERE tipediskondetail_m.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220927_041447_migrate_MHG2650_view_tipediskondetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_041447_migrate_MHG2650_view_tipediskondetail_v cannot be reverted.\n";

        return false;
    }
    */
}
