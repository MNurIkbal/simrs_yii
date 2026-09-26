<?php

use yii\db\Migration;

/**
 * Class m210505_182238_migrate_20210506_infostokobat_v
 */
class m210505_182238_migrate_20210506_infostokobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infostokobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokobat_v\" AS  SELECT ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokobatalkes_r.ruangan_id,
    ruangan_m.ruangan_nama,
    stokobatalkes_r.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.minimalstok AS min_stok,
    obatalkes_m.maksimalstok AS max_stok,
    stokobatalkes_r.qty_dipesan,
    stokobatalkes_r.qty_tersedia,
    stokobatalkes_r.qty_sisa AS qty_stok
   FROM stokobatalkes_r
     JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id;");
        
        $this->execute('ALTER TABLE "public"."infostokobat_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210505_182238_migrate_20210506_infostokobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210505_182238_migrate_20210506_infostokobat_v cannot be reverted.\n";

        return false;
    }
    */
}
