<?php

use yii\db\Migration;

/**
 * Class m210226_075218_migrate_20210226_validasipobarangdetail_t
 */
class m210226_075218_migrate_20210226_validasipobarangdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipobarangdetail_t" ADD COLUMN if not exists "purchasereqbrgdetail_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_075218_migrate_20210226_validasipobarangdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_075218_migrate_20210226_validasipobarangdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
