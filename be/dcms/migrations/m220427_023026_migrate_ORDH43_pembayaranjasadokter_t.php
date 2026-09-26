<?php

use yii\db\Migration;

/**
 * Class m220427_023026_migrate_ORDH43_pembayaranjasadokter_t
 */
class m220427_023026_migrate_ORDH43_pembayaranjasadokter_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS public.pembayaranjasadokter_t (
            pembayaranjasadokter_id serial8 NOT NULL,
            tgl_flag timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            pendaftaran_id int4,
            pegawai_id int4,
            tindakanpelayanan_id int4,
            tarif_tindakan float8 DEFAULT 0,
            additional_data text COLLATE pg_catalog.default,
            created_date timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            created_by int4,
            flag_key text COLLATE pg_catalog.default,
            modified_count int4,
            last_modified_date timestamp(6),
            last_modified_by int4,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date time(6),
            deleted_by int4,
            CONSTRAINT pembayaranjasadokter_t_pkey PRIMARY KEY (pembayaranjasadokter_id)
          );');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220427_023026_migrate_ORDH43_pembayaranjasadokter_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_023026_migrate_ORDH43_pembayaranjasadokter_t cannot be reverted.\n";

        return false;
    }
    */
}
