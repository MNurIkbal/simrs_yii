<?php

use yii\db\Migration;

/**
 * Class m201229_083007_migrate_20201229_SY_tabel_pendaftaran_t
 */
class m201229_083007_migrate_20201229_SY_tabel_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaran_t"
ADD COLUMN IF NOT EXISTS "namadepan" varchar(20) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "nama_pasien" varchar(50) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "propinsi_id" int4,
ADD COLUMN IF NOT EXISTS "kabupaten_id" int4,
ADD COLUMN IF NOT EXISTS "kecamatan_id" int4,
ADD COLUMN IF NOT EXISTS "kelurahan_id" int4,
ADD COLUMN IF NOT EXISTS "rt" int2,
ADD COLUMN IF NOT EXISTS "rw" int2,
ADD COLUMN IF NOT EXISTS "kode_pos" varchar(15) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "alamat_pasien" text COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "no_telepon_pasien" varchar(15) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "pekerjaan_id" int4,
ADD COLUMN IF NOT EXISTS "pt" varchar(50);');



$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."namadepan" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."nama_pasien" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."propinsi_id" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."kabupaten_id" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."kecamatan_id" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."kelurahan_id" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."rt" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."rw" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."kode_pos" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."alamat_pasien" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."no_telepon_pasien" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."pekerjaan_id" IS \'keperluan St Yusup\';');
$this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."pt" IS \'keperluan St Yusup\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_083007_migrate_20201229_SY_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_083007_migrate_20201229_SY_tabel_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
