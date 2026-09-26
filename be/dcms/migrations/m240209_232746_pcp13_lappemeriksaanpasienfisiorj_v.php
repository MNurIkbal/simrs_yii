<?php

use yii\db\Migration;

/**
 * Class m240209_232746_pcp13_lappemeriksaanpasienfisiorj_v
 */
class m240209_232746_pcp13_lappemeriksaanpasienfisiorj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS lappemeriksaanpasienfisiorj_v");
        $sql = file_get_contents(__DIR__ . '/definitions/lappemeriksaanpasienfisiorj_v.sql');
        $this->execute($sql);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_232746_pcp13_lappemeriksaanpasienfisiorj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_232746_pcp13_lappemeriksaanpasienfisiorj_v cannot be reverted.\n";

        return false;
    }
    */
}
