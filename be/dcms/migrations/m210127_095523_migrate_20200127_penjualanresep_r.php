<?php

use yii\db\Migration;

/**
 * Class m210127_095523_migrate_20200127_penjualanresep_r
 */
class m210127_095523_migrate_20200127_penjualanresep_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        CREATE TABLE NOT EXISTS "public"."penjualanresep_r" (
        "id" serial8,
        "penjualanresep_id" int4,
        "pasienadmisi_id" int4,
        "pegawai_id" int4,
        "pendaftaran_id" int4,
        "returresep_id" int4,
        "kelaspelayanan_id" int4,
        "penjamin_id" int4,
        "pasien_id" int4,
        "carabayar_id" int4,
        "ruangan_id" int4,
        "reseptur_id" int4,
        "shift_id" int4,
        "tglpenjualan" timestamp(6),
        "jenispenjualan" varchar COLLATE "pg_catalog"."default",
        "tglresep" timestamp(6),
        "noresep" varchar COLLATE "pg_catalog"."default",
        "totharganetto" float8,
        "totalhargajual" float8,
        "totaltarifservice" float8,
        "biayaadministrasi" float8,
        "biayakonseling" float8,
        "pembulatanharga" float8,
        "jasadokterresep" float8,
        "discount" float8,
        "subsidiasuransi" float8,
        "subsidipemerintah" float8,
        "subsidirs" float8,
        "iurbiaya" float8,
        "lamapelayanan" int4,
        "penjpasienpegawai_id" int4,
        "penjpasienruangan_id" int4,
        "antrianfarmasi_id" int4,
        "permohonanoa_id" int4,
        "takaranresep" float8,
        "isresepperawatan" bool,
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
        "iter" float4,
        "nama_pembeli" varchar(255) COLLATE "pg_catalog"."default",
        "pegawairesep_id" int4,
        "karyawan_id" int4,
        "catatan" text COLLATE "pg_catalog"."default",
        "status_bayar" int2 DEFAULT 349,
        "status_reseptur" int2 DEFAULT 347,
        "antrian_id" int4,
        "pembatalanresep_id" int4,
        "status_worklist" int2 DEFAULT 674,
        "tgl_lahir" date,
        "log_user" json,
        "is_sent" bool DEFAULT false,
        "is_sending" bool DEFAULT false,
        "id_sync_sercon" text COLLATE "pg_catalog"."default",
        "sync_respon" text COLLATE "pg_catalog"."default",
        "keterangan" varchar(255) COLLATE "pg_catalog"."default",
        "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
        CONSTRAINT "penjualanresep_r_pkey" PRIMARY KEY ("id")
        )
        ;');

        $this->execute('ALTER TABLE "public"."penjualanresep_r" OWNER TO "postgres";');
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_095523_migrate_20200127_penjualanresep_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_095523_migrate_20200127_penjualanresep_r cannot be reverted.\n";

        return false;
    }
    */
}
