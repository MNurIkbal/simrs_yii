<?php

use yii\db\Migration;

/**
 * Class m250107_090003_migrate_dsv_1617_alter_asuransi_t
 */
class m250107_090003_migrate_dsv_1617_alter_asuransi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.asuransi_t ADD IF NOT EXISTS is_cob bool DEFAULT false NULL;");
        $this->execute("ALTER TABLE public.asuransi_t ADD IF NOT EXISTS benefit_id varchar NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250107_090003_migrate_dsv_1617_alter_asuransi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250107_090003_migrate_dsv_1617_alter_asuransi_t cannot be reverted.\n";

        return false;
    }
    */
}
