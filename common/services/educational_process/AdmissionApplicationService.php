<?php

namespace common\services\educational_process;

use app\models\link\EntrantReceptionGroupLink;
use common\helpers\ApplicationHelper;
use common\helpers\PersonCredentialHelper;
use common\models\educational_process\AdmissionApplication;
use common\models\person\Entrant;
use common\models\person\Person;
use common\services\person\PersonService;
use common\services\TransactionManager;
use frontend\models\forms\AdmissionApplicationForm;

class AdmissionApplicationService
{
    public $personService;
    public $transactionManager;

    /**
     * AdmissionApplicationService constructor.
     * @param PersonService $personService
     */
    public function __construct(PersonService $personService, TransactionManager $transactionManager)
    {
        $this->personService = $personService;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param AdmissionApplicationForm $admissionApplicationForm
     * @param int $institution_id
     * @return AdmissionApplication
     * @throws \Exception
     */
    public function create(
        AdmissionApplicationForm $admissionApplicationForm,
        int $institution_id
    ): AdmissionApplication {
        // TODO: add validation of iin and email uniqueness, check existence of similar application
        $admissionApplication = AdmissionApplication::add(
            $institution_id,
            $admissionApplicationForm->getAttributes()
        );

        if (!$admissionApplication->save()) {
            throw new \Exception('Saving Error');
        }

        return $admissionApplication;
    }

    /**
     * @param int $id
     * @param AdmissionApplicationForm $admissionApplicationForm
     * @return AdmissionApplication
     * @throws \Exception
     */
    public function update(
        int $id,
        AdmissionApplicationForm $admissionApplicationForm
    ): AdmissionApplication {
        $admissionApplication = AdmissionApplication::findOne($id);
        if (!$admissionApplication) {
            throw new \Exception('Not Found');
        }

        $admissionApplication->properties = $admissionApplicationForm->getAttributes();
        if (!$admissionApplication->save()) {
            throw new \Exception('Saving Error');
        }

        return $admissionApplication;
    }

    /**
     * @param int $id
     * @param int $status
     * @param Person $user
     * @param int|null $reception_group_id
     * @return AdmissionApplication
     * @throws \Exception
     */
    public function changeStatus(int $id, int $status, Person $user, int $reception_group_id = null)
    {
        // TODO: add history of status updates
        if ($status === ApplicationHelper::STATUS_ACCEPTED) {
            if (!$reception_group_id) {
                throw new \Exception('Group must be specified for accepted admission application');
            }
            return $this->accept($id, $user, $reception_group_id);
        }

        throw new \Exception('Not supported status');
    }

    /**
     * @param int $application_id
     * @param Person $user
     * @param int $reception_group_id
     * @return AdmissionApplication
     * @throws \Exception
     */
    protected function accept(int $application_id, Person $user, int $reception_group_id): AdmissionApplication
    {
        $admissionApplication = AdmissionApplication::findOne($application_id);
        if (!$admissionApplication) {
            throw new \Exception('Not Found');
        }
        if ($admissionApplication->status !== ApplicationHelper::STATUS_CREATED) {
            throw new \Exception('Forbidden');
        }
        $admissionApplication->status = ApplicationHelper::STATUS_ACCEPTED;

        $entrant = Entrant::add(
            null,
            $admissionApplication->properties['firstname'],
            $admissionApplication->properties['lastname'],
            $admissionApplication->properties['middlename'],
            $admissionApplication->properties['iin']
        );
        $entrant->setAttributes($admissionApplication->properties);

        $this->transactionManager->execute(function () use (
            $entrant,
            $user,
            &$admissionApplication,
            $reception_group_id
        ) {
            $person = $this->personService->create(
                $entrant,
                $admissionApplication->institution_id,
                true,
                $admissionApplication->properties['email'],
                PersonCredentialHelper::TYPE_EMAIL,
                $user->activeAccessToken->token,
                $user->person_type
            );

            $admissionApplication->person_id = $person->id;
            if (!$admissionApplication->save()) {
                throw new \Exception('Saving error');
            }

            $entrantReceptionGroupLink = EntrantReceptionGroupLink::add(
                $person->id,
                $reception_group_id
            );
            if (!$entrantReceptionGroupLink->save()) {
                throw new \Exception(current($entrantReceptionGroupLink->getFirstErrors()));
            }
        });

        return $admissionApplication;
    }
}