<?php

use yii\db\Migration;

/**
 * Class m191204_234944_konfigsystem_1716
 */
class m191204_234944_konfigsystem_1716 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
                          ADD COLUMN "adm_persen" float4,
                          ADD COLUMN "adm_tindakan_id" int4;');

        $this->execute('DROP VIEW if exists public.tindakanadm_v;');

        $this->execute("
    CREATE OR REPLACE VIEW public.tindakanadm_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama AS penjamin,
    tariftindakan_m.harga_tariftindakan AS tarif,
    konfigsystem_k.adm_persen
   FROM tariftindakan_m
     JOIN konfigsystem_k ON tariftindakan_m.daftartindakan_id = konfigsystem_k.adm_tindakan_id
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
  WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.komponentarif_id = 6;");

        $this->execute('ALTER TABLE public.tindakanadm_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191204_234944_konfigsystem_1716 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191204_234944_konfigsystem_1716 cannot be reverted.\n";

        return false;
    }
    */
}
