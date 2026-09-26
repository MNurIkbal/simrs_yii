<?php

use yii\db\Migration;

/**
 * Class m220324_180856_migrate_skema_daftarpaketfisiodet_m
 */
class m220324_180856_migrate_skema_daftarpaketfisiodet_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."daftarpaketfisiodet_m" (
          "daftarpaketfisiodet_id" serial8,
          "daftarpaketfisio_id" int4,
          "daftartindakan_id" int4,
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
          CONSTRAINT "daftarpaketfisiodet_m_pkey" PRIMARY KEY ("daftarpaketfisiodet_id")
            )
            ;
          ');

        $this->execute('ALTER TABLE "public"."daftarpaketfisiodet_m" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_180856_migrate_skema_daftarpaketfisiodet_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_180856_migrate_skema_daftarpaketfisiodet_m cannot be reverted.\n";

        return false;
    }
    */
}
