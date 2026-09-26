<?php

use yii\db\Migration;

/**
 * Class m230819_105808_add_iterasi_signa_inforesepdetail
 */
class m230819_105808_add_iterasi_signa_inforesepdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inforesepdetail_v");
        $inforesepdetail_v = file_get_contents(__DIR__ . '/definitions/inforesepdetail_v.sql');
        $this->execute($inforesepdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230819_105808_add_iterasi_signa_inforesepdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230819_105808_add_iterasi_signa_inforesepdetail cannot be reverted.\n";

        return false;
    }
    */
}
