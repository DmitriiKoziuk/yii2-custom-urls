<?php
namespace DmitriiKoziuk\yii2CustomUrls\forms;

use DmitriiKoziuk\yii2Base\forms\Form;
use DmitriiKoziuk\yii2CustomUrls\data\UrlIndexAttributeLabels;

class UrlSearchForm extends Form
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
            [['created_at', 'updated_at'], 'integer'],
            [['url', 'redirect_to_url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return UrlIndexAttributeLabels::getLabels();
    }
}