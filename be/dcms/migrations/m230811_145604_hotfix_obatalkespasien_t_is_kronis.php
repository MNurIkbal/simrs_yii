<?php

use yii\db\Migration;

/**
 * Class m230811_145604_hotfix_obatalkespasien_t_is_kronis
 */
class m230811_145604_hotfix_obatalkespasien_t_is_kronis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE obatalkespasien_t ADD COLUMN IF NOT EXISTS is_kronis bool NULL DEFAULT FALSE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230811_145604_hotfix_obatalkespasien_t_is_kronis cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230811_145604_hotfix_obatalkespasien_t_is_kronis cannot be reverted.\n";

        return false;
    }
    */
}
