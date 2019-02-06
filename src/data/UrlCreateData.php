<?php
namespace DmitriiKoziuk\yii2CustomUrls\data;

use yii\base\Model;

class UrlCreateData extends Model
{
    public $url;
    public $module_name;
    public $controller_name;
    public $action_name;
    public $entity_id;

    public function __construct(
        string $url,
        string $module_name,
        string $controller_name,
        string $action_name,
        string $entity_id,
        array $config = []
    ) {
        parent::__construct($config);
        $this->url = $url;
        $this->module_name = $module_name;
        $this->controller_name = $controller_name;
        $this->action_name = $action_name;
        $this->entity_id = $entity_id;
    }

    public function rules()
    {
        return [
            [['url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['url'], 'string', 'max' => 500],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
        ];
    }
}