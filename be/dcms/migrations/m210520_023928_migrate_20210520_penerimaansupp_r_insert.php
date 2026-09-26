<?php

use yii\db\Migration;

/**
 * Class m210520_023928_migrate_20210520_penerimaansupp_r_insert
 */
class m210520_023928_migrate_20210520_penerimaansupp_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"penerimaansupp_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
IF (NEW.is_verifikasi IS TRUE)
THEN
        -- INSERT table history penerimaansupp_r
        INSERT INTO penerimaansupp_r (      
        penerimaansupp_id,
        no_penerimaan,
        tgl_penerimaan,
        supplier_id,
        no_faktur,
        peg_mengetahui,
        peg_menyetujui,
        ruanganpenerima_id,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        pajak_id,
        payterm_id,
        is_tipe,
        no_suratjalan,
        is_verifikasi,
        tgl_verifikasi,
        status_rekap,
        is_consigment
        )VALUES(
        NEW.penerimaansupp_id ,
        NEW.no_penerimaan ,
        NEW.tgl_penerimaan ,
        NEW.supplier_id ,
        NEW.no_faktur ,
        NEW.peg_mengetahui ,
        NEW.peg_menyetujui ,
        NEW.ruanganpenerima_id ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        NEW.pajak_id ,
        NEW.payterm_id ,
        NEW.is_tipe ,
        NEW.no_suratjalan ,
        NEW.is_verifikasi ,
        NEW.tgl_verifikasi ,
            'ACCRUAL',
        NEW.is_consigment
        );
END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute('ALTER FUNCTION "public"."penerimaansupp_r_insert"() OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210520_023928_migrate_20210520_penerimaansupp_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210520_023928_migrate_20210520_penerimaansupp_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
