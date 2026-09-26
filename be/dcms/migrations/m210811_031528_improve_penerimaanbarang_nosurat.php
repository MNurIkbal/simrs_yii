<?php

use yii\db\Migration;

/**
 * Class m210811_031528_improve_penerimaanbarang_nosurat
 */
class m210811_031528_improve_penerimaanbarang_nosurat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penerimaanbarang_t" ALTER COLUMN "no_suratjalan" DROP NOT NULL;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210811_031528_improve_penerimaanbarang_nosurat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210811_031528_improve_penerimaanbarang_nosurat cannot be reverted.\n";

        return false;
    }
    */
}
