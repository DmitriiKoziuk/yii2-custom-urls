<?php

namespace DmitriiKoziuk\yii2CustomUrls\records;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2CustomUrls\data\UrlIndexAttributeLabels;

/**
 * This is the model class for table "{{%url_indexes}}".
 *
 * @property string $url
 * @property string $redirect_to_url
 * @property string $module_name
 * @property string $controller_name
 * @property string $action_name
 * @property string $entity_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class UrlIndexRecord extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%url_indexes}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['url', 'redirect_to_url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
            [['url'], 'unique'],
            [
                ['controller_name'],
                'unique',
                'targetAttribute' => ['module_name', 'controller_name', 'action_name', 'entity_id']
            ],
            [['module_name', 'redirect_to_url'], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return UrlIndexAttributeLabels::getLabels();
    }
}
