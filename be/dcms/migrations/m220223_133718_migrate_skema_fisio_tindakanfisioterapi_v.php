<?php

use yii\db\Migration;

/**
 * Class m220223_133718_migrate_skema_fisio_tindakanfisioterapi_v
 */
class m220223_133718_migrate_skema_fisio_tindakanfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.tindakanfisioterapi_v;');
            $this->execute("
                CREATE VIEW \"public\".\"tindakanfisioterapi_v\" AS
                 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 1)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 2)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 3)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 4)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 5)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 5)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 6)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 7)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 8)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 9)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 10)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 11)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
    pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama
   FROM (pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id = 12)) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
UNION ALL
 SELECT jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
    pemeriksaanfisio_m.daftartindakan_id,
    jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
    pemeriksaanfisio_m.pemeriksaanfisio_kode,
    pemeriksaanfisio_m.pemeriksaanfisio_nama,
    daftarpaketfisiodet_m.daftartindakandet_id,
    daftartindakandet_m.daftartindakandet_nama
   FROM ((((pemeriksaanfisio_m
     JOIN ( SELECT a.jenispemeriksaanfisio_id,
            a.jenispemeriksaanfisio_nama
           FROM jenispemeriksaanfisio_m a
          WHERE (a.jenispemeriksaanfisio_id <> ALL (ARRAY[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]))) jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
     JOIN ( SELECT a.parent_id,
            a.daftarpaketfisio_id
           FROM daftarpaketfisio_m a) daftarpaketfisio_m ON ((pemeriksaanfisio_m.daftartindakan_id = daftarpaketfisio_m.parent_id)))
     JOIN ( SELECT a.daftarpaketfisio_id,
            a.daftartindakan_id AS daftartindakandet_id,
            a.is_deleted
           FROM daftarpaketfisiodet_m a
          WHERE (a.is_deleted = false)) daftarpaketfisiodet_m ON ((daftarpaketfisio_m.daftarpaketfisio_id = daftarpaketfisiodet_m.daftarpaketfisio_id)))
     JOIN ( SELECT a.daftartindakan_id AS daftartindakandet_id,
            a.daftartindakan_nama AS daftartindakandet_nama,
            a.is_deleted
           FROM daftartindakan_m a
          WHERE (a.is_deleted = false)) daftartindakandet_m ON ((daftarpaketfisiodet_m.daftartindakandet_id = daftartindakandet_m.daftartindakandet_id)))
                ;");
            $this->execute('
                ALTER TABLE public.tindakanfisioterapi_v OWNER TO postgres;
                ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_133718_migrate_skema_fisio_tindakanfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_133718_migrate_skema_fisio_tindakanfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
