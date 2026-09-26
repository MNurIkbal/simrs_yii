<?php

use yii\db\Migration;

/**
 * Class m211117_041141_migrate_hotfix_slotjadwaldokter_m
 */
class m211117_041141_migrate_hotfix_slotjadwaldokter_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."slotjadwaldokter_m" (
          "slotjadwaldokter_id" serial4,
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
          CONSTRAINT "slotjadwaldokter_m_pkey" PRIMARY KEY ("slotjadwaldokter_id")
      )
      ;
        ');

        $this->execute('ALTER TABLE "public"."slotjadwaldokter_m" 
          OWNER TO "postgres";
          ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211117_041141_migrate_hotfix_slotjadwaldokter_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211117_041141_migrate_hotfix_slotjadwaldokter_m cannot be reverted.\n";

        return false;
    }
    */
}
