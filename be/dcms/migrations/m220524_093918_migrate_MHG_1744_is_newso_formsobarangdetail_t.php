<?php

use yii\db\Migration;

/**
 * Class m220524_093918_migrate_MHG_1744_is_newso_formsobarangdetail_t
 */
class m220524_093918_migrate_MHG_1744_is_newso_formsobarangdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."formsobarangdetail_t" ADD IF NOT EXISTS "is_newso" bool;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220524_093918_migrate_MHG_1744_is_newso_formsobarangdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220524_093918_migrate_MHG_1744_is_newso_formsobarangdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
