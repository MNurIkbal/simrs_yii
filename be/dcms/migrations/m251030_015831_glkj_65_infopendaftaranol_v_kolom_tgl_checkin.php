<?php

use yii\db\Migration;

/**
 * Class m251030_015831_glkj_65_infopendaftaranol_v_kolom_tgl_checkin
 */
class m251030_015831_glkj_65_infopendaftaranol_v_kolom_tgl_checkin extends Migration
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
        echo "m251030_015831_glkj_65_infopendaftaranol_v_kolom_tgl_checkin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251030_015831_glkj_65_infopendaftaranol_v_kolom_tgl_checkin cannot be reverted.\n";

        return false;
    }
    */
}
