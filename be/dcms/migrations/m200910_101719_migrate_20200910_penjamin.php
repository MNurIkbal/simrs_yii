<?php

use yii\db\Migration;

/**
 * Class m200910_101719_migrate_20200910_penjamin
 */
class m200910_101719_migrate_20200910_penjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penjamin_m" ADD COLUMN "penjamin_kode" varchar(100) COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200910_101719_migrate_20200910_penjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200910_101719_migrate_20200910_penjamin cannot be reverted.\n";

        return false;
    }
    */
}
