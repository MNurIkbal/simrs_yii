<?php

use yii\db\Migration;

/**
 * Class m250828_115229_add_seeder_cfi
 */
class m250828_115229_add_seeder_cfi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("UPDATE tabularlist_m SET is_deleted = TRUE WHERE tabularlist_chapter = 'ICF'");

        $this->execute("INSERT INTO tabularlist_m 
        (tabularlist_chapter,tabularlist_block,tabularlist_title,tabularlist_revisi,tabularlist_versi) VALUES 
        ('ICF','B001 - B999','B001 - B999','ICF','ICF');
        ");

        $this->execute("UPDATE dtd_m SET is_deleted = TRUE WHERE dtd_noterperinci = 'DTD - ICF'");

        $this->execute("INSERT INTO dtd_m (tabularlist_id,dtd_kode,dtd_noterperinci,dtd_nama,dtd_namalainnya,dtd_nourut) 
        SELECT tabularlist_id, 'B001-B999'::VARCHAR as dtd_kode, 'B001-B999'::VARCHAR as dtd_noterperinci,'DTD - ICF'::VARCHAR AS dtd_nama,'DTD - ICF'::VARCHAR AS dtd_namalainnya, 3 AS dtd_nourut 
        FROM tabularlist_m
        WHERE tabularlist_chapter='ICF';
        ");

        $this->execute("UPDATE klasifikasidiagnosa_m SET is_deleted = TRUE WHERE klasifikasidiagnosa_namalain = 'Klasifikasi ICF'");

        $this->execute("INSERT INTO klasifikasidiagnosa_m  
        (klasifikasidiagnosa_kode,klasifikasidiagnosa_nama,klasifikasidiagnosa_namalain,klasifikasidiagnosa_desc,dtd_id) 
        SELECT 
        'B'::VARCHAR AS klasifikasidiagnosa_kode,
        'B'::VARCHAR AS klasifikasidiagnosa_nama,
        'Klasifikasi ICF'::VARCHAR AS klasifikasidiagnosa_namalain,
        'Klasifikasi ICF'::VARCHAR AS klasifikasidiagnosa_desc,
        dtd_id 
        FROM dtd_m
        WHERE dtd_nama='DTD - ICF';
        ");

        $this->execute("UPDATE diagnosa_m SET is_deleted = TRUE WHERE klasifikasidiagnosa_id IN (SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B')");

        $this->execute("INSERT INTO diagnosa_m 
        (klasifikasidiagnosa_id,diagnosa_kode,diagnosa_nama,diagnosa_namalainnya,diagnosa_katakunci,validcode,ina_grouper) 
        SELECT * FROM ( VALUES
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B126','Temperament And Personality Functions','Temperament And Personality Functions','B126',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B117','Intellectual Functions','Intellectual Functions','B117',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B110','Consciousness Functions','Consciousness Functions','B110',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B134','Sleep Function','Sleep Function','B134',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B139','Global Mental Functions, Other Specified And Unspecified','Global Mental Functions, Other Specified And Unspecified','B139',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B130','Energy And Drive Functions','Energy And Drive Functions','B130',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B122','Global Psychosocial Functions','Global Psychosocial Functions','B122',1,'INA'),
         ((SELECT klasifikasidiagnosa_id FROM klasifikasidiagnosa_m WHERE klasifikasidiagnosa_kode='B'),'B114','Orientation Functions','Orientation Functions','B114',1,'INA')
        ) AS v(klasifikasidiagnosa_id,diagnosa_kode, diagnosa_nama, diagnosa_namalainnya, diagnosa_katakunci, validcode, ina_grouper);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250828_115229_add_seeder_cfi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250828_115229_add_seeder_cfi cannot be reverted.\n";

        return false;
    }
    */
}
