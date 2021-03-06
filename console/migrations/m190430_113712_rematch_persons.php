<?php

use common\models\person\Person;
use common\models\person\PersonCredential;
use yii\db\Migration;

/**
 * Class m190430_113712_rematch_persons
 */
class m190430_113712_rematch_persons extends Migration
{
    private $max_id = 1298702;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /* @var Person[] $persons */
        $persons = Person::find()
            ->joinWith('institutions')
            ->where(['<=', Person::tableName() . '.id', $this->max_id])
            ->orderBy(Person::tableName() . '.id')
            ->all();
        $total = count($persons);
        $persons_delete = [];
        $i = $j = 0;
        foreach ($persons as $person) {
            $total--;
            if (count($person->institutions) > 0) {
                continue;
            }

            if ($person->portal_uid !== null) {
                /* @var Person[] $otherPersons */
                $otherPersons = Person::find()
                    ->where([
                        'AND',
                        ['portal_uid' => $person->portal_uid],
                        ['>', 'id', $this->max_id]
                    ])
                    ->all();

                if (count($otherPersons) > 0) {
                    echo $person->id . ": " . $person->getFullName() . ": " . $person->iin . "\n";
                    foreach ($otherPersons as $otherPerson) {
                        echo $otherPerson->id . ": " . $otherPerson->getFullName() . ": " . $otherPerson->iin . "\n";
                        $this->moveCredentials($person, $otherPerson);
                        $i++;
                    }
                    array_push($persons_delete, $person->id);
                    echo $total . "--- \n";
                    $j++;
                }
            }
        }

        $this->delete(Person::tableName(), ['id' => $persons_delete]);

        echo "Person Found: " . $j . "\n";
        echo "Other person Found: " . $i . "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    private function moveCredentials(Person $person, Person $newPerson)
    {
        $this->update(Person::tableName(), [
            'firstname' => $person->firstname,
            'lastname' => $person->lastname,
            'middlename' => $person->middlename,
            'iin' => $person->iin
        ], [
            'id' => $newPerson->id
        ]);

        $this->execute('UPDATE ' . PersonCredential::tableName() . ' SET person_id = :new_person_id WHERE person_id = :person_id', [
            ':person_id' => $person->id,
            ':new_person_id' => $newPerson->id
        ]);
    }
}
