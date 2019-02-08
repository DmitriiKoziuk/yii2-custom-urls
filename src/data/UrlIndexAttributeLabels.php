<?php
namespace DmitriiKoziuk\yii2CustomUrls\data;

use Yii;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;

class UrlIndexAttributeLabels
{
    public static function getLabels()
    {
        return [
            'url'             => Yii::t(CustomUrlsModule::ID, 'Url'),
            'redirect_to_url' => Yii::t(CustomUrlsModule::ID, 'Redirect to url'),
            'module_name'     => Yii::t(CustomUrlsModule::ID, 'Module name'),
            'controller_name' => Yii::t(CustomUrlsModule::ID, 'Controller name'),
            'action_name'     => Yii::t(CustomUrlsModule::ID, 'Action name'),
            'entity_id'       => Yii::t(CustomUrlsModule::ID, 'Entity ID'),
            'created_at'      => Yii::t(CustomUrlsModule::ID, 'Created at'),
            'updated_at'      => Yii::t(CustomUrlsModule::ID, 'Updated at'),
        ];
    }
}