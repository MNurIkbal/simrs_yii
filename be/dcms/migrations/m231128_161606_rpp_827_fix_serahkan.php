<?php

use yii\db\Migration;

/**
 * Class m231128_161606_rpp_827_fix_serahkan
 */
class m231128_161606_rpp_827_fix_serahkan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrs_v");
        $infopasienrs_v = file_get_contents(__DIR__ . '/definitions/infopasienrs_v.sql');
        $this->execute($infopasienrs_v);

        $get_sequence_antrian_t = file_get_contents(__DIR__ . '/definitions/get_sequence_antrian_t.fn.sql');
        $this->execute($get_sequence_antrian_t);
        
        $pendaftaranol_hapuskuota = file_get_contents(__DIR__ . '/definitions/pendaftaranol_hapuskuota.fn.sql');
        $this->execute($pendaftaranol_hapuskuota);

        $this->execute("DROP VIEW IF EXISTS antrianjkn_v");
        $antrianjkn_v = file_get_contents(__DIR__ . '/definitions/antrianjkn_v.sql');
        $this->execute($antrianjkn_v);

        $this->execute("DROP VIEW IF EXISTS dokumen_v");
        $dokumen_v = file_get_contents(__DIR__ . '/definitions/dokumen_v.view.sql');
        $this->execute($dokumen_v);

        $this->execute("DROP VIEW IF EXISTS inforiwayatpasien_v");
        $inforiwayatpasien_v = file_get_contents(__DIR__ . '/definitions/inforiwayatpasien_v.view.sql');
        $this->execute($inforiwayatpasien_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231128_161606_rpp_827_fix_serahkan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231128_161606_rpp_827_fix_serahkan cannot be reverted.\n";

        return false;
    }
    */
}
