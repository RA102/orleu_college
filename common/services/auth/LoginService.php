<?php

namespace common\services\auth;

use common\models\person\AccessToken;
use common\models\person\Person;
use yii\helpers\Json;

class LoginService
{
    public function login(string $username, string $password): bool
    {
        try {
            $authData = $this->getPdsAuthData($username, $password);
            $person = $this->getPerson($authData['person']);
            $this->saveToken($person, $authData);
            \Yii::$app->user->login($person);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     * @throws \Exception
     */
    private function getPdsAuthData(string $username, string $password)
    {
        $connection = curl_init();
        if (!$connection) {
            throw new \Exception('Не удалось инициализировать соединение');
        }

        curl_setopt_array($connection, [
            CURLOPT_URL => \Yii::$app->params['pds_auth_url'],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['indentity' => $username, 'password' => $password]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Access: Bearer ' . \Yii::$app->params['college_pds_access_token'],
            ],
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20
        ]);
        $data = curl_exec($connection);
        $info = curl_getinfo($connection);
        curl_close($connection);

        if ($data === false) {
            throw new \yii\web\ServerErrorHttpException('Недоступен сервер авторизации.');
        }

        if ($info['http_code'] === 422) {
            throw new \yii\web\UnprocessableEntityHttpException('Логин или пароль не верный.');
        }

        return Json::decode($data);
    }

    private function saveToken(Person $person, array $data)
    {
        $accessToken = AccessToken::add(
            $person,
            $data['token'],
            $data['hash'],
            $data['is_temporary']
        );

        if (!$accessToken->save()) {
            throw new \DomainException($accessToken->getErrorSummary(true)[0]);
        }
    }

    private function getPerson(array $personData): Person
    {
        $person = Person::findIdentityByUID($personData['id']);

        if (!$person) {
            $person = Person::add(
                $personData['id'],
                $personData['firstname'],
                $personData['lastname'],
                $personData['middlename'],
                $personData['iin']
            );
        } else {
            $person->firstname = $personData['firstname'];
            $person->lastname = $personData['lastname'];
            $person->middlename = $personData['middlename'];
            $person->iin = $personData['iin'];
        }

        if (!$person->save()) {
            throw new \DomainException($person->getErrorSummary(true)[0]);
        }

        return $person;
    }
}