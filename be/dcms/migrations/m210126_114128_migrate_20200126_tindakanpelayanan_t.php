<?php

use yii\db\Migration;

/**
 * Class m210126_114128_migrate_20200126_tindakanpelayanan_t
 */
class m210126_114128_migrate_20200126_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN if not exists "tarif_diskon" float8 DEFAULT 0;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_114128_migrate_20200126_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_114128_migrate_20200126_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
