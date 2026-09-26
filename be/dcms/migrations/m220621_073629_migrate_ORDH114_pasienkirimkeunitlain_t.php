<?php

use yii\db\Migration;

/**
 * Class m220621_073629_migrate_ORDH114_pasienkirimkeunitlain_t
 */
class m220621_073629_migrate_ORDH114_pasienkirimkeunitlain_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN IF NOT EXISTS "tglpersetujuan" TIMESTAMP;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220621_073629_migrate_ORDH114_pasienkirimkeunitlain_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_073629_migrate_ORDH114_pasienkirimkeunitlain_t cannot be reverted.\n";

        return false;
    }
    */
}
