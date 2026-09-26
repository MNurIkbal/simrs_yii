<?php

use yii\db\Migration;

/**
 * Class m250812_072330_migrate_restriction_function_get_restriction_obat_detail
 */
class m250812_072330_migrate_restriction_function_get_restriction_obat_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS get_restriction_obat_detail");
        $get_restriction_obat_detail = file_get_contents(__DIR__ . '/definitions/get_restriction_obat_detail.sql');
        $this->execute($get_restriction_obat_detail);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250812_072330_migrate_restriction_function_get_restriction_obat_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250812_072330_migrate_restriction_function_get_restriction_obat_detail cannot be reverted.\n";

        return false;
    }
    */
}
