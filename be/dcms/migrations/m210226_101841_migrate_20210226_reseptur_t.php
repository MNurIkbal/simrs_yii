<?php

use yii\db\Migration;

/**
 * Class m210226_101841_migrate_20210226_reseptur_t
 */
class m210226_101841_migrate_20210226_reseptur_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."reseptur_t" ADD COLUMN if not exists "catatan" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_101841_migrate_20210226_reseptur_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_101841_migrate_20210226_reseptur_t cannot be reverted.\n";

        return false;
    }
    */
}
