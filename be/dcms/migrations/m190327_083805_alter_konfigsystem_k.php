<?php

use yii\db\Migration;

/**
 * Class m190327_083805_alter_konfigsystem_k
 */

use Doco\components\DocoHelpers;

class m190327_083805_alter_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = DocoHelpers::checkSchemaTabel('konfigsystem_k',[
            'reservasi_awal',
            'reservasi_akhir'
        ]);
        if (empty($schema)) {
            $this->execute('
                ALTER TABLE "public"."konfigsystem_k" 
                  ADD COLUMN "reservasi_awal" int2,
                  ADD COLUMN "reservasi_akhir" int2;
            ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_083805_alter_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_083805_alter_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
