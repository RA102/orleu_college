<?php

namespace common\services\pds;

use common\services\pds\exceptions\PersonNotExistException;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;

class PersonUpdateService extends PersonSearchService
{
    /**
     * @param int $person_id
     * @param PdsPersonInterface $person
     * @return PdsPersonInterface
     * @throws ForbiddenHttpException
     * @throws \Throwable
     * @throws \yii\web\ServerErrorHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function update(int $person_id, PdsPersonInterface $person): PdsPersonInterface
    {
        $persons = $this->findOne(['id' => $person_id]);
        if (empty($persons)) {
            throw new PersonNotExistException('Person not exists');
        }

        $userToken = $this->getAccessToken();
        $userRole = $this->getRole();
        $query = array_filter($person->getAttributes());
        $response = $this->updatePdsPerson($person_id, $query, $userToken->token, $userRole);
        return $this->getPersonObject($response);
    }

    private function updatePdsPerson(int $person_id, array $attributes, string $token, string $role)
    {
        $data = $this->pdsGateway->updatePerson($person_id, $attributes, $token, $role);
        return Json::decode($data);
    }
}
