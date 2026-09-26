<?php

use yii\db\Migration;

/**
 * Class m210330_021541_migrate_20210330_infoformulirstokopname_v
 */
class m210330_021541_migrate_20210330_infoformulirstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoformulirstokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoformulirstokopname_v\" AS  SELECT formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    formulirstokopname_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    formulirstokopname_t.totalharga,
    min(periodestokobat_m.tglperiodestok_awal) AS periode_awal,
    max(periodestokobat_m.tglperiodestok_akhir) AS periode_akhir,
    stokopname_t.is_verifikasi
   FROM formulirstokopname_t
     JOIN ruangan_m ON formulirstokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN formstokopname_t ON formulirstokopname_t.formulirstokopname_id = formstokopname_t.formulirstokopname_id
     LEFT JOIN periodestokobat_m ON formstokopname_t.periodestok_id = periodestokobat_m.periodestokobat_id
     LEFT JOIN stokopname_t ON formulirstokopname_t.stokopname_id = stokopname_t.stokopname_id
  WHERE formulirstokopname_t.is_active = true AND formulirstokopname_t.is_deleted = false AND (stokopname_t.is_verifikasi IS NULL OR stokopname_t.is_verifikasi = false)
  GROUP BY formulirstokopname_t.formulirstokopname_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, formulirstokopname_t.tglformulir, formulirstokopname_t.noformulir, formulirstokopname_t.ruangan_id, formulirstokopname_t.totalharga, periodestokobat_m.tglperiodestok_awal, periodestokobat_m.tglperiodestok_akhir, stokopname_t.is_verifikasi;");

        $this->execute('ALTER TABLE "public"."infoformulirstokopname_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210330_021541_migrate_20210330_infoformulirstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_021541_migrate_20210330_infoformulirstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
