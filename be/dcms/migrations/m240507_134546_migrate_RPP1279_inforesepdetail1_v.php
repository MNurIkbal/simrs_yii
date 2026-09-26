<?php

use yii\db\Migration;

/**
 * Class m240507_134546_migrate_RPP1279_inforesepdetail1_v
 */
class m240507_134546_migrate_RPP1279_inforesepdetail1_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inforesepdetail1_v");
        $inforesepdetail1_v = file_get_contents(__DIR__ . '/definitions/inforesepdetail1_v.sql');
        $this->execute($inforesepdetail1_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240507_134546_migrate_RPP1279_inforesepdetail1_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_134546_migrate_RPP1279_inforesepdetail1_v cannot be reverted.\n";

        return false;
    }
    */
}
