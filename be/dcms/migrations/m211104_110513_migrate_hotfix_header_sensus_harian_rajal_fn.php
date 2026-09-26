<?php

use yii\db\Migration;

/**
 * Class m211104_110513_migrate_hotfix_header_sensus_harian_rajal_fn
 */
class m211104_110513_migrate_hotfix_header_sensus_harian_rajal_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.header_sensus_harian_rajal_fn;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"header_sensus_harian_rajal_fn\"()
  RETURNS TABLE(\"instalasi_id\" int4, \"ruangan_id\" int4, \"ruangan_nama\" varchar, \"jenis_ruangan\" int4, \"carabayar_id\" int4, \"carabayar_nama\" varchar) AS \$BODY\$ BEGIN
        FOR instalasi_id,
        ruangan_id,
        ruangan_nama,
        jenis_ruangan IN SELECT
        ruangan_m.instalasi_id,
        ruangan_m.ruangan_id,
        ruangan_m.ruangan_nama,
        ruangan_m.jenis_ruangan 
    FROM
        ruangan_m 
    WHERE
        ruangan_m.jenis_ruangan IS NOT NULL
        LOOP
        FOR carabayar_id,
        carabayar_nama IN SELECT
        * 
    FROM
        carabayar_m 
    WHERE
        is_active = TRUE 
        AND is_deleted = FALSE
        LOOP
        RETURN NEXT;
    
END LOOP;

END LOOP;
RETURN;

END \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211104_110513_migrate_hotfix_header_sensus_harian_rajal_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211104_110513_migrate_hotfix_header_sensus_harian_rajal_fn cannot be reverted.\n";

        return false;
    }
    */
}
