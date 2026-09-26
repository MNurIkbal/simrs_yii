<?php

use yii\db\Migration;

/**
 * Class m250702_082858_rpp_2175_obatalkespasien_r_update
 */
class m250702_082858_rpp_2175_obatalkespasien_r_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $obatalkespasien_r_update = file_get_contents(__DIR__ . '/definitions/obatalkespasien_r_update.sql');
        $this->execute($obatalkespasien_r_update);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250702_082858_rpp_2175_obatalkespasien_r_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250702_082858_rpp_2175_obatalkespasien_r_update cannot be reverted.\n";

        return false;
    }
    */
}
