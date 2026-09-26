<?php

use yii\db\Migration;

/**
 * Class m190723_110009_monitorbpjs_v
 */
class m190723_110009_monitorbpjs_v extends Migration
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
    monitorbpjsdetail_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    monitorbpjs_m.is_active
   FROM monitorbpjs_m
     JOIN monitorbpjsdetail_m ON monitorbpjs_m.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id
     JOIN groupinacbg_m ON monitorbpjsdetail_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
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
        echo "m190723_110009_monitorbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190723_110009_monitorbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
