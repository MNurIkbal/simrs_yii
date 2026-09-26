<?php

use yii\db\Migration;

/**
 * Class m220425_080831_migrate_MHG629_slotjadwaldokter_r
 */
class m220425_080831_migrate_MHG629_slotjadwaldokter_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."slotjadwaldokter_r" (
          "slotjadwaldokterrekap_id" serial8,
          "slotjadwaldokter_id" int4,
          "jadwaldokter_id" int4,
          "slot_sequence" int4,
          "jam_mulai" time(6),
          "jam_selesai" time(6),
          "slot_type" bool,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6),
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool,
          "is_active" bool,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "tipe_cara_bayar" varchar(20) COLLATE "pg_catalog"."default",
          CONSTRAINT "slotjadwaldokter_r_pkey" PRIMARY KEY ("slotjadwaldokterrekap_id")
      )
      ;');

        $this->execute('ALTER TABLE "public"."slotjadwaldokter_r" 
          OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220425_080831_migrate_MHG629_slotjadwaldokter_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_080831_migrate_MHG629_slotjadwaldokter_r cannot be reverted.\n";

        return false;
    }
    */
}
