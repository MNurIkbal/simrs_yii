<?php

use yii\db\Migration;

/**
 * Class m230919_091221_migrate_dsv467_sy_klaimcbg
 */
class m230919_091221_migrate_dsv467_sy_klaiminacbg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE sy_klaiminacbg ADD COLUMN IF NOT EXISTS dializer int4 NULL");
        $this->execute("ALTER TABLE sy_klaiminacbg ADD COLUMN IF NOT EXISTS transfusi_darah int4 NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_091221_migrate_dsv467_sy_klaimcbg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_091221_migrate_dsv467_sy_klaimcbg cannot be reverted.\n";

        return false;
    }
    */
}
