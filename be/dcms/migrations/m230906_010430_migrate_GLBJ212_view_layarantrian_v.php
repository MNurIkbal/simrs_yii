<?php

use yii\db\Migration;

/**
 * Class m230906_010430_migrate_GLBJ212_view_layarantrian_v
 */
class m230906_010430_migrate_GLBJ212_view_layarantrian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."layarantrian_v";
        '); 

        $this->execute('
            CREATE VIEW "public"."layarantrian_v" AS  SELECT DISTINCT layarantrian_m.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    layarantrian_m.layarantrian_judul,
    layarantrian_m.jenisantrian_id,
    fgetnamalookup(layarantrian_m.jenisantrian_id) AS jenis_antrian,
    jenisantriandetail_m.jenisantriandetail_id,
    jenisantriandetail_m.nama AS lantai,
    loket_m.loket_id,
    loket_m.loket_nama AS loket,
    layarantrian_m.konfigantrian_id,
    layarantrian_m.layarantrian_latarbelakang,
    layarantrian_m.layarantrian_maxitem,
    layarantrian_m.layarantrian_itemhigh,
    layarantrian_m.layarantrian_itemwidth,
    layarantrian_m.layarantrian_intrefresh, 
    layarantrian_m.is_active,
    layarantrian_m.is_deleted
   FROM layarantrian_m
     JOIN layarantriandetail_m ON layarantrian_m.layarantrian_id = layarantriandetail_m.layarantrian_id
     JOIN loket_m ON layarantriandetail_m.loket_id = loket_m.loket_id
     JOIN loketjenisantrian_mp ON loket_m.loket_id = loketjenisantrian_mp.loket_id
     JOIN jenisantriandetail_m ON loketjenisantrian_mp.jenisantriandetail_id = jenisantriandetail_m.jenisantriandetail_id
  WHERE layarantriandetail_m.is_deleted = false AND layarantrian_m.is_deleted = false
UNION ALL
 SELECT DISTINCT layarantrian_m.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    layarantrian_m.layarantrian_judul,
    layarantrian_m.jenisantrian_id,
    fgetnamalookup(layarantrian_m.jenisantrian_id) AS jenis_antrian,
    NULL::integer AS jenisantriandetail_id,
    NULL::text AS lantai,
    layarantriandetail_m.loket_id,
    loket_m.loket_nama AS loket,
    layarantrian_m.konfigantrian_id,
    layarantrian_m.layarantrian_latarbelakang,
    layarantrian_m.layarantrian_maxitem,
    layarantrian_m.layarantrian_itemhigh,
    layarantrian_m.layarantrian_itemwidth,
    layarantrian_m.layarantrian_intrefresh,
    layarantrian_m.is_active,
    layarantrian_m.is_deleted
   FROM layarantrian_m
     JOIN layarantriandetail_m ON layarantrian_m.layarantrian_id = layarantriandetail_m.layarantrian_id
     LEFT JOIN loket_m ON layarantriandetail_m.loket_id = loket_m.loket_id
  WHERE layarantrian_m.is_deleted IS FALSE AND layarantrian_m.is_active IS TRUE AND layarantriandetail_m.loket_id IS NULL;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230906_010430_migrate_GLBJ212_view_layarantrian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230906_010430_migrate_GLBJ212_view_layarantrian_v cannot be reverted.\n";

        return false;
    }
    */
}
