<?php

use yii\db\Migration;

/**
 * Class m230926_064751_migrate_DSV324_generateNoSuratKeterangan
 */
class m230926_064751_migrate_DSV324_generateNoSuratKeterangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.\"generateNoSuratKeterangan\"(p_surat_keterangan_id integer)
        RETURNS TABLE(nomor_surat character varying)
        LANGUAGE plpgsql
        IMMUTABLE
       AS \$function\$
       
                   BEGIN
                       RETURN QUERY 
               SELECT (profilrumahsakit_m.kodesurat_rs|| '/' || CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(LEFT(no_surat,12)), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))
                       || '/' || surat_keterangan_m.kode_surat || '/'  || profilrumahsakit_m.nama_rs )::VARCHAR as nomor_surat
           
           FROM surat_keterangan_pasien_t
               LEFT JOIN (SELECT profilrs_id,profilrumahsakit_m.additional_data::json ->> 'kodesurat_rs' as kodesurat_rs,  profilrumahsakit_m.additional_data::json ->> 'nama_rs' as nama_rs
                       from profilrumahsakit_m )	profilrumahsakit_m ON profilrumahsakit_m.profilrs_id = 1
                       LEFT JOIN surat_keterangan_m on surat_keterangan_m.surat_keterangan_id = p_surat_keterangan_id
                       WHERE TO_CHAR(surat_keterangan_pasien_t.created_date, 'YYYY') = TO_CHAR(CURRENT_DATE, 'YYYY') and surat_keterangan_pasien_t.surat_keterangan_id = p_surat_keterangan_id
                   GROUP BY profilrumahsakit_m.kodesurat_rs,profilrumahsakit_m.nama_rs,surat_keterangan_m.kode_surat
       ;
           
                   END
                   \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_064751_migrate_DSV324_generateNoSuratKeterangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_064751_migrate_DSV324_generateNoSuratKeterangan cannot be reverted.\n";

        return false;
    }
    */
}
