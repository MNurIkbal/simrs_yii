<?php

use yii\db\Migration;

/**
 * Class m240815_085554_hotfix_laporankunjunganri_v
 */
class m240815_085554_hotfix_laporankunjunganri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganri_v");
        $laporankunjunganri_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganri_v.sql');
        $this->execute($laporankunjunganri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240815_085554_hotfix_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240815_085554_hotfix_laporankunjunganri_v cannot be reverted.\n";

        return false;
    }
    */
}
