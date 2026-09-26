<?php

namespace Doco\components;

use Yii;

class DocoNotification
{

    /**
    * @author Randy Vianda Putra
    * @todo init curl to onesignal
    * @param array header
    * @param array heading
    * @param array content
    * @param array player_id
    * @param string app_id
    * @param array data
    */
    public static function createNotification($header = [], array $heading, array $content, $player = [], $app_id = '',  $data = [])
    {
        $app_id = !empty($app_id) 
            ? $app_id
            :'308efa2c-21a3-41c6-a150-7ced4a1e0738';
        $player_default = [];
        $data = ['test' => 'notif test'];
        $included_players = !empty($player)
            ? $player
            : $player_default;
        $fields = [
            'app_id' => $app_id,
            'include_player_ids' => $included_players,
            'data' => $data,
            'headings' => $heading,
            'contents' => $content,
        ];

        $fields = json_encode($fields);
        $header_default = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ZDdjYjc0OWQtNTYwYi00ODBkLThjNmItNTY0ZGUxOGIwMWNm'
        ];
        $header = !empty($header) ? $header : $header_default;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;

    }

}