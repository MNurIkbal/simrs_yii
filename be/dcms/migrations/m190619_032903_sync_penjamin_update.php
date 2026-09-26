<?php

use yii\db\Migration;

/**
 * Class m190619_032903_sync_penjamin_update
 */
class m190619_032903_sync_penjamin_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
    DROP VIEW IF exists public.sync_penjamin;
        ');

         $this->execute('
    CREATE OR REPLACE VIEW public.sync_penjamin AS 
 SELECT penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_m.alamat_penjamin,
    \'\'::character varying AS email,
    \'\'::character varying AS npwp,
    \'\'::character varying AS no_telp,
    \'\'::character varying AS website,
    COALESCE(penjamin_m.deleted_date, penjamin_m.last_modified_date, penjamin_m.created_date) AS date,
        CASE penjamin_m.is_deleted
            WHEN true THEN 1
            ELSE 0
        END AS deleted
   FROM penjamin_m
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id;
        ');

         $this->execute('
    ALTER TABLE public.sync_penjamin
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190619_032903_sync_penjamin_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_032903_sync_penjamin_update cannot be reverted.\n";

        return false;
    }
    */
}
