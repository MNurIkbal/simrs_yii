<?php

use yii\db\Migration;

/**
 * Class m220624_114010_migrate_cssd_cssdrusak_t
 */
class m220624_114010_migrate_cssd_cssdrusak_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdrusak_t" (
          "cssdrusak_id" serial8,
          "cssdsterilisasi_id" int4 NOT NULL,
          "tgl_selesai" timestamp(6),
          "catatan" text COLLATE "pg_catalog"."default",
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "Untitled_pkey" PRIMARY KEY ("cssdrusak_id")
      )
      ;
      ');

        $this->execute('ALTER TABLE "public"."cssdrusak_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_114010_migrate_cssd_cssdrusak_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_114010_migrate_cssd_cssdrusak_t cannot be reverted.\n";

        return false;
    }
    */
}
