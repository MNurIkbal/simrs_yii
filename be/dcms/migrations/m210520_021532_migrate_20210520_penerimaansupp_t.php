<?php

use yii\db\Migration;

/**
 * Class m210520_021532_migrate_20210520_penerimaansupp_t
 */
class m210520_021532_migrate_20210520_penerimaansupp_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."penerimaansupp_t" ADD COLUMN if not exists "is_consigment" bool DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210520_021532_migrate_20210520_penerimaansupp_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210520_021532_migrate_20210520_penerimaansupp_t cannot be reverted.\n";

        return false;
    }
    */
}
