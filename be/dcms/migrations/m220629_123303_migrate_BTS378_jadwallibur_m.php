<?php

use yii\db\Migration;

/**
 * Class m220629_123303_migrate_BTS378_jadwallibur_m
 */
class m220629_123303_migrate_BTS378_jadwallibur_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."jadwallibur_m" (
            "jadwallibur_id" serial8,
            "tgl_libur" date,
            "ket_libur" text COLLATE "pg_catalog"."default",
            "is_liburnasional" bool DEFAULT false,
            "additional_data" text COLLATE "pg_catalog"."default",
            "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4,
            "modified_count" int4,
            "last_modified_date" timestamp(6),
            "last_modified_by" int4,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool DEFAULT true,
            "deleted_date" timestamp(6),
            "deleted_by" int4,
            CONSTRAINT "jadwallibur_m_pkey" PRIMARY KEY ("jadwallibur_id")
            )
            ;
            ');

        $this->execute('
            ALTER TABLE "public"."jadwallibur_m" 
            OWNER TO "postgres";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_123303_migrate_BTS378_jadwallibur_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_123303_migrate_BTS378_jadwallibur_m cannot be reverted.\n";

        return false;
    }
    */
}
