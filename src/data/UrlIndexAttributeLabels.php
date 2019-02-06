<?php
namespace DmitriiKoziuk\yii2CustomUrls\data;

use Yii;
use DmitriiKoziuk\yii2CustomUrls\CustomUrls;

class UrlIndexAttributeLabels
{
    public static function getLabels()
    {
        return [
            'url'             => Yii::t(CustomUrls::ID, 'Url'),
            'redirect_to_url' => Yii::t(CustomUrls::ID, 'Redirect to url'),
            'module_name'     => Yii::t(CustomUrls::ID, 'Module name'),
            'controller_name' => Yii::t(CustomUrls::ID, 'Controller name'),
            'action_name'     => Yii::t(CustomUrls::ID, 'Action name'),
            'entity_id'       => Yii::t(CustomUrls::ID, 'Entity ID'),
            'created_at'      => Yii::t(CustomUrls::ID, 'Created at'),
            'updated_at'      => Yii::t(CustomUrls::ID, 'Updated at'),
        ];
    }
}