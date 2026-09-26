<?php

use yii\db\Migration;

/**
 * Class m210913_014957_migrate_US1463_view_kemendagri
 */
class m210913_014957_migrate_US1463_view_kemendagri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.propinsi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"propinsi_v\" AS
            SELECT propinsi_m.propinsi_id,
            propinsi_m.propinsi_nama,
            propinsi_m.kode_propinsi AS kode_kemendagri_propinsi
            FROM propinsi_m
            ;");
        $this->execute('
            ALTER TABLE public.propinsi_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.kabupaten_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kabupaten_v\" AS
            SELECT propinsi_m.propinsi_id,
            kabupaten_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            propinsi_m.kode_propinsi AS kode_kemendagri_propinsi,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendagri_kabupaten
            FROM (propinsi_m
            JOIN kabupaten_m ON ((propinsi_m.propinsi_id = kabupaten_m.propinsi_id)))
            ;");
        $this->execute('
            ALTER TABLE public.kabupaten_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.kecamatan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kecamatan_v\" AS
            SELECT propinsi_m.propinsi_id,
            kabupaten_m.kabupaten_id,
            kecamatan_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            propinsi_m.kode_propinsi AS kode_kemendagri_propinsi,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendagri_kabupaten,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendagri_kecamatan
            FROM ((propinsi_m
            JOIN kabupaten_m ON ((propinsi_m.propinsi_id = kabupaten_m.propinsi_id)))
            JOIN kecamatan_m ON ((kabupaten_m.kabupaten_id = kecamatan_m.kabupaten_id)))
            ;");
        $this->execute('
            ALTER TABLE public.kecamatan_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.kelurahan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"kelurahan_v\" AS
            SELECT propinsi_m.propinsi_id,
            kabupaten_m.kabupaten_id,
            kecamatan_m.kecamatan_id,
            kelurahan_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            propinsi_m.kode_propinsi AS kode_kemendagri_propinsi,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendagri_kabupaten,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendagri_kecamatan,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendagri_kelurahan
            FROM (((propinsi_m
            JOIN kabupaten_m ON ((propinsi_m.propinsi_id = kabupaten_m.propinsi_id)))
            JOIN kecamatan_m ON ((kabupaten_m.kabupaten_id = kecamatan_m.kabupaten_id)))
            JOIN kelurahan_m ON ((kecamatan_m.kecamatan_id = kelurahan_m.kecamatan_id)))
            ;");
        $this->execute('
            ALTER TABLE public.kelurahan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210913_014957_migrate_US1463_view_kemendagri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210913_014957_migrate_US1463_view_kemendagri cannot be reverted.\n";

        return false;
    }
    */
}
