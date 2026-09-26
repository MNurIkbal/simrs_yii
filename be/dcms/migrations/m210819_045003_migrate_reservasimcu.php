<?php

use yii\db\Migration;

/**
 * Class m210819_045003_migrate_reservasimcu
 */
class m210819_045003_migrate_reservasimcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE if not EXISTS "public"."reservasimcu_r" (
                      "reservasimcu_id" serial8,
                      "nomorindukpegawai" varchar(30) COLLATE "pg_catalog"."default",
                      "no_rekam_medik" varchar(100) COLLATE "pg_catalog"."default",
                      "nama_lengkap" varchar(100) COLLATE "pg_catalog"."default",
                      "alamat" text COLLATE "pg_catalog"."default",
                      "tanggal_lahir" date,
                      "usia" varchar(30) COLLATE "pg_catalog"."default",
                      "jeniskelamin" varchar(20) COLLATE "pg_catalog"."default",
                      "statusperkawinan" varchar(20) COLLATE "pg_catalog"."default" DEFAULT 9999,
                      "departemen" varchar(50) COLLATE "pg_catalog"."default",
                      "posisi_bagian" varchar(50) COLLATE "pg_catalog"."default",
                      "jenis_mcu" varchar(50) COLLATE "pg_catalog"."default",
                      "nama_perusahaan" varchar(50) COLLATE "pg_catalog"."default",
                      "tgl_pemeriksaan" timestamp(6),
                      "no_passport" varchar(30) COLLATE "pg_catalog"."default",
                      "alamatemail" varchar(100) COLLATE "pg_catalog"."default",
                      "no_mobile_phone" varchar(20) COLLATE "pg_catalog"."default",
                      "no_ktp" varchar(30) COLLATE "pg_catalog"."default",
                      "nama_kota" varchar(50) COLLATE "pg_catalog"."default",
                      "warga_negara" varchar(25) COLLATE "pg_catalog"."default",
                      "alamat_domisili" text COLLATE "pg_catalog"."default",
                      "no_asuransi" varchar(50) COLLATE "pg_catalog"."default",
                      "additional_data" text COLLATE "pg_catalog"."default",
                      "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                      "created_by" int4,
                      "modified_count" int4,
                      "last_modified_date" timestamp(6),
                      "last_modified_by" int4,
                      "is_deleted" bool NOT NULL DEFAULT false,
                      "is_active" bool NOT NULL DEFAULT true,
                      "deleted_date" timestamp(6),
                      "deleted_by" int4,
                      "carabayar_id" int4,
                      "penjamin_id" int4,
                      "asalrujukan_id" int4,
                      "jenisidentitas" varchar(20) COLLATE "pg_catalog"."default",
                      "no_identitas_pasien" varchar(30) COLLATE "pg_catalog"."default",
                      "no_exportexcel" varchar(32) COLLATE "pg_catalog"."default",
                      "tempat_lahir" varchar(25) COLLATE "pg_catalog"."default",
                      "tgl_reservasi" timestamp(6),
                      "pendaftaran_id" int4,
                      "status_reservasi" varchar(255) COLLATE "pg_catalog"."default" DEFAULT 564,
                      "status_mcu" int4 DEFAULT 1041,
                      CONSTRAINT "reservasimcu_r_pkey" PRIMARY KEY ("reservasimcu_id")
                    )
                    ;
        '); 
         
        $this->execute('ALTER TABLE "public"."reservasimcu_r" ADD COLUMN if not exists "status_mcu" int4 DEFAULT 1041;');

        $this->execute('COMMENT ON COLUMN "public"."reservasimcu_r"."status_mcu" IS \'lookup_type=\'\'status_mcu\'\'\';');

        $this->execute('DELETE from lookup_m WHERE lookup_type=\'status_mcu\';');

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1041, 'status_mcu', 'Open', 'Open', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1042, 'status_mcu', 'Close', 'Close', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");

        $this->execute('DROP VIEW if exists public.reservasimcu_v;');

        $this->execute("
            CREATE VIEW \"public\".\"reservasimcu_v\" AS  SELECT reservasimcu_r.no_exportexcel AS no_order,
    reservasimcu_r.tgl_reservasi AS tgl_order,
    reservasimcu_r.penjamin_id,
    penjamin.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin.penjamin_nama AS penjamin,
    reservasimcu_r.jenis_mcu AS paket_mcu,
    count_pasienorder.pasien_order,
    COALESCE(count_pasienperiksa.pasien_periksa, 0::bigint) AS pasien_periksa,
    count_pasienorder.pasien_order - COALESCE(count_pasienperiksa.pasien_periksa, 0::bigint) AS sisa_pasien,
        CASE
            WHEN (count_pasienorder.pasien_order - COALESCE(count_pasienperiksa.pasien_periksa, 0::bigint)) = 0 THEN 1042
            ELSE 1041
        END AS status_mcu_id,
        CASE
            WHEN (count_pasienorder.pasien_order - COALESCE(count_pasienperiksa.pasien_periksa, 0::bigint)) = 0 THEN 'Close'::text
            ELSE 'Open'::text
        END AS status_mcu
   FROM reservasimcu_r
     JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin ON reservasimcu_r.penjamin_id = penjamin.penjamin_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.no_exportexcel,
            a.penjamin_id,
            count(a.no_exportexcel) AS pasien_order
           FROM reservasimcu_r a
          WHERE a.is_deleted = false
          GROUP BY a.no_exportexcel, a.penjamin_id) count_pasienorder ON reservasimcu_r.no_exportexcel::text = count_pasienorder.no_exportexcel::text AND reservasimcu_r.penjamin_id = count_pasienorder.penjamin_id
     LEFT JOIN ( SELECT a.no_exportexcel,
            a.penjamin_id,
            count(a.no_exportexcel) AS pasien_periksa
           FROM reservasimcu_r a
          WHERE a.is_deleted = false AND a.status_reservasi::text = '565'::text
          GROUP BY a.no_exportexcel, a.penjamin_id) count_pasienperiksa ON reservasimcu_r.no_exportexcel::text = count_pasienperiksa.no_exportexcel::text AND reservasimcu_r.penjamin_id = count_pasienperiksa.penjamin_id
  GROUP BY reservasimcu_r.no_exportexcel, reservasimcu_r.tgl_reservasi, reservasimcu_r.penjamin_id, penjamin.penjamin_nama, reservasimcu_r.jenis_mcu, count_pasienorder.pasien_order, count_pasienperiksa.pasien_periksa, reservasimcu_r.status_mcu, penjamin.carabayar_id, carabayar_m.carabayar_nama;");

        $this->execute('ALTER TABLE "public"."reservasimcu_v" OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210819_045003_migrate_reservasimcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210819_045003_migrate_reservasimcu cannot be reverted.\n";

        return false;
    }
    */
}
