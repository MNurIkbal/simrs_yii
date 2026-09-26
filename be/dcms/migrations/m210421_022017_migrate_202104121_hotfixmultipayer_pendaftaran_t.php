<?php

use yii\db\Migration;

/**
 * Class m210421_022017_migrate_202104121_hotfixmultipayer_pendaftaran_t
 */
class m210421_022017_migrate_202104121_hotfixmultipayer_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
            ADD COLUMN IF NOT EXISTS "is_multipayer" bool DEFAULT false;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210421_022017_migrate_202104121_hotfixmultipayer_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210421_022017_migrate_202104121_hotfixmultipayer_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
