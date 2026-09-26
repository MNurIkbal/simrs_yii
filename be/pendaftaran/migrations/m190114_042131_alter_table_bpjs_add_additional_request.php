<?php

use yii\db\Migration;

/**
 * Class m190114_042131_alter_table_bpjs_add_additional_request
 */
class m190114_042131_alter_table_bpjs_add_additional_request extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE bpjs_t ADD COLUMN additional_request VARCHAR
            ");

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042131_alter_table_bpjs_add_additional_request cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042131_alter_table_bpjs_add_additional_request cannot be reverted.\n";

        return false;
    }
    */
}
