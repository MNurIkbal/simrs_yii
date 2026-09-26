<?php

use yii\db\Migration;

/**
 * Class m220715_101002_create_view_drugsupport
 */
class m220715_101002_create_view_drugsupport extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DROP VIEW IF EXISTS "public"."anestesikondisipasiendrugsupport_v";
        ');
        $this->execute('
        CREATE OR REPLACE VIEW "public"."anestesikondisipasiendrugsupport_v"
        AS SELECT "drugsupport"."anestesikondisipasiendrugsupport_id",
            "drugsupport"."anestesikondisipasien_id",
            "obatalkes"."obatalkes_id",
            "obatalkes"."obatalkes_kode",
            "obatalkes"."obatalkes_nama",
            "drugsupport"."dose",
            "drugsupport"."time_delivery",
            "drugsupport"."is_active",
            "drugsupport"."is_deleted",
            "drugsupport"."created_date",
            "drugsupport"."created_by"
        FROM "anestesikondisipasiendrugsupport_t" "drugsupport"
            JOIN "obatalkes_m" "obatalkes" ON "obatalkes"."obatalkes_id" = "drugsupport"."obatalkes_id";
        ');

        $this->execute('
        DROP VIEW IF EXISTS "public"."anestesipostoprdrugsupport_v";
        ');
        $this->execute('
        CREATE OR REPLACE VIEW "public"."anestesipostoprdrugsupport_v"
        AS SELECT "drugsupport"."anestesipostoprdrugsupport_id",
            "drugsupport"."anestesipostopr_id",
            "obatalkes"."obatalkes_id",
            "obatalkes"."obatalkes_kode",
            "obatalkes"."obatalkes_nama",
            "drugsupport"."dose",
            "drugsupport"."time_delivery",
            "drugsupport"."is_active",
            "drugsupport"."is_deleted",
            "drugsupport"."created_date",
            "drugsupport"."created_by"
        FROM "anestesipostoprdrugsupport_t" "drugsupport"
            JOIN "obatalkes_m" "obatalkes" ON "obatalkes"."obatalkes_id" = "drugsupport"."obatalkes_id";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_101002_create_view_drugsupport cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_101002_create_view_drugsupport cannot be reverted.\n";

        return false;
    }
    */
}