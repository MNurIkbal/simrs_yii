<?php

use yii\db\Migration;

/**
 * Class m220810_081730_migrate_mhg__1779_is_newso_formstokopname_t_and_stokopnamedetail_t
 */
class m220810_081730_migrate_mhg__1779_is_newso_formstokopname_t_and_stokopnamedetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE formstokopname_t ADD IF NOT EXISTS is_newso bool DEFAULT false; ');
		$this->execute('ALTER  TABLE stokopnamedetail_t ADD IF NOT EXISTS is_newso bool DEFAULT false; ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_081730_migrate_mhg__1779_is_newso_formstokopname_t_and_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_081730_migrate_mhg__1779_is_newso_formstokopname_t_and_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }
    */
}
