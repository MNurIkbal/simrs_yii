<?php

use yii\db\Migration;

/**
 * Class m210226_075023_migrate_20210226_validasipobarang_t
 */
class m210226_075023_migrate_20210226_validasipobarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipobarang_t" ALTER COLUMN "supplier_id" DROP NOT NULL;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_075023_migrate_20210226_validasipobarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_075023_migrate_20210226_validasipobarang_t cannot be reverted.\n";

        return false;
    }
    */
}
