<?php

use yii\db\Migration;

/**
 * Class m210114_100334_migrate_20200114_validasipoobat_t
 */
class m210114_100334_migrate_20200114_validasipoobat_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                $this->execute('ALTER TABLE "public"."validasipoobat_t" ALTER COLUMN "supplier_id" DROP NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_100334_migrate_20200114_validasipoobat_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_100334_migrate_20200114_validasipoobat_t cannot be reverted.\n";

        return false;
    }
    */
}
