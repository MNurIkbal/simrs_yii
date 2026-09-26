<?php

use yii\db\Migration;

/**
 * Class m220829_093634_migrate_MHG_1779_is_newso_formstokopname_t_and_stokopnamedetail_t
 */
class m220829_093634_migrate_MHG_1779_is_newso_formstokopname_t_and_stokopnamedetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE formstokopname_t ADD IF NOT EXISTS is_newso bool Default false; ');
        $this->execute('ALTER  TABLE stokopnamedetail_t ADD IF NOT EXISTS is_newso bool Default false; ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220829_093634_migrate_MHG_1779_is_newso_formstokopname_t_and_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220829_093634_migrate_MHG_1779_is_newso_formstokopname_t_and_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }
    */
}
