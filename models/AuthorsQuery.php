<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Authors]].
 *
 * @see Authors
 */
class AuthorsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);
        $this->select(["*", "CONCAT(authors.firstname, ' ', authors.lastname) AS author_name"]);
    }

    /**
     * @inheritdoc
     * @return Authors[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Authors[]|array
     */
    public function getAll($db = null)
    {
        return $this->createCommand($db)->queryAll();
    }

    /**
     * @inheritdoc
     * @return Authors|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
