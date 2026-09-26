<?php

use yii\db\Migration;

/**
 * Class m240624_105214_migrate_pcp33_update_view_expertise_v
 */
class m240624_105214_migrate_pcp33_update_view_expertise_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS expertise_v");
        $expertise_v = file_get_contents(__DIR__ . '/definitions/expertise_v.sql');
        $this->execute($expertise_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240624_105214_migrate_pcp33_update_view_expertise_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240624_105214_migrate_pcp33_update_view_expertise_v cannot be reverted.\n";

        return false;
    }
    */
}
