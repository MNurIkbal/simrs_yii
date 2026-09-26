<?php

use yii\db\Migration;

/**
 * Class m210309_110653_migrate_20210309_3140_f_getpasienrdrujukri
 */
class m210309_110653_migrate_20210309_3140_f_getpasienrdrujukri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienrdrujukri\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xcarakeluar\" int4, \"xinstalasi\" int4)
  RETURNS TABLE(\"pasienrdri\" int4) AS \$BODY\$
    
DECLARE
--  pasienmasukigd int4;
    
BEGIN

IF (xcarakeluar = 5 AND xinstalasi = 2) --Dirujuk RI dan instalasi RD
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienrdri
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
    JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
    WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pasienpulang_t.carakeluar_id = 5
    AND pendaftaran_t.instalasi_id = 2 --RD
    AND pendaftaran_t.is_active = TRUE 
  AND pendaftaran_t.is_deleted = FALSE
    GROUP BY pasien_m.pasien_id;
END IF;
    
-- RETURN DATA
RETURN NEXT;

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
        echo "m210309_110653_migrate_20210309_3140_f_getpasienrdrujukri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_110653_migrate_20210309_3140_f_getpasienrdrujukri cannot be reverted.\n";

        return false;
    }
    */
}
