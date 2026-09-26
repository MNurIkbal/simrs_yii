<?php

use yii\db\Migration;

/**
 * Class m210220_143614_migrate_20210220_resumemedisri_t
 */
class m210220_143614_migrate_20210220_resumemedisri_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "anamnesa" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "pemeriksaan_fisik" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "prosedur" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "konsultasi" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "obat_rs" text COLLATE "pg_catalog"."default";');

        $this->execute('ALTER TABLE "public"."resumemedisri_t" ADD COLUMN IF NOT EXISTS "lain_lainnya" text COLLATE "pg_catalog"."default";');
 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210220_143614_migrate_20210220_resumemedisri_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210220_143614_migrate_20210220_resumemedisri_t cannot be reverted.\n";

        return false;
    }
    */
}
