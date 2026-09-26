<?php

use yii\db\Migration;

/**
 * Class m231028_051540_add_lookup_url_finger
 */
class m231028_051540_add_lookup_url_finger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m where lookup_id = 2125");

        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value)
        VALUES(2125, 'bpjs', 'url_finger_bpjs', 'fingerbpjs://parameter=value');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_051540_add_lookup_url_finger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_051540_add_lookup_url_finger cannot be reverted.\n";

        return false;
    }
    */
}
