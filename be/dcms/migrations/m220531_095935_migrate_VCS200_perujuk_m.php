<?php

use yii\db\Migration;

/**
 * Class m220531_095935_migrate_VCS200_perujuk_m
 */
class m220531_095935_migrate_VCS200_perujuk_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public".perujuk_m ADD COLUMN IF NOT EXISTS "sync_id" varchar;');
        $this->execute('ALTER TABLE "public".perujuk_m ADD COLUMN IF NOT EXISTS "perujuk_kode" varchar(50);');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_095935_migrate_VCS200_perujuk_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_095935_migrate_VCS200_perujuk_m cannot be reverted.\n";

        return false;
    }
    */
}
