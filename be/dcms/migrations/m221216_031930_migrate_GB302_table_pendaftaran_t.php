<?php

use yii\db\Migration;

/**
 * Class m221216_031930_migrate_GB302_table_pendaftaran_t
 */
class m221216_031930_migrate_GB302_table_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pendaftaran_t" 
              ADD IF NOT EXISTS "is_ditagihkan" bool DEFAULT true,
              ADD IF NOT EXISTS "alasan_batalstop" text;
        '); 

        $this->execute('
            COMMENT ON COLUMN "public"."pendaftaran_t"."is_ditagihkan" IS \'flag jika batal stop akomodasi dan ditagihkan\';

        ');

        $this->execute('
            COMMENT ON COLUMN "public"."pendaftaran_t"."alasan_batalstop" IS \'alasan pembatalan stop akomodasi\';
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221216_031930_migrate_GB302_table_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221216_031930_migrate_GB302_table_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
