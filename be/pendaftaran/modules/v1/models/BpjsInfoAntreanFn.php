<?php
namespace app\modules\v1\models;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class BpjsInfoAntreanFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return 'bpjs_info_antrean_fn';
    }

    public static function tableName($ext_obj = null)
    {
        $params = isset($ext_obj) ? $ext_obj : static::$_extParam;

        if (!isset($params)) {
            return parent::tableName($ext_obj);
        }

        if (is_array($params) && ArrayHelper::isAssociative($params)) {
            throw new InvalidConfigException('Konfigurasi extParam tidak dapat menggunakan Associative Array');
        }

        if (is_array($params)) {
            foreach ($params as $val_param) {
                if ($val_param === '0' || $val_param === 0) {
                    continue;
                }

                if ($val_param === null) {
                    continue;
                }

                if (is_string($val_param) && strtoupper($val_param) === 'NULL') {
                    continue;
                }

                if (empty($val_param)) {
                    throw new InvalidConfigException('Konfigurasi Property extParam Salah: Nilai tidak boleh ');
                }
            }

            $params = array_map(function ($value) {
                if ($value === null || (is_string($value) && strtoupper($value) === 'NULL')) {
                    return 'NULL';
                }

                if ($value instanceof \yii\db\Expression) {
                    return (string) $value;
                }

                $escaped = str_replace("'", "''", $value);

                return "'" . $escaped . "'";
            }, $params);

            return static::functionName() . '(' . implode(',', $params) . ')';
        }

        return parent::tableName($params);
    }
}
