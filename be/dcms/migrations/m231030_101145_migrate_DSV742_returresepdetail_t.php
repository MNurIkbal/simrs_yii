<?php

use yii\db\Migration;

/**
 * Class m231030_101145_migrate_DSV742_returresepdetail_t
 */
class m231030_101145_migrate_DSV742_returresepdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("ALTER TABLE public.returresepdetail_t ADD IF NOT EXISTS qty_pemberian_akhir int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_101145_migrate_DSV742_returresepdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_101145_migrate_DSV742_returresepdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
