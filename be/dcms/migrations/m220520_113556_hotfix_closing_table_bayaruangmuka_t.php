<?php

use yii\db\Migration;

/**
 * Class m220520_113556_hotfix_closing_table_bayaruangmuka_t
 */
class m220520_113556_hotfix_closing_table_bayaruangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "pembatalanuangmuka_t_insert" ON "public"."bayaruangmuka_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembatalanuangmuka_t_insert" AFTER UPDATE OF "is_deleted" ON "public"."bayaruangmuka_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembatalanuangmuka_t_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220520_113556_hotfix_closing_table_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_113556_hotfix_closing_table_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
