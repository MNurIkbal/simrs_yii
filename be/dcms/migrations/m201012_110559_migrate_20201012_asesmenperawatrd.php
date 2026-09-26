<?php

use yii\db\Migration;

/**
 * Class m201012_110559_migrate_20201012_asesmenperawatrd
 */
class m201012_110559_migrate_20201012_asesmenperawatrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asesmenperawatrd_t" ADD COLUMN IF NOT EXISTS "resiko_jatuh" text;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201012_110559_migrate_20201012_asesmenperawatrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_110559_migrate_20201012_asesmenperawatrd cannot be reverted.\n";

        return false;
    }
    */
}
