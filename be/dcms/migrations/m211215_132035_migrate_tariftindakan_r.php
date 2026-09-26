<?php

use yii\db\Migration;

/**
 * Class m211215_132035_migrate_tariftindakan_r
 */
class m211215_132035_migrate_tariftindakan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."tariftindakan_r" (
  "id" serial8,
  "tariftindakan_id" int4,
  "kelaspelayanan_id" int4,
  "komponentarif_id" int4,
  "daftartindakan_id" int4,
  "jenistarif_id" int4,
  "perdatarif_id" int4,
  "harga_tariftindakan" numeric(18,2),
  "persendiskon_tindakan" numeric(18,2),
  "hargadiskon_tindakan" numeric(18,2),
  "persencyto_tindakan" numeric(18,2),
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "tipepaket_id" int4,
  "penjamin_id" int4,
  "is_clone" bool,
  "tarifparent_id" int4,
  "kamarruangan_id" int4,
  "persen_penyulit" numeric(15,2),
  "dokter_id" int4,
  "ruangan_id" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6),
  CONSTRAINT "tariftindakan_r_pkey" PRIMARY KEY ("id")
)
;');

        $this->execute('ALTER TABLE "public"."tariftindakan_r" 
  OWNER TO "postgres";');

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211215_132035_migrate_tariftindakan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211215_132035_migrate_tariftindakan_r cannot be reverted.\n";

        return false;
    }
    */
}
