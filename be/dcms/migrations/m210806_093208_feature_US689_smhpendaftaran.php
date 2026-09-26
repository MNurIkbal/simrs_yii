<?php

use yii\db\Migration;

/**
 * Class m210806_093208_feature_US689_smhpendaftaran
 */
class m210806_093208_feature_US689_smhpendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
          ADD COLUMN IF NOT EXISTS "referral_doctor_id" int4,
          ADD COLUMN IF NOT EXISTS "email" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "note" text COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "reference_letter" text COLLATE "pg_catalog"."default";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210806_093208_feature_US689_smhpendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210806_093208_feature_US689_smhpendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
