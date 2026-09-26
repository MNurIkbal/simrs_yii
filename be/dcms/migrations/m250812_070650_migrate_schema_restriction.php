<?php

use yii\db\Migration;

/**
 * Class m250812_070650_migrate_schema_restriction
 */
class m250812_070650_migrate_schema_restriction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS restriction_obat_m");
        $this->execute('CREATE TABLE "public"."restriction_obat_m" (
                                    "restriction_obat_id" serial4 NOT NULL,
                                    "restriction_obat_nama" varchar(150) COLLATE "pg_catalog"."default",
                                    "restriction_obat_namalainnya" varchar(150) COLLATE "pg_catalog"."default",
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
                                    CONSTRAINT "pk_restriction_obat" PRIMARY KEY ("restriction_obat_id")
                            );');

        $this->execute("DROP TABLE IF EXISTS restriction_akses_obat_mp");
        $this->execute('CREATE TABLE "public"."restriction_akses_obat_mp" (
                        "restriction_akses_obat_id" serial4 NOT NULL,
                        "restriction_obat_id" int4 NOT NULL,
                        "penjamin_id" int4 NOT NULL,
                        "instalasi_id" int4 NOT NULL,
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
                        CONSTRAINT "pk_restriction_akses_obat" PRIMARY KEY ("restriction_akses_obat_id")
                        )
                        ;
        ');


        $this->execute("DROP TABLE IF EXISTS restriction_list_obat_mp");
        $this->execute('CREATE TABLE "public"."restriction_list_obat_mp" (
                        "restriction_list_obat_id" serial4 NOT NULL,
                        "restriction_obat_id" int4 NOT NULL,
                        "obatalkes_id" int4 NOT NULL,
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
                        CONSTRAINT "pk_restriction_list_obat" PRIMARY KEY ("restriction_list_obat_id")
                        )
                        ;
                        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250812_070650_migrate_schema_restriction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250812_070650_migrate_schema_restriction cannot be reverted.\n";

        return false;
    }
    */
}
