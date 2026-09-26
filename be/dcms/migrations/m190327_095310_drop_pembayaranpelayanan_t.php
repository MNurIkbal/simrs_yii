<?php

use yii\db\Migration;

/**
 * Class m190327_095310_drop_pembayaranpelayanan_t
 */

use Doco\components\DocoHelpers;

class m190327_095310_drop_pembayaranpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = DocoHelpers::checkSchemaTabel('pembayaranpelayanan_t','pasien_id');
        if (!empty($schema)) {
            $this->execute('
                ALTER TABLE "public"."pembayaranpelayanan_t" 
                  ALTER COLUMN "pasien_id" DROP NOT NULL;
            ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_095310_drop_pembayaranpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_095310_drop_pembayaranpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
