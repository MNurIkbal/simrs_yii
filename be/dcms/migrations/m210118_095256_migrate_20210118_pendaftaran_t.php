<?php

use yii\db\Migration;

/**
 * Class m210118_095256_migrate_20210118_pendaftaran_t
 */
class m210118_095256_migrate_20210118_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
  ADD COLUMN IF NOT EXISTS"penanggungbiaya_id" int4;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210118_095256_migrate_20210118_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_095256_migrate_20210118_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
