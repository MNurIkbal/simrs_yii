<?php

use yii\db\Migration;

/**
 * Class m211022_093506_migrate_validasipo
 */
class m211022_093506_migrate_validasipo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipobarang_t" ALTER COLUMN "tgl_validasi" DROP NOT NULL;');
        $this->execute('ALTER TABLE "public"."validasipoobat_t" ALTER COLUMN "tgl_validasi" DROP NOT NULL;');

      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211022_093506_migrate_validasipo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_093506_migrate_validasipo cannot be reverted.\n";

        return false;
    }
    */
}
