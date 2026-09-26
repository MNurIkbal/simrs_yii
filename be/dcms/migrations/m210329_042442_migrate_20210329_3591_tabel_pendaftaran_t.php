<?php

use yii\db\Migration;

/**
 * Class m210329_042442_migrate_20210329_3591_tabel_pendaftaran_t
 */
class m210329_042442_migrate_20210329_3591_tabel_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
          ADD COLUMN IF NOT EXISTS"no_exportexcel" varchar(50);
          ');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_042442_migrate_20210329_3591_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_042442_migrate_20210329_3591_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
