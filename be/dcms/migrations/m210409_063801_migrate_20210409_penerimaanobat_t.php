<?php

use yii\db\Migration;

/**
 * Class m210409_063801_migrate_20210409_penerimaanobat_t
 */
class m210409_063801_migrate_20210409_penerimaanobat_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."penerimaanobat_t" ADD COLUMN if not exists "no_faktur_sementara" varchar(100) COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210409_063801_migrate_20210409_penerimaanobat_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210409_063801_migrate_20210409_penerimaanobat_t cannot be reverted.\n";

        return false;
    }
    */
}
