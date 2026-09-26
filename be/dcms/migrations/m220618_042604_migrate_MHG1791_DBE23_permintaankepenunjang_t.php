<?php

use yii\db\Migration;

/**
 * Class m220618_042604_migrate_MHG1791_DBE23_permintaankepenunjang_t
 */
class m220618_042604_migrate_MHG1791_DBE23_permintaankepenunjang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."permintaankepenunjang_t" 
          ADD COLUMN IF NOT EXISTS "is_referred" bool DEFAULT false;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220618_042604_migrate_MHG1791_DBE23_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220618_042604_migrate_MHG1791_DBE23_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }
    */
}
