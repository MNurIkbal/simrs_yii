<?php

use yii\db\Migration;

/**
 * Class m220112_063900_migrate_US2677_bpjs_t
 */
class m220112_063900_migrate_US2677_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t" 
            ADD COLUMN IF NOT EXISTS "klsrawatnaik" int4,
            ADD COLUMN IF NOT EXISTS "pembiayaan" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "penanggung_jawab" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "tujuan_kunj" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "flag_procedure" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "kd_penunjang" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "assesment_pel" varchar(255) COLLATE "pg_catalog"."default";
            ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."klsrawatnaik" IS \'diisi jika naik kelas rawat\';
          ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."pembiayaan" IS \'Asuransi Kesehatan Tambahan. diisi jika naik kelas rawat\';
          ');

        $this->execute('COMMENT ON COLUMN "public"."bpjs_t"."penanggung_jawab" IS \'diisi jika naik kelas rawat\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_063900_migrate_US2677_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_063900_migrate_US2677_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
