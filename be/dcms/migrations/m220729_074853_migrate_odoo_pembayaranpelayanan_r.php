<?php

use yii\db\Migration;

/**
 * Class m220729_074853_migrate_odoo_pembayaranpelayanan_r
 */
class m220729_074853_migrate_odoo_pembayaranpelayanan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pembayaranpelayanan_r" (
                "id" serial8 NOT NULL PRIMARY KEY,
                "pembayaranpelayanan_id" int4,
                "carabayar_id" int4,
                "ruangan_id" int4,
                "penjamin_id" int4,
                "pembebasantarif_id" int4,
                "pendaftaran_id" int4,
                "pasienadmisi_id" int4,
                "suratketjaminan_id" int4,
                "tandabuktibayar_id" int4,
                "pasien_id" int4,
                "pembklaimdetail_id" int4,
                "ruangan_pelakhir_id" int4,
                "no_pembayaran" varchar(50) COLLATE "pg_catalog"."default",
                "tgl_pembayaran" timestamp(6),
                "no_resep" varchar(50) COLLATE "pg_catalog"."default",
                "no_sjp" varchar(50) COLLATE "pg_catalog"."default",
                "total_biayaoa" float8 DEFAULT 0,
                "total_biayatindakan" float8 DEFAULT 0,
                "total_biayapelayanan" float8 DEFAULT 0,
                "total_subsidiasuransi" float8 DEFAULT 0,
                "total_subsidipemerintah" float8 DEFAULT 0,
                "total_subsidirs" float8 DEFAULT 0,
                "total_iurbiaya" float8 DEFAULT 0,
                "total_bayartindakan" float8 DEFAULT 0,
                "total_discount" float8 DEFAULT 0,
                "total_pembebasan" float8 DEFAULT 0,
                "total_sisatagihan" float8 DEFAULT 0,
                "statusbayar" varchar(30) COLLATE "pg_catalog"."default",
                "biaya_administrasi" float8 DEFAULT 0,
                "e_collection" bool,
                "no_rekening" varchar(32) COLLATE "pg_catalog"."default",
                "nama_pemrekening" varchar(100) COLLATE "pg_catalog"."default",
                "penggunaan_uangmuka" float8 DEFAULT 0,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool DEFAULT false,
                "is_active" bool DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                "total_terbayar" float8 DEFAULT 0,
                "pembulatan" float8 DEFAULT 0,
                "is_lunas" bool,
                "penjualanresep_id" int4,
                "pembayaran_id" int4,
                "is_penjaminutama" bool DEFAULT false,
                "keterangan" varchar(255) COLLATE "pg_catalog"."default",
                "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
                "is_sent" bool DEFAULT false,
                "is_sending" bool DEFAULT false,
                "id_sync_sercon" text COLLATE "pg_catalog"."default",
                "sync_respon" text COLLATE "pg_catalog"."default",
                "is_update" bool DEFAULT false,
                "id_sync_sercon_update" text COLLATE "pg_catalog"."default",
                "sync_respon_update" text COLLATE "pg_catalog"."default"
                )
            ;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_074853_migrate_odoo_pembayaranpelayanan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_074853_migrate_odoo_pembayaranpelayanan_r cannot be reverted.\n";

        return false;
    }
    */
}
