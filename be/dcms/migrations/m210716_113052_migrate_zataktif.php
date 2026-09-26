<?php

use yii\db\Migration;

/**
 * Class m210716_113052_migrate_zataktif
 */
class m210716_113052_migrate_zataktif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 $this->execute('CREATE TABLE IF NOT exists "public"."zataktifobat_mp" (
  "obatalkes_id" int4 NOT NULL,
  "zataktif_id" int4 NOT NULL,
  "is_primary" bool DEFAULT false,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4
)
;');

 $this->execute('ALTER TABLE "public"."zataktifobat_mp" OWNER TO "postgres";');

 $this->execute('DROP VIEW if exists "public"."infozataktifobat_v";');
 
 $this->execute("
    CREATE VIEW \"public\".\"infozataktifobat_v\" AS  SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    zataktifobat_mp.zataktif_id,
    zataktifobat_mp.zataktif_nama,
    zataktifobat_mp.is_primary,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.obatalkes_kode,
    zataktifobat_mp.jumlah_zataktif,
    obatalkes_m.jenisobatalkes_id
   FROM obatalkes_m
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.zataktif_id,
            a.is_primary,
            zataktif_m.zataktif_nama,
            count(*) OVER (PARTITION BY a.obatalkes_id) AS jumlah_zataktif
           FROM zataktifobat_mp a
             LEFT JOIN zataktif_m ON a.zataktif_id = zataktif_m.zataktif_id
          WHERE a.is_deleted = false) zataktifobat_mp ON obatalkes_m.obatalkes_id = zataktifobat_mp.obatalkes_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
  WHERE obatalkes_m.is_deleted = false;");

 $this->execute('ALTER TABLE "public"."infozataktifobat_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210716_113052_migrate_zataktif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210716_113052_migrate_zataktif cannot be reverted.\n";

        return false;
    }
    */
}
