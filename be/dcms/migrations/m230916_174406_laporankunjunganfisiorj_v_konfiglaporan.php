<?php

use yii\db\Migration;

/**
 * Class m230916_174406_laporankunjunganfisiorj_v_konfiglaporan
 */
class m230916_174406_laporankunjunganfisiorj_v_konfiglaporan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganfisiorj_v");
        $laporankunjunganfisiorj_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganfisiorj_v.view.sql');
        $this->execute($laporankunjunganfisiorj_v);

        $this->execute("
        INSERT INTO konfiglaporan_k (key_laporan,jenis_laporan,source_view,\"filter\") VALUES
	    ('laporan_fisio','Laporan Kunjungan Fisioterapi Rajal','laporankunjunganfisiorj_v','\"Tanggal Pendaftaran\"');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230916_174406_laporankunjunganfisiorj_v_konfiglaporan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230916_174406_laporankunjunganfisiorj_v_konfiglaporan cannot be reverted.\n";

        return false;
    }
    */
}
