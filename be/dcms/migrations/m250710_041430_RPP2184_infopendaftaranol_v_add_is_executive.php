<?php

use yii\db\Migration;

/**
 * Class m250710_041430_RPP2184_infopendaftaranol_v_add_is_executive
 */
class m250710_041430_RPP2184_infopendaftaranol_v_add_is_executive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopendaftaranol_v");
        $infopendaftaranol_v = file_get_contents(__DIR__ . '/definitions/infopendaftaranol_v.view.sql');
        $this->execute($infopendaftaranol_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250710_041430_RPP2184_infopendaftaranol_v_add_is_executive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250710_041430_RPP2184_infopendaftaranol_v_add_is_executive cannot be reverted.\n";

        return false;
    }
    */
}
