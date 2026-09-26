<?php

use yii\db\Migration;

/**
 * Class m231119_125755_glbj_295_rpp_918_bpjs_infoantrean_v_infopendaftaranol
 */
class m231119_125755_glbj_295_rpp_918_bpjs_infoantrean_v_infopendaftaranol extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS bpjs_infoantrean_v");
        $bpjs_infoantrean_v = file_get_contents(__DIR__ . '/definitions/bpjs_infoantrean_v.view.sql');
        $this->execute($bpjs_infoantrean_v);

        $this->execute("DROP VIEW IF EXISTS infopendaftaranol_v");
        $infopendaftaranol_v = file_get_contents(__DIR__ . '/definitions/infopendaftaranol_v.view.sql');
        $this->execute($infopendaftaranol_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231119_125755_glbj_295_rpp_918_bpjs_infoantrean_v_infopendaftaranol cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231119_125755_glbj_295_rpp_918_bpjs_infoantrean_v_infopendaftaranol cannot be reverted.\n";

        return false;
    }
    */
}
