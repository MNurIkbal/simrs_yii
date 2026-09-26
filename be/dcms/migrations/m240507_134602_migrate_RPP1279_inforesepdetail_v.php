<?php

use yii\db\Migration;

/**
 * Class m240507_134602_migrate_RPP1279_inforesepdetail_v
 */
class m240507_134602_migrate_RPP1279_inforesepdetail_v extends Migration
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
        echo "m240507_134602_migrate_RPP1279_inforesepdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_134602_migrate_RPP1279_inforesepdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
