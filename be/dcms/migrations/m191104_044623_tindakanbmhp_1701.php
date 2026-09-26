<?php

use yii\db\Migration;

/**
 * Class m191104_044623_tindakanbmhp_1701
 */
class m191104_044623_tindakanbmhp_1701 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('CREATE TABLE "public"."tindakanbmhp_mp" (
                      "daftartindakan_id" int4,
                      "tipepaket_id" int4,
                      "obatalkes_id" int4,
                      "satuaninput_id" int4,
                      "satuanunit_id" int4,
                      "nilai_konversi" float4,
                      "qty_input" float4,
                      "qty_konversi" float4,
                      "additional_data" text COLLATE "pg_catalog"."default",
                      "created_date" timestamp(6) NOT NULL DEFAULT now(),
                      "created_by" int4,
                      "modified_count" int4,
                      "last_modified_date" timestamp(6),
                      "last_modified_by" int4,
                      "is_deleted" bool NOT NULL DEFAULT false,
                      "is_active" bool NOT NULL DEFAULT true,
                      "deleted_date" timestamp(6),
                      "deleted_by" int4);
                    ');

        $this->execute('ALTER TABLE public.tindakanbmhp_mp
  OWNER TO postgres;');

        
        $this->execute('DROP VIEW if exists public.tindakanbmhp_v;');

        $this->execute("
          CREATE OR REPLACE VIEW public.tindakanbmhp_v AS 
 SELECT tindakanbmhp_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanbmhp_mp.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    tindakanbmhp_mp.satuaninput_id,
    satuan_input.satuanunit_nama AS satuaninput_nama,
    tindakanbmhp_mp.satuanunit_id,
    satuanunit_m.satuanunit_nama,
    tindakanbmhp_mp.nilai_konversi,
    tindakanbmhp_mp.qty_input,
    tindakanbmhp_mp.qty_konversi
   FROM tindakanbmhp_mp
     JOIN daftartindakan_m ON tindakanbmhp_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN obatalkes_m ON tindakanbmhp_mp.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satuanunit_m ON tindakanbmhp_mp.satuanunit_id = satuanunit_m.satuanunit_id
     JOIN satuanunit_m satuan_input ON tindakanbmhp_mp.satuaninput_id = satuan_input.satuanunit_id
  WHERE tindakanbmhp_mp.is_deleted = false;
");

        $this->execute('ALTER TABLE public.tindakanbmhp_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infotindakanbmhp_v;');

        $this->execute("
          CREATE OR REPLACE VIEW public.infotindakanbmhp_v AS 
 SELECT tindakanbmhp_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    ( SELECT array_to_json(array_agg(row_to_json(detail.*))) AS array_to_json
           FROM ( SELECT x.daftartindakan_id,
                    x.obatalkes_id,
                    obatalkes_m.obatalkes_nama,
                    x.satuaninput_id,
                    satuan_input.satuanunit_nama AS satuaninput_nama,
                    x.satuanunit_id,
                    satuanunit_m.satuanunit_nama,
                    x.nilai_konversi,
                    x.qty_input,
                    x.qty_konversi
                   FROM tindakanbmhp_mp x
                     JOIN obatalkes_m ON x.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN satuanunit_m ON x.satuanunit_id = satuanunit_m.satuanunit_id
                     JOIN satuanunit_m satuan_input ON x.satuaninput_id = satuan_input.satuanunit_id
                  WHERE tindakanbmhp_mp.daftartindakan_id = x.daftartindakan_id AND x.is_deleted = false) detail) AS detail_bmhp
   FROM tindakanbmhp_mp
     JOIN daftartindakan_m ON tindakanbmhp_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
  WHERE tindakanbmhp_mp.is_deleted = false
  GROUP BY tindakanbmhp_mp.daftartindakan_id, daftartindakan_m.daftartindakan_nama;");

        $this->execute('ALTER TABLE public.infotindakanbmhp_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191104_044623_tindakanbmhp_1701 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191104_044623_tindakanbmhp_1701 cannot be reverted.\n";

        return false;
    }
    */
}
