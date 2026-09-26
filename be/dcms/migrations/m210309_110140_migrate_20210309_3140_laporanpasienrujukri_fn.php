<?php

use yii\db\Migration;

/**
 * Class m210309_110140_migrate_20210309_3140_laporanpasienrujukri_fn
 */
class m210309_110140_migrate_20210309_3140_laporanpasienrujukri_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"laporanpasienrujukkeri_fn\"(\"xfirstdate\" date, \"xlastdate\" date)
  RETURNS TABLE(\"tgl_pendaftaran\" date, \"tglpasienpulang\" date, \"pasien_id\" int4, \"carakeluar\" int4, \"carakeluar_nama\" varchar, \"instalasi\" int4, \"instalasi_nama\" varchar, \"pasienrjkeri\" int4, \"pasienrdkeri\" int4, \"jumlah\" int4) AS \$BODY\$

DECLARE
--jenis_ruangan int4;
BEGIN

            FOR tgl_pendaftaran, tglpasienpulang, pasien_id, carakeluar, carakeluar_nama, instalasi, instalasi_nama IN
                    SELECT
            (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            (to_char(pasienpulang_t.tglpasienpulang, 'YYYY-MM-DD'::text))::date AS tglpasienpulang,
            pasien_m.pasien_id,
            pasienpulang_t.carakeluar_id AS carakeluar,
            carakeluar_m.carakeluar_nama,
                        pendaftaran_t.instalasi_id AS instalasi,
                        instalasi_m.instalasi_nama
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                        JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
            JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                        JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
            WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xfirstdate::DATE AND xlastdate::DATE
            AND pendaftaran_t.is_active = TRUE 
            AND pendaftaran_t.is_deleted = FALSE
                        AND pasienpulang_t.carakeluar_id = 5
                        GROUP BY 
                        (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date,
            (to_char(pasienpulang_t.tglpasienpulang, 'YYYY-MM-DD'::text))::date,
            pasien_m.pasien_id,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
                        pendaftaran_t.instalasi_id,
                        instalasi_m.instalasi_nama
                LOOP
                    SELECT * from f_getpasienrjrujukri(xfirstdate::DATE, xlastdate::DATE, carakeluar, instalasi) INTO pasienrjkeri;
                    
                    SELECT * from f_getpasienrdrujukri(xfirstdate::DATE, xlastdate::DATE, carakeluar, instalasi) INTO pasienrdkeri;
                    
                    SELECT pasienrjkeri + pasienrdkeri INTO jumlah;
                    
                     RETURN NEXT;
            END LOOP;
END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_110140_migrate_20210309_3140_laporanpasienrujukri_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_110140_migrate_20210309_3140_laporanpasienrujukri_fn cannot be reverted.\n";

        return false;
    }
    */
}
