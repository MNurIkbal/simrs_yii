<?php

use yii\db\Migration;

/**
 * Class m230820_142404_recreate_logedittagihan_r_rpp303
 */
class m230820_142404_recreate_logedittagihan_r_rpp303 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS logedittagihan_r");

        $this->execute("
        CREATE TABLE IF NOT EXISTS public.logedittagihan_r (
            logedittagihan_id bigserial NOT NULL,
            pendaftaran_id int4 NULL,
            pasienmasukpenunjang_id int4 NULL,
            kelompok varchar(255) NULL,
            status text NULL,
            tipe_pasien varchar(50) NULL,
            \"checkPenjamin\" varchar(15) NULL,
            cyto varchar(255) NULL,
            \"defaultPenjamin\" text NULL,
            dijamin float8 NULL,
            harga float8 NULL,
            instalasi varchar(50) NULL,
            \"isPenjamin\" varchar(15) NULL,
            is_obat bool NULL,
            kelompoktindakan_nama varchar(100) NULL,
            keterangan text NULL,
            nominal_diskon float8 NULL,
            penjamin varchar(255) NULL,
            persen_diskon float8 NULL,
            qty float8 NULL,
            subtotal float8 NULL,
            subtotal_origin float8 NULL,
            tanggal timestamp(0) NULL,
            tindakan varchar(255) NULL,
            tindakan_obat_id int4 NULL,
            \"totalDibayar\" float8 NULL,
            value text NULL,
            penyulit float8 NULL,
            pelayanan_id int4 NULL,
            harga_origin float8 NULL,
            cyto_origin float8 NULL,
            penyulit_origin float8 NULL,
            id int4 NULL,
            is_ditagihkan bool NULL DEFAULT true,
            \"subPenjamin\" text NULL,
            dijamin_subpayer float8 NULL,
            plafon_payer float8 NULL,
            plafon_subpayer float8 NULL,
            excess_pasien float8 NULL,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT logedittagihan_r_pkey PRIMARY KEY (logedittagihan_id)
          );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230820_142404_recreate_logedittagihan_r_rpp303 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230820_142404_recreate_logedittagihan_r_rpp303 cannot be reverted.\n";

        return false;
    }
    */
}
