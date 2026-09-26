<?php

use yii\db\Migration;

/**
 * Class m220105_043241_migrate_smh_pendaftaranol_t
 */
class m220105_043241_migrate_smh_pendaftaranol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
            ADD COLUMN IF NOT EXISTS "email" varchar(50) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "note" text COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "reference_letter" text COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "no_rekam_medik" varchar(100) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "additional_jkn" text COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "referral_doctor_id" int4;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220105_043241_migrate_smh_pendaftaranol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220105_043241_migrate_smh_pendaftaranol_t cannot be reverted.\n";

        return false;
    }
    */
}
