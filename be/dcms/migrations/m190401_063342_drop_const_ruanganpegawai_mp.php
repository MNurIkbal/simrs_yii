<?php

use yii\db\Migration;

/**
 * Class m190401_063342_drop_const_ruanganpegawai_mp
 */
class m190401_063342_drop_const_ruanganpegawai_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."ruanganpegawai_mp" 
              DROP CONSTRAINT "pk_ruanganpegawai";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_063342_drop_const_ruanganpegawai_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_063342_drop_const_ruanganpegawai_mp cannot be reverted.\n";

        return false;
    }
    */
}
