<?php

use yii\db\Migration;

/**
 * Class m230708_122220_lepas_validasi_jadwal_operasi_rpp284
 */
class m230708_122220_lepas_validasi_jadwal_operasi_rpp284 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->execute('UPDATE "public"."konfigsystem_k" SET "lepas_validasi_jadwal_operasi" = \'t\' WHERE "konfigsystem_id" = 1;');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('UPDATE "public"."konfigsystem_k" SET "lepas_validasi_jadwal_operasi" = \'f\' WHERE "konfigsystem_id" = 1;');

        /* echo "m230708_122220_lepas_validasi_jadwal_operasi_rpp284 cannot be reverted.\n"; */

        /* return false; */
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230708_122220_lepas_validasi_jadwal_operasi_rpp284 cannot be reverted.\n";

        return false;
    }
    */
}
