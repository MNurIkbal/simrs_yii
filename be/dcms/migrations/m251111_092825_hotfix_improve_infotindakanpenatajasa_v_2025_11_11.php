<?php

use yii\db\Migration;

/**
 * Class m251111_092825_hotfix_improve_infotindakanpenatajasa_v_2025_11_11
 */
class m251111_092825_hotfix_improve_infotindakanpenatajasa_v_2025_11_11 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("DROP VIEW IF EXISTS infotindakanpenatajasa_v");
        $infotindakanpenatajasa_v_2025_11_11 = file_get_contents(__DIR__ . '/definitions/infotindakanpenatajasa_v_2025_11_11.sql');
        $this->execute($infotindakanpenatajasa_v_2025_11_11);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251111_092825_hotfix_improve_infotindakanpenatajasa_v_2025_11_11 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251111_092825_hotfix_improve_infotindakanpenatajasa_v_2025_11_11 cannot be reverted.\n";

        return false;
    }
    */
}
