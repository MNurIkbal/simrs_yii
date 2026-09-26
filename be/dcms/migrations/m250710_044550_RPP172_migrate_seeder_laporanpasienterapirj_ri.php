<?php

use yii\db\Migration;

/**
 * Class m250710_044550_RPP172_migrate_seeder_laporanpasienterapirj_ri
 */
class m250710_044550_RPP172_migrate_seeder_laporanpasienterapirj_ri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE FROM docmapping_k
            WHERE kode_doc = 'fisio-lap-pasien-rajal' OR kode_doc = 'fisio-lap-pasien-ranap';
        ");

        // insert cetakan laporan pasien fisioterapi rj dan ri
        $this->execute("
            INSERT INTO docmapping_k (docheader_id,docbody_text,docfooter_id,controller,doc_key,nama_doc,kode_doc,kertas_id,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_active,deleted_date,deleted_by,is_deleted,additional_style) VALUES
                (8,'<h2 style=\"text-align:center\"><strong>Laporan Pasien Fisioterapi Rawat Jalan</strong></h2>

            <p>#datatable#</p>
            ',11,'','fisioterapi-LaporanPasienFisioterapiRajalController-actionCetakPdfBgprocess','Fisioterapi Lap. Pasien Rajal','fisio-lap-pasien-rajal',25,'','".date('Y-m-d H:i:s')."',1,0,NULL,NULL,true,NULL,NULL,false,NULL),
                (8,'<h2 style=\"text-align:center\"><strong>Laporan Pasien Fisioterapi Ranap&nbsp;</strong></h2>

            <p style=\"text-align:center\"><strong>Periode #periode#</strong></p>

            <p>#datatable#</p>
            ',11,'','fisioterapi-LaporanPasienFisioterapiRanapController-actionCetakPdfBgprocess','Fisioterapi Lap. Pasien Ranap','fisio-lap-pasien-ranap',25,'','".date('Y-m-d H:i:s')."',1,0,NULL,NULL,true,NULL,NULL,false,NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250710_044550_RPP172_migrate_seeder_laporanpasienterapirj_ri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250710_044550_RPP172_migrate_seeder_laporanpasienterapirj_ri cannot be reverted.\n";

        return false;
    }
    */
}
