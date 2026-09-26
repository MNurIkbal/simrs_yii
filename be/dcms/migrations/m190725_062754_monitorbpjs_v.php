<?php

use yii\db\Migration;

/**
 * Class m190725_062754_monitorbpjs_v
 */
class m190725_062754_monitorbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('
          DROP VIEW if exists public.monitorbpjs_v;
        ');

                $this->execute('
         CREATE OR REPLACE VIEW public.monitorbpjs_v AS 
 SELECT monitorbpjs_m.monitorbpjs_id,
    monitorbpjs_m.kelompoktindakan_nama,
    monitorbpjs_m.is_active,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT monitorbpjsdetail_m.groupinacbg_id,
                    groupinacbg_m.groupinacbg_nama
                   FROM monitorbpjsdetail_m
                     JOIN groupinacbg_m ON monitorbpjsdetail_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                  WHERE monitorbpjs_m.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_deleted = false) d1) AS monitorbpjsdetail
   FROM monitorbpjs_m
  WHERE monitorbpjs_m.is_deleted = false;
        ');

                    $this->execute('
          ALTER TABLE public.monitorbpjs_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_062754_monitorbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_062754_monitorbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
