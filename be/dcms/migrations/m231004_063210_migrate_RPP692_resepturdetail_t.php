<?php

use yii\db\Migration;

/**
 * Class m231004_063210_migrate_RPP692_resepturdetail_t
 */
class m231004_063210_migrate_RPP692_resepturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("ALTER TABLE public.resepturdetail_t ADD IF NOT EXISTS hari int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231004_063210_migrate_RPP692_resepturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231004_063210_migrate_RPP692_resepturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
