<?php

use yii\db\Migration;

/**
 * Class m210121_070833_improve_add_table_cathlab
 */
class m210121_070833_improve_add_table_cathlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TABLE IF EXISTS "public"."cathlab_t";
        ');

        $this->execute('
            CREATE TABLE "public"."cathlab_t" (
              "cathlab_id" serial8 NOT NULL PRIMARY KEY,
              "pendaftaran_id" int4,
              "pasienadmisi_id" int4,
              "tipe" varchar(20) COLLATE "pg_catalog"."default",
              "tgl_cathlab" timestamp(6),
              "indikasi" text COLLATE "pg_catalog"."default",
              "approach" text COLLATE "pg_catalog"."default",
              "target" text COLLATE "pg_catalog"."default",
              "koroner" text COLLATE "pg_catalog"."default",
              "lm" varchar(100) COLLATE "pg_catalog"."default",
              "lad" varchar(100) COLLATE "pg_catalog"."default",
              "lcx" varchar(100) COLLATE "pg_catalog"."default",
              "rca" varchar(100) COLLATE "pg_catalog"."default",
              "laporan_pci" text COLLATE "pg_catalog"."default",
              "laporan_dsa" text COLLATE "pg_catalog"."default",
              "lain_lain" text COLLATE "pg_catalog"."default",
              "kesimpulan" text COLLATE "pg_catalog"."default",
              "saran" text COLLATE "pg_catalog"."default",
              "cum_air_kerma" varchar(50) COLLATE "pg_catalog"."default",
              "cum_dap" varchar(50) COLLATE "pg_catalog"."default",
              "fluo_time" varchar(50) COLLATE "pg_catalog"."default",
              "kontras" varchar(50) COLLATE "pg_catalog"."default",
              "procedure_time" varchar(50) COLLATE "pg_catalog"."default",
              "operator_id" int4,
              "tgl_prosedure" timestamp(6),
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_070833_improve_add_table_cathlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_070833_improve_add_table_cathlab cannot be reverted.\n";

        return false;
    }
    */
}
