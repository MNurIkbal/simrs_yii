<?php

use yii\db\Migration;

/**
 * Class m210414_031915_migrate_20210414_3679_tabel_pendaftaran_t
 */
class m210414_031915_migrate_20210414_3679_tabel_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
          ADD COLUMN IF NOT EXISTS"dokterpengirim_id" int4,
          ADD COLUMN IF NOT EXISTS"styrujukaninstalasi_id" int4;
          ');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210414_031915_migrate_20210414_3679_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210414_031915_migrate_20210414_3679_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
