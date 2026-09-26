<?php

use yii\db\Migration;

/**
 * Class m190404_092013_infoformulirstokopname_v
 */
class m190404_092013_infoformulirstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infoformulirstokopname_v;
        ');

        $this->execute('
           CREATE OR REPLACE VIEW infoformulirstokopname_v AS 
 SELECT formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    formulirstokopname_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    formulirstokopname_t.totalharga,
    min(periodestokobat_m.tglperiodestok_awal) AS periode_awal,
    max(periodestokobat_m.tglperiodestok_akhir) AS periode_akhir
   FROM formulirstokopname_t
     JOIN ruangan_m ON formulirstokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN formstokopname_t ON formulirstokopname_t.formulirstokopname_id = formstokopname_t.formulirstokopname_id
     LEFT JOIN periodestokobat_m ON formstokopname_t.periodestok_id = periodestokobat_m.periodestokobat_id
  WHERE formulirstokopname_t.is_active = true AND formulirstokopname_t.is_deleted = false AND formulirstokopname_t.stokopname_id IS NULL
  GROUP BY formulirstokopname_t.formulirstokopname_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, formulirstokopname_t.tglformulir, formulirstokopname_t.noformulir, formulirstokopname_t.ruangan_id, formulirstokopname_t.totalharga, periodestokobat_m.tglperiodestok_awal, periodestokobat_m.tglperiodestok_akhir;

                    ');
        
        $this->execute('
           ALTER TABLE infoformulirstokopname_v
  OWNER TO postgres;

        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190404_092013_infoformulirstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190404_092013_infoformulirstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
