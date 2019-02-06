<?php
namespace DmitriiKoziuk\yii2CustomUrls\repositories;

use yii\db\Expression;
use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord;

class UrlIndexRepository extends EntityRepository
{
    /**
     * @param string $controllerName
     * @param string $actionName
     * @param string $entityId
     * @param string $moduleName
     * @return UrlIndexRecord|null
     */
    public function findEntity(
        string $controllerName,
        string $actionName,
        string $entityId,
        string $moduleName = null
    ) {
        /** @var UrlIndexRecord|null $urlIndex */
        $urlIndex = UrlIndexRecord::find()->where(
            [
                'module_name'     => new Expression(':mn', [':mn' => $moduleName]),
                'controller_name' => new Expression(':cn', [':cn' => $controllerName]),
                'action_name'     => new Expression(':an', [':an' => $actionName]),
                'entity_id'       => new Expression(':ei', [':ei' => $entityId]),
            ]
        )->one();
        return $urlIndex;
    }

    /**
     * @param string $url
     * @return UrlIndexRecord|null
     */
    public function findByUrl(string $url)
    {
        /** @var UrlIndexRecord|null $urlIndex */
        $urlIndex = UrlIndexRecord::find()->where(
            ['url' => new Expression(':url', [':url' => $url])]
        )->one();
        return $urlIndex;
    }
}