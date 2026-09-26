<?php

use yii\db\Migration;

/**
 * Class m240515_090826_migrate_DSV_1277_penjamindiskon_m
 */
class m240515_090826_migrate_DSV_1277_penjamindiskon_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."penjamindiskon_m" (
                "penjamindiskon_id" serial4 NOT NULL,
                "penjamin_id" int4 NOT NULL,
                "diskon_otomatis" float8 NULL,
                "additional_data" text NULL,
                "created_date" timestamp NOT NULL DEFAULT \'now\'::text::date,
                "created_by" int4 NULL,
                "modified_count" int4 NULL,
                "last_modified_date" timestamp NULL,
                "last_modified_by" int4 NULL,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp NULL,
                "deleted_by" int4 NULL,
                CONSTRAINT "pk_penjamindiskon_m" PRIMARY KEY ("penjamindiskon_id")
            )
        ');
        $this->execute('
            ALTER TABLE "public"."penjamindiskon_m" OWNER TO "postgres";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240515_090826_migrate_DSV_1277_penjamindiskon_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240515_090826_migrate_DSV_1277_penjamindiskon_m cannot be reverted.\n";

        return false;
    }
    */
}
