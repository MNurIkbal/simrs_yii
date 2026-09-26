<?php

use yii\db\Migration;

/**
 * Class m221019_041056_hotfix_penerimaanbarangdetail_t
 */
class m221019_041056_hotfix_penerimaanbarangdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.penerimaanbarangdetail_t ADD IF NOT EXISTS is_batal bool NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221019_041056_hotfix_penerimaanbarangdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221019_041056_hotfix_penerimaanbarangdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
