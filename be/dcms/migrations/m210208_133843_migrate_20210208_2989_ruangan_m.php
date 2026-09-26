<?php

use yii\db\Migration;

/**
 * Class m210208_133843_migrate_20210208_2989_ruangan_m
 */
class m210208_133843_migrate_20210208_2989_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."ruangan_m" ADD COLUMN IF NOT EXISTS "jenis_ruangan" varchar(30) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210208_133843_migrate_20210208_2989_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_133843_migrate_20210208_2989_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
