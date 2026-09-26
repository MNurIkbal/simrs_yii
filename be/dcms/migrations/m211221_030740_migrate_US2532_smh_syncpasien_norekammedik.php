<?php

use yii\db\Migration;

/**
 * Class m211221_030740_migrate_US2532_smh_syncpasien_norekammedik
 */
class m211221_030740_migrate_US2532_smh_syncpasien_norekammedik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
          ADD COLUMN IF NOT EXISTS "no_rekam_medik" varchar(100) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_030740_migrate_US2532_smh_syncpasien_norekammedik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_030740_migrate_US2532_smh_syncpasien_norekammedik cannot be reverted.\n";

        return false;
    }
    */
}
