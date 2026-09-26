<?php

use yii\db\Migration;

/**
 * Class m190327_094805_alter_komponentarif_m
 */

use Doco\components\DocoHelpers;

class m190327_094805_alter_komponentarif_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = DocoHelpers::checkSchemaTabel('komponentarif_m',[
            'is_dokter',
            'is_perawat',
            'is_fisioterapis',
            'is_dietisien',
            'is_radiografer',
        ]);
        if (empty($schema)) {
            $this->execute('
                ALTER TABLE "public"."komponentarif_m" 
                  ADD COLUMN "is_dokter" bool,
                  ADD COLUMN "is_perawat" bool,
                  ADD COLUMN "is_fisioterapis" bool,
                  ADD COLUMN "is_dietisien" bool,
                  ADD COLUMN "is_radiografer" bool;
            ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_094805_alter_komponentarif_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_094805_alter_komponentarif_m cannot be reverted.\n";

        return false;
    }
    */
}
