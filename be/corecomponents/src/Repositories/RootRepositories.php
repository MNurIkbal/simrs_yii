<?php

namespace Doco\Repositories;

use Yii;

class RootRepositories
{
    /**
     * execute SQL SP
     *
     * @param String $spCommand
     * @param Array $arrayParam
     * @return Array/Object
     * @author Tsani Nashrullah
     **/
    public function executeSp($spCommand, $arrayParam = [], $allRecord = true)
    {
        if (is_array($spCommand)) {
            $commandString = 'CALL ' . $spCommand['command'] . '(';
            $indexKey = 0;
            foreach ($spCommand['param'] as $keyParam => $param) {
                $commandString .= ':' . $keyParam . ((($indexKey + 1) >= count($spCommand['param'])) ? ')' : ', ');
                $indexKey += 1;
            }
            $command = Yii::$app->db->createCommand($commandString);
            foreach ($spCommand['param'] as $secondKeyParam => $valueParam) {
                $command->bindValue(":" .$secondKeyParam, $valueParam);
            }

            // return $command->getRawSql();
            if (isset($spCommand['executeable'])) {
                $result = $command->execute();
            } else if (isset($spCommand['allRecord'])) {
                $result = $command->queryAll();
            } else {
                if (isset($spCommand['allRecord'])) {
                    $result = $command->queryAll();
                } else {
                    $result = $command->queryOne();
                }
            }

            $errorMessage = isset($result[0]) && !empty($result[0]['err_message']) ? $result[0]['err_message'] : ((isset($result['err_messages']) && !empty($result['err_messages'])) ? $result['err_messages'] : '');
            if (!empty($errorMessage)) {
                Yii::error([
                    "Message" => $errorMessage,
                    "ON" => $spCommand
                ]);
                throw new \yii\web\HttpException(500, 'Something went wrong on SP.');
            } else {
                return $result;
            }
        } else {
            foreach ($arrayParam as $param => $valueParam) {
                $spCommand = str_replace('[' . $param . ']', $valueParam, $spCommand);
            }
            if ($allRecord) {
                return Yii::$app
                    ->db
                    ->createCommand('CALL ' . $spCommand)
                    ->queryAll();
            } else {
                return Yii::$app
                    ->db
                    ->createCommand('CALL ' . $spCommand)
                    ->queryOne();
            }
        }
    }
}
