<?php

use yii\db\Migration;

/**
 * Class m210107_074808_migrate_live_20210107_func_f_setpasienid
 */
class m210107_074808_migrate_live_20210107_func_f_setpasienid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_setpasienid\"(\"xpasienasal_id\" int4, \"xpasientujuan_id\" int4)
  RETURNS TABLE(\"status\" varchar) AS \$BODY\$
    DECLARE 
    status VARCHAR;
BEGIN
status := 'gagal';

UPDATE 
    pendaftaran_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pendaftaranol_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pasienadmisi_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pasienmasukpenunjang_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    cppt_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    soaprj_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    anamnesa_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    konsulpoli_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    reseptur_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    tindakanpelayanan_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    penjualanresep_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    instruksitindakan_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    instruksitindakanbmhp_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pembayaranpelayanan_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    pembayarantransaksi_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    pasienmasukpenunjang_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    pindahkamar_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    obatalkespasien_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pasiendiagnosa_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    koreksidiagnosa_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pasiendirujukkeluar_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
    UPDATE 
    pasienkirimkeunitlain_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    pasienpulang_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;
    
--  klaiminacbg_t —perlu di cek dulu sepertinya
--  UPDATE 
--  klaiminacbg_t SET
--  pasien_id = xpasientujuan_id
--  WHERE 
--  pasien_id = xpasienasal_id;
-- 
    UPDATE 
    pemeriksaanfisik_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    UPDATE 
    hasilpemeriksaanlab_t SET
    pasien_id = xpasientujuan_id
    WHERE 
    pasien_id = xpasienasal_id;

    
status := 'berhasil';
    RETURN QUERY 
    SELECT status

RETURN;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_074808_migrate_live_20210107_func_f_setpasienid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_074808_migrate_live_20210107_func_f_setpasienid cannot be reverted.\n";

        return false;
    }
    */
}
