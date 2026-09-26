<?php

use yii\db\Migration;

/**
 * Class m200205_062326_struktur_sync_20200205_2
 */
class m200205_062326_struktur_sync_20200205_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bank_m" 
                          ADD COLUMN "url" text COLLATE "pg_catalog"."default",
                          ADD COLUMN "cons_id" text COLLATE "pg_catalog"."default",
                          ADD COLUMN "secret_key" text COLLATE "pg_catalog"."default";');

        $this->execute('DROP VIEW if exists public.pendaftaranpenjamin_v;');

        $this->execute('ALTER TABLE "public"."pendaftaranpenjamin_t" ALTER COLUMN "namapemilikasuransi" TYPE varchar(255) COLLATE "pg_catalog"."default";');
        
        $this->execute("
            CREATE OR REPLACE VIEW public.pendaftaranpenjamin_v AS 
 SELECT pendaftaranpenjamin_t.pendaftaranpenjamin_id,
    pendaftaranpenjamin_t.pendaftaran_id,
    pendaftaranpenjamin_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaranpenjamin_t.penjamin_id,
    pendaftaranpenjamin_t.penjamin_nama,
    pendaftaranpenjamin_t.nokartuasuransi,
    pendaftaranpenjamin_t.pasien_id,
    pendaftaranpenjamin_t.asuransipasien_id,
    pendaftaranpenjamin_t.nama_pasien,
    pendaftaranpenjamin_t.namapemilikasuransi,
    pendaftaranpenjamin_t.nominal_dijamin,
    pendaftaranpenjamin_t.alasan_batal,
    pendaftaranpenjamin_t.is_deleted,
    pendaftaranpenjamin_t.created_date
   FROM pendaftaranpenjamin_t
     JOIN carabayar_m ON pendaftaranpenjamin_t.carabayar_id = carabayar_m.carabayar_id
  WHERE pendaftaranpenjamin_t.is_deleted IS FALSE;");

        $this->execute('ALTER TABLE public.pendaftaranpenjamin_v
  OWNER TO postgres;');

         $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_062326_struktur_sync_20200205_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_062326_struktur_sync_20200205_2 cannot be reverted.\n";

        return false;
    }
    */
}
