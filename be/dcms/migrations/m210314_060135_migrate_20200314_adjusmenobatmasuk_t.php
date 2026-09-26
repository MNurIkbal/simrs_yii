<?php

use yii\db\Migration;

/**
 * Class m210314_060135_migrate_20200314_adjusmenobatmasuk_t
 */
class m210314_060135_migrate_20200314_adjusmenobatmasuk_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('COMMENT ON COLUMN "public"."adjusmenobatmasuk_t"."harga_netto" IS \'untuk menyimpan total harga netto\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210314_060135_migrate_20200314_adjusmenobatmasuk_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210314_060135_migrate_20200314_adjusmenobatmasuk_t cannot be reverted.\n";

        return false;
    }
    */
}
