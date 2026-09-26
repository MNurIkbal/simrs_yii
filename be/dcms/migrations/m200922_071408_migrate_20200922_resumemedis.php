<?php

use yii\db\Migration;

/**
 * Class m200922_071408_migrate_20200922_resumemedis
 */
class m200922_071408_migrate_20200922_resumemedis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->execute('ALTER TABLE "public"."resumemedis_t" ADD IF NOT EXISTS "catatan" varchar(255) COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_071408_migrate_20200922_resumemedis cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_071408_migrate_20200922_resumemedis cannot be reverted.\n";

        return false;
    }
    */
}
