<?php

use yii\db\Migration;

/**
 * Class m211202_013748_migrate_resumemedis_t
 */
class m211202_013748_migrate_resumemedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute('ALTER TABLE "public"."resumemedis_t" 
                              ALTER COLUMN "saran" TYPE text COLLATE "pg_catalog"."default" USING "saran"::text,
                              ALTER COLUMN "catatan" TYPE text COLLATE "pg_catalog"."default" USING "catatan"::text;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211202_013748_migrate_resumemedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211202_013748_migrate_resumemedis_t cannot be reverted.\n";

        return false;
    }
    */
}
