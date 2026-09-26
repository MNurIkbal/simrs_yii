<?php

use yii\db\Migration;

/**
 * Class m231114_034234_migrate_DSV_919_lookup_m
 */
class m231114_034234_migrate_DSV_919_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m where lookup_id = 2158");

        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value)
        VALUES(2158, 'bpjs', 'base_url_jkn', 'https://apijkn-dev.bpjs-kesehatan.go.id/');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231114_034234_migrate_DSV_919_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231114_034234_migrate_DSV_919_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
