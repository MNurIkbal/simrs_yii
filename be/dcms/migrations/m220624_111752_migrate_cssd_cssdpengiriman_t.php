<?php

use yii\db\Migration;

/**
 * Class m220624_111752_migrate_cssd_cssdpengiriman_t
 */
class m220624_111752_migrate_cssd_cssdpengiriman_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdpengiriman_t" (
          "cssdpengiriman_id" serial8,
          "cssd_id" int4,
          "ruanganasal_id" int4,
          "ruangantujuan_id" int4,
          "tglpengiriman" timestamp(6),
          "peg_mengetahui_id" int4,
          "peg_menyetujui_id" int4,
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "cssdpengiriman_t_pkey" PRIMARY KEY ("cssdpengiriman_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdpengiriman_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_111752_migrate_cssd_cssdpengiriman_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_111752_migrate_cssd_cssdpengiriman_t cannot be reverted.\n";

        return false;
    }
    */
}
