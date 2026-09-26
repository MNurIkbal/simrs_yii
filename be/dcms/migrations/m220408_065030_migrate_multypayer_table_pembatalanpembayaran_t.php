<?php

use yii\db\Migration;

/**
 * Class m220408_065030_migrate_multypayer_table_pembatalanpembayaran_t
 */
class m220408_065030_migrate_multypayer_table_pembatalanpembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pembatalanpembayaran_t" (
              "pembatalanpembayaran_id" int8 NOT NULL PRIMARY KEY,
              "pembayaran_id" int4 NOT NULL,
              "tgl_batal" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
              "pendaftaran_id" int4,
              "pasienadmisi_id" int4,
              "total_tagihan" float8 DEFAULT 0,
              "total_dibayar" float8 DEFAULT 0,
              "total_dijamin" float8 DEFAULT 0,
              "total_sisatagihan" float8 DEFAULT 0,
              "total_kembalian" float8,
              "total_administrasi" float8 DEFAULT 0,
              "total_pembulatan" float8 DEFAULT 0,
              "total_pembebasan" float8 DEFAULT 0,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              "penggunaan_uangmuka" float8,
              "pemberianpiutang_id" int4,
              "total_ditagihkan" float8,
              "total_tunai" float8 DEFAULT 0,
              "total_nontunai" float8 DEFAULT 0,
              "total_discount" float8 DEFAULT 0,
              "total_discountpembayaran" float8,
              "catatan" text COLLATE "pg_catalog"."default",
              "sisa_uangmuka" float8 DEFAULT 0,
              "no_pembayaran" varchar(255) COLLATE "pg_catalog"."default",
              "no_invoicepasien" varchar(255) COLLATE "pg_catalog"."default",
              "pembulatan" float8 DEFAULT 0,
              "total_discountadm" float8 DEFAULT 0,
              "alasan_batal" text COLLATE "pg_catalog"."default"
            )
            ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065030_migrate_multypayer_table_pembatalanpembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065030_migrate_multypayer_table_pembatalanpembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
