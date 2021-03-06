<?php

namespace marsoltys\yii2user\models;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see ProfileField
 */
class ProfileFieldQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return ProfileField[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ProfileField|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return $this
     */
    public function forAll()
    {
        return $this->andWhere(['visible' => ProfileField::VISIBLE_ALL])->sort();
    }

    /**
     * @return $this
     */
    public function forUser()
    {
        return $this->andWhere('visible >='.ProfileField::VISIBLE_REGISTER_USER)->sort();
    }

    /**
     * @return $this
     */
    public function forOwner()
    {
        return $this->andWhere('visible >='.ProfileField::VISIBLE_ONLY_OWNER)->sort();
    }

    /**
     * @return $this
     */
    public function forRegistration()
    {
        return $this->andFilterWhere(['or',
            ['required' => ProfileField::REQUIRED_NO_SHOW_REG],
            ['required' => ProfileField::REQUIRED_YES_SHOW_REG]
        ])->sort();
    }

    /**
     * @return $this
     */
    public function sort()
    {
        return $this->addOrderBy('position');
    }

    /**
     * @return $this
     */
    public function findbyPk($condition)
    {
        $primaryKey = ProfileField::primaryKey();
        if (isset($primaryKey[0])) {
            $condition = [$primaryKey[0] => $condition];
        }
        return $this->andWhere($condition);
    }
}