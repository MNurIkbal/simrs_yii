<?php

use yii\db\Migration;

/**
 * Class m220205_092741_hotfix_konfigsystem_k_column_set_tgl_sensus
 */
class m220205_092741_hotfix_konfigsystem_k_column_set_tgl_sensus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(' ALTER TABLE "public"."konfigsystem_k" RENAME COLUMN "set_tgl_sensusi" TO "set_tgl_sensus";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220205_092741_hotfix_konfigsystem_k_column_set_tgl_sensus cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220205_092741_hotfix_konfigsystem_k_column_set_tgl_sensus cannot be reverted.\n";

        return false;
    }
    */
}
