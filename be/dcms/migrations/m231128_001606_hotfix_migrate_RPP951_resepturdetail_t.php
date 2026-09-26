<?php

use yii\db\Migration;

/**
 * Class m231204_101106_hotfix_migrate_RPP951_resepturdetail_t
 */
class m231128_001606_hotfix_migrate_RPP951_resepturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.resepturdetail_t ADD IF NOT EXISTS is_retur bool NULL DEFAULT false;");
        $this->execute("ALTER TABLE public.obatalkespasien_t ADD IF NOT EXISTS is_retur bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231204_101106_hotfix_migrate_RPP951_resepturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231204_101106_hotfix_migrate_RPP951_resepturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
