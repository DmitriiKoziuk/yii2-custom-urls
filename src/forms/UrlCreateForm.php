<?php
namespace DmitriiKoziuk\yii2CustomUrls\forms;

use yii\base\Model;

class UrlCreateForm extends Model
{
    public $url;
    public $module_name;
    public $controller_name;
    public $action_name;
    public $entity_id;

    public function rules()
    {
        return [
            [['url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
        ];
    }
}