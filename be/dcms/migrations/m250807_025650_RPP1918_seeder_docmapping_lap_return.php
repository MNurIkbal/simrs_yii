<?php

use yii\db\Migration;

/**
 * Class m250807_025650_RPP1918_seeder_docmapping_lap_return
 */
class m250807_025650_RPP1918_seeder_docmapping_lap_return extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM docmapping_k WHERE kode_doc='lap-return';");
        
        $this->execute("INSERT INTO docmapping_k (docheader_id,docbody_text,docfooter_id,controller,doc_key,nama_doc,kode_doc,kertas_id,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_active,deleted_date,deleted_by,is_deleted,additional_style) VALUES
	        (53,'<p>#table_report#</p>',60,'','apotek-LapReturController-actionExportPdf','Laporan Retur','lap-return',3,'','2020-06-13 17:50:47.000',NULL,NULL,NULL,NULL,true,NULL,NULL,false,NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250807_025650_RPP1918_seeder_docmapping_lap_return cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250807_025650_RPP1918_seeder_docmapping_lap_return cannot be reverted.\n";

        return false;
    }
    */
}
