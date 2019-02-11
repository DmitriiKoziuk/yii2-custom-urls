<?php
namespace DmitriiKoziuk\yii2CustomUrls\forms;

use DmitriiKoziuk\yii2Base\forms\Form;

class UrlUpdateForm extends Form
{
    public $url;
    public $redirect_to_url;
    public $module_name;
    public $controller_name;
    public $action_name;
    public $entity_id;

    public function rules()
    {
        return [
            [['url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['url', 'redirect_to_url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
        ];
    }
}