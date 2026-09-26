<?php

use yii\db\Migration;

/**
 * Class m200923_031114_migrate_20200923_obatalkes
 */
class m200923_031114_migrate_20200923_obatalkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN IF NOT EXISTS "catatan" text;');

        $this->execute('ALTER TABLE "public"."obathistory_r" ADD COLUMN IF NOT EXISTS "catatan" text;');
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"obatalkes_m_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ikbal 27 Februari 2019

DECLARE
    vobatalkes_id int4;
    vobatalkes_nama VARCHAR;
    vharganetto float8;
    vket_ubah_harga int4;
    vlast_modified_by int4;
    vcatatan text;

BEGIN
  
    vobatalkes_id := NEW.obatalkes_id;
    vobatalkes_nama := NEW.obatalkes_nama;
    vharganetto := NEW.harganetto;
    vket_ubah_harga := NEW.ket_ubah_harga;
    vlast_modified_by := NEW.last_modified_by;
    vcatatan := NEW.catatan;
    
    INSERT INTO obathistory_r(
        tgl_obathistory, obatalkes_id, obatalkes_nama, harga_dasar, keterangan, last_modified_by,catatan
    )VALUES(
        CURRENT_TIMESTAMP, vobatalkes_id, vobatalkes_nama, vharganetto, vket_ubah_harga, vlast_modified_by,vcatatan
    );
    
    
RETURN NEW;

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('DROP VIEW if exists "public"."obathistory_v";');

        $this->execute("
            CREATE VIEW \"public\".\"obathistory_v\" AS  SELECT obathistory_r.obatalkes_id,
    obathistory_r.obatalkes_nama,
    obathistory_r.tgl_obathistory,
    obathistory_r.harga_dasar,
    fgetnamalookup((obathistory_r.keterangan)::integer) AS keterangan,
    pegawai_m.nama_pegawai,
    obathistory_r.catatan
   FROM ((obathistory_r
     JOIN loginpemakai_k ON ((obathistory_r.last_modified_by = loginpemakai_k.loginpemakai_id)))
     LEFT JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)));");
        
        $this->execute('ALTER TABLE "public"."obathistory_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200923_031114_migrate_20200923_obatalkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200923_031114_migrate_20200923_obatalkes cannot be reverted.\n";

        return false;
    }
    */
}
