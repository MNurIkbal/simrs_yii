<?php

use yii\db\Migration;

/**
 * Class m231028_064248_ins_antrian_konfig_antrian_fase2
 */
class m231028_064248_ins_antrian_konfig_antrian_fase2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $ins_noantrian_konfig = file_get_contents(__DIR__ . '/definitions/ins_noantrian_konfig.fn.sql');
        $this->execute($ins_noantrian_konfig);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_064248_ins_antrian_konfig_antrian_fase2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_064248_ins_antrian_konfig_antrian_fase2 cannot be reverted.\n";

        return false;
    }
    */
}
