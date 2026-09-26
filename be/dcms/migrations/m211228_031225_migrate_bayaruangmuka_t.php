<?php

use yii\db\Migration;

/**
 * Class m211228_031225_migrate_bayaruangmuka_t
 */
class m211228_031225_migrate_bayaruangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bayaruangmuka_t" ADD COLUMN if not exists "alasan_batal" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211228_031225_migrate_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_031225_migrate_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
