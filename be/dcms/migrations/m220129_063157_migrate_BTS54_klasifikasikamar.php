<?php

use yii\db\Migration;

/**
 * Class m220129_063157_migrate_BTS54_klasifikasikamar
 */
class m220129_063157_migrate_BTS54_klasifikasikamar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."klasifikasikamar_m" (
            "klasifikasikamar_id" serial4,
          "sirsonline_id" int4,
          "eiscovid_id" int4,
          "applicare_id" int4,
          "spgdt_id" int4,
          "klasifikasikamar_nama" varchar(100) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "klasifikasikamar_m_pkey" PRIMARY KEY ("klasifikasikamar_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."klasifikasikamar_m" 
          OWNER TO "postgres";
          ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."sirsonline_m" (
            "sirsonline_id" serial4,
          "sirsonline_nama" varchar(100) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "sirsonline_m_pkey" PRIMARY KEY ("sirsonline_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."sirsonline_m" 
          OWNER TO "postgres";
          ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."eiscovid_m" (
          "eiscovid_id" serial4,
          "eiscovid_nama" varchar(100) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "eiscovid_m_pkey" PRIMARY KEY ("eiscovid_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."eiscovid_m" 
          OWNER TO "postgres";
          ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."applicare_m" (
          "applicare_id" serial4,
          "applicare_nama" varchar(100) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "applicare_m_pkey" PRIMARY KEY ("applicare_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."applicare_m" 
          OWNER TO "postgres";
          ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."spgdt_m" (
          "spgdt_id" serial4,
          "spgdt_nama" varchar(100) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "spgdt_m_pkey" PRIMARY KEY ("spgdt_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."spgdt_m" 
          OWNER TO "postgres";
          ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220129_063157_migrate_BTS54_klasifikasikamar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220129_063157_migrate_BTS54_klasifikasikamar cannot be reverted.\n";

        return false;
    }
    */
}
