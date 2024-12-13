<?php

class AmoCrmV4Client
{
    var $curl = null;
    var $subDomain = "test"; #Наш аккаунт - поддомен

    var $client_id = "0cb9e8c6-cbac-452b-a555-90935d5b7acd";
    var $client_secret = "zULhFvrq4HdV0rUTDEVPOKmNV7WK9VMI1pHTtvvGnBwcNy31QfXDPTVxDBGWHWaT";
    var $code = "def502008cb61b77438ec20fbdhy46a66f42ba03780060da5c94bdf0132e0c246971c9a29d89b4173c4f1cf1f2bfdee370364f50cb1984d247c645cf11be68ab8f9b04b5872a2101ccc10837f6d039e8591c8b9e72317b5b9ce8b9019911997fa57b130c1a830dba53b9651ad3d53510bf97774dd8215a8af534305ea7fcb6fc12a9f1087d0219a97dfcfcd41d65f077793739a36868f9f3d80d8a1b5644ac3ac9bdfaf262b6fb549c65b98b219aa0333bae70d4d934c5c06e2675f398ac1c9494a7d59215713e1295b210d0bd18944b02f0ce9ad7a681734b8931182a81db9de21d93452cb0c0634a2c14cb1b5c78258e559d64350680e7bb83003b45d61d6c09d8ad1bec0c0491a9acc3d502a7346f17bc60eb70cf5ea77c6ab04b8389a445b57f962504fabd630f4f87bc29a046c72f0354823f7da82f988583fb21fa35420ef10d17bf93c471bd7b03f1f5cda5f640b743c52f31a0dc47463ece78784edf3e0dd9f8e1a5fed908af49a5efbd87a68c9e760b7079dbfa45d6004c0ab34ac1259ea11ea4a618625fb43dc78ba25ea90bdf783ab69972349380e2b38a241005166cb9e081723b97b31c7f0878f1767146dbfd88a70f781d06125611c5d1e5b2f03840bc205503364a69550548d7ad22bcef282b7fec4be19f82ef818faee725957f25b9c14";
    var $redirect_uri = "https://test.ru/index.html";

    var $access_token = "eyJ0eXAiOiJKV1QiLCJfrGciOiJSUzI1NiIsImp0aSI6IjViMTliZjY5MDA0NTYwNmNlYTEyOWM2Yjk5MWZkNjQyMjhhZWRjZWUwOThiYWFmMjZkMzBlNmY3YmRmMTIxNmU3N2RlNTdjMmJkNmE3ZDYwIn0.eyJhdWQiOiI1NGQ5ZDE5NS0yMzUwLTQyMTktYTI5MC04YjViOGUxOWYxMTAiLCJqdGkiOiI1YjE5YmY2OTAwNDU2MDZjZWExMjljNmI5OTFmZDY0MjI4YWVkY2VlMDk4YmFhZjI2ZDMwZTZmN2JkZjEyMTZlNzdkZTU3YzJiZDZhN2Q2MCIsImlhdCI6MTY5NzAzODQ3MSwibmJmIjoxNjk3MDM4NDcxLCJleHAiOjE2OTcxMjQ4NzEsInN1YiI6IjEwMTU0NDIyIiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxMDY0NjA2LCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOiJ2MiIsInNjb3BlcyI6WyJwdXNoX25vdGlmaWNhdGlvbnMiLCJmaWxlcyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.RBPEJd-qes-0E7zFT5NXxqDtHNEv-ikrHMTKFOU4zYf22PWgbR8-tDQ9ye9Sk7FipZo35mxm0We_GADTBVD1klLfHF657apNB7o87OmfcYh1Uvg3xbOpRLvL1WGdzRBTncZNLxvQw57eeQIm1yro1AYs2LW1EvRXEdQccYL6nIN_mTC98QWyZBF4ADa2xoAa_fZfA0RiufUywqmQb6qoPn9Sxhc1PWnAjwGYNsGXBy-rRTe0DS3LbEWKUHPaFWUQye4VEeJWhOVdi7I991tOqL1LzfFxGvbWkRxUZkLgJl96jN_i7iBkgKrx8RH5IG3pd5b5f8zsR1qz2A9EBb_cKw";

    var $token_file = "C:\OSPanel\domains\crm\TOKEN.txt";

    function __construct($subDomain, $client_id, $client_secret, $code, $redirect_uri)
    {
        $this->subDomain = $subDomain;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->code = $code;
        $this->redirect_uri = $redirect_uri;

        if(file_exists($this->token_file)) {
            $expires_in = json_decode(file_get_contents("C:\OSPanel\domains\crm\TOKEN.txt"))->{'expires_in'};
            if($expires_in < time()) {
                $this->access_token = json_decode(file_get_contents("C:\OSPanel\domains\crm\TOKEN.txt"))->{'access_token'};
                $this->GetToken(true);
            }
            else
                $this->access_token = json_decode(file_get_contents("C:\OSPanel\domains\crm\TOKEN.txt"))->{'access_token'};
        }
        else
            $this->GetToken();
    }

    function GetToken($refresh = false){
        $link = 'https://' . $this->subDomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

        /** Соберем данные для запроса */
        if($refresh)
        {
            $data = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,                
                'grant_type' => 'refresh_token',
                'refresh_token' => json_decode(file_get_contents("TOKEN.txt"))->{'refresh_token'},
                'redirect_uri' => $this->redirect_uri
            ];
        } else {
            $data = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => 'authorization_code',
                'code' => $this->code,
                'redirect_uri' => $this->redirect_uri
            ];
        }

        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        try
        {
            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        }
        catch(\Exception $e)
        {
            echo $out;
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }

        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */
        $response = json_decode($out, true);

        $this->access_token = $response['access_token'];

        $token = [
            'access_token' => $response['access_token'], //Access токен
            'refresh_token' => $response['refresh_token'], //Refresh токен
            'token_type' => $response['token_type'], //Тип токена
            'expires_in' => time() + $response['expires_in'] //Через сколько действие токена истекает
        ];

        file_put_contents("TOKEN.txt", json_encode($token));
    }

    function CurlRequest($link, $method, $PostFields = [])
    {
        /** Формируем заголовки */
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        if ($method == "POST" || $method == 'PATCH') {
            curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($PostFields));
        }
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int) $code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        );

        try
        {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
            }

        } catch (Exception $E) {
            $this->Error('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode() . $link);
        }


        return $out;
    }

    function GETRequestApi($service, $params = [])
    {
        $result = '';
        try {
            $url = "";
            if ($params !== []) {
                $params = ToGetArray($params);
                $url = 'https://' . $this->subDomain . '.amocrm.ru/api/v4/' . $service . '?' . $params;
            } else
                $url = 'https://' . $this->subDomain . '.amocrm.ru/api/v4/' . $service;

            $result = json_decode($this->CurlRequest($url, 'GET'), true);

            usleep(250000);

        } catch (ErrorException $e) {
            $this->Error($e);
        }

        return $result;
    }

    function POSTRequestApi($service, $params = [], $method = "POST")
    {
        $result = '';
        try {
            $url = 'https://' . $this->subDomain . '.amocrm.ru/api/v4/' . $service;

            $result = json_decode($this->CurlRequest($url, $method, $params), true);

            usleep(250000);

        } catch (ErrorException $e) {
            $this->Error($e);
        }

        return $result;
    }

    function Error($e){
        file_put_contents("ERROR_LOG.txt", $e);
    }
}

function ToGetArray($array){
    $result = "";

    foreach ($array as $key => $value)
    {
        $result .= $key . "=" . $value . '&';
    }

    return substr($result,0,-1);
}
