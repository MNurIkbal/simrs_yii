<?php

use yii\db\Migration;

/**
 * Class m210127_122539_migrate_20210127_daftartindakan_m
 */
class m210127_122539_migrate_20210127_daftartindakan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('COMMENT ON COLUMN "public"."daftartindakan_m"."servicecategory_id" IS \'kebutuhan ODDO\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_122539_migrate_20210127_daftartindakan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_122539_migrate_20210127_daftartindakan_m cannot be reverted.\n";

        return false;
    }
    */
}
