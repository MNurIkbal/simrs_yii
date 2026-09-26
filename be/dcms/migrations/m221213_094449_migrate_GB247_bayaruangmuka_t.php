<?php

use yii\db\Migration;

/**
 * Class m221213_094449_migrate_GB247_bayaruangmuka_t
 */
class m221213_094449_migrate_GB247_bayaruangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.bayaruangmuka_t ADD IF NOT EXISTS is_tunai bool NULL DEFAULT true;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_094449_migrate_GB247_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221213_094449_migrate_GB247_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
