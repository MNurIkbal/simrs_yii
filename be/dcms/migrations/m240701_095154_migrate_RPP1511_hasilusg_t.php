<?php

use yii\db\Migration;

/**
 * Class m240701_095154_migrate_RPP1511_hasilusg_t
 */
class m240701_095154_migrate_RPP1511_hasilusg_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.hasilusg_t ADD IF NOT EXISTS ruangan_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240701_095154_migrate_RPP1511_hasilusg_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240701_095154_migrate_RPP1511_hasilusg_t cannot be reverted.\n";

        return false;
    }
    */
}
