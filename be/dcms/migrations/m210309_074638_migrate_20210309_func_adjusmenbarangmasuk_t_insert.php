<?php

use yii\db\Migration;

/**
 * Class m210309_074638_migrate_20210309_func_adjusmenbarangmasuk_t_insert
 */
class m210309_074638_migrate_20210309_func_adjusmenbarangmasuk_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"adjusmenobatmasuk_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history adjusmenobatmasuk_r<----------------------------------
        INSERT INTO adjusmenobatmasuk_r (       
                adjusmenobatmasuk_id,
                adjusmenobat_id,
                obatalkes_id,
                tgl_kadaluarsa,
                qty,
                satuankecil_id,
                harga_netto,
                satuanbesar_id,
                qty_konversi,
                satuankonversi_id,
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
                no_batch,
                keterangan,
                keterangan_rekap
        )VALUES(
                NEW.adjusmenobatmasuk_id,
                NEW.adjusmenobat_id,
                NEW.obatalkes_id,
                NEW.tgl_kadaluarsa,
                NEW.qty,
                NEW.satuankecil_id,
                NEW.harga_netto,
                NEW.satuanbesar_id,
                NEW.qty_konversi,
                NEW.satuankonversi_id,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.no_batch,
                NEW.keterangan,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_074638_migrate_20210309_func_adjusmenbarangmasuk_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_074638_migrate_20210309_func_adjusmenbarangmasuk_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
