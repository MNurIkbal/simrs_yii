<?php

use yii\db\Migration;

/**
 * Class m190812_045331_diagnosa_v
 */
class m190812_045331_diagnosa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.diagnosa_v;');

        $this->execute("CREATE OR REPLACE VIEW public.diagnosa_v AS 
                 SELECT diagnosa_m.diagnosa_id,
                    diagnosa_m.diagnosa_kode,
                    diagnosa_m.diagnosa_nama AS diagnosa_namalainnya,
                    diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                    klasifikasidiagnosa_m.klasifikasidiagnosa_nama,
                    dtd_m.dtd_nama,
                    tabularlist_m.tabularlist_chapter,
                    tabularlist_m.tabularlist_versi,
                    diagnosa_m.is_active,
                    diagnosa_m.is_deleted
                   FROM diagnosa_m
                     LEFT JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
                     LEFT JOIN dtd_m ON klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id
                     LEFT JOIN tabularlist_m ON dtd_m.tabularlist_id = tabularlist_m.tabularlist_id
                  WHERE diagnosa_m.is_active = true AND diagnosa_m.is_deleted = false AND diagnosa_m.diagnosa_kode::text <> klasifikasidiagnosa_m.klasifikasidiagnosa_kode::text AND diagnosa_m.klasifikasidiagnosa_id IS NOT NULL
                UNION ALL
                 SELECT diagnosakep_m.diagnosakep_id AS diagnosa_id,
                    diagnosakep_m.diagnosakep_kode AS diagnosa_kode,
                    diagnosakep_m.diagnosakep_nama AS diagnosa_namalainnya,
                    diagnosakep_m.diagnosakep_nama AS diagnosa_nama,
                    NULL::character varying AS klasifikasidiagnosa_nama,
                    NULL::character varying AS dtd_nama,
                    NULL::character varying AS tabularlist_chapter,
                    'ICD_KEP'::character varying AS tabularlist_versi,
                    diagnosakep_m.is_active,
                    diagnosakep_m.is_deleted
                   FROM diagnosakep_m
                  WHERE diagnosakep_m.is_active = true AND diagnosakep_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.diagnosa_v
                        OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190812_045331_diagnosa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190812_045331_diagnosa_v cannot be reverted.\n";

        return false;
    }
    */
}
