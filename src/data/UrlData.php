<?php
namespace DmitriiKoziuk\yii2CustomUrls\data;

use DmitriiKoziuk\yii2Base\data\Data;

class UrlData extends Data
{
    public $url;
    public $redirect_to_url;
    public $module_name;
    public $controller_name;
    public $action_name;
    public $entity_id;
    public $created_at;
    public $updated_at;

    public function rules()
    {
        return [
            [['url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['url', 'redirect_to_url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return UrlIndexAttributeLabels::getLabels();
    }
}