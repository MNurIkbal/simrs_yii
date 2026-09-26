<?php

use yii\db\Migration;

/**
 * Class m201008_032208_migrate_mhkn_20201008_penjamin_view
 */
class m201008_032208_migrate_mhkn_20201008_penjamin_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('DROP VIEW if exists public.penjamin_v;');
            $this->execute("
            CREATE OR REPLACE VIEW public.penjamin_v
 AS  SELECT carabayar_m.carabayar_id,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(carabayar_m.groupcarabayar_id) AS group_carabayar,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_m.*::penjamin_m AS penjamin_m,
    penjamin_m.penjamin_namalainnya,
    penjamin_m.is_active,
    penjamin_m.is_online,
    penjamin_m.groupmargin_id,
    groupmargin_m.groupmargin_nama,
    penjamin_m.penjamin_kode
   FROM ((carabayar_m
     JOIN penjamin_m ON ((carabayar_m.carabayar_id = penjamin_m.carabayar_id)))
     LEFT JOIN groupmargin_m ON ((penjamin_m.groupmargin_id = groupmargin_m.groupmargin_id)))
  WHERE (penjamin_m.is_deleted = false)");

            $this->execute('ALTER TABLE public.infokunjunganrs_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_032208_migrate_mhkn_20201008_penjamin_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_032208_migrate_mhkn_20201008_penjamin_view cannot be reverted.\n";

        return false;
    }
    */
}
