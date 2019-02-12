<?php
namespace DmitriiKoziuk\yii2CustomUrls;

use yii\di\Container;
use yii\base\Application;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2CustomUrls\components\UrlRule;
use DmitriiKoziuk\yii2CustomUrls\services\UrlIndexService;
use DmitriiKoziuk\yii2CustomUrls\repositories\UrlIndexRepository;
use DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService;

final class CustomUrlsModule extends \yii\base\Module implements ModuleInterface
{
    const ID = 'dk-custom-urls';

    /**
     * @var string
     */
    const TRANSLATE = self::ID;

    /**
     * @var Container
     */
    public $diContainer;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $backendAppId;

    /**
     * Overwrite this param if you frontend app id is different from default.
     * @var string
     */
    public $frontendAppId;

    public function init()
    {
        parent::init();
        /** @var Application $app */
        $app = $this->module;
        $this->_initLocalProperties($app);
        $this->_registerTranslations($app);
        $this->_registerClassesToDIContainer($app);
        $this->_registerRules($app);
    }

    public static function getId(): string
    {
        return self::ID;
    }

    public function getBackendMenuItems(): array
    {
        return ['label' => 'Custom urls', 'url' => ['/' . self::ID . '/url/index']];
    }

    public static function requireOtherModulesToBeActive(): array
    {
        return [
            BaseModule::class,
            ConfigManagerModule::class,
        ];
    }

    private function _initLocalProperties(Application $app)
    {
        if (empty($this->backendAppId)) {
            throw new \InvalidArgumentException('Property backendAppId not set.');
        }
        if (empty($this->frontendAppId)) {
            throw new \InvalidArgumentException('Property frontendAppId not set.');
        }
        if ($app instanceof \yii\web\Application && $app->id == $this->backendAppId) {
            $this->controllerNamespace = __NAMESPACE__ . '\controllers\backend';
        }
    }

    private function _registerTranslations(Application $app): void
    {
        $app->i18n->translations[self::TRANSLATE] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath'       => '@DmitriiKoziuk/yii2CustomUrls/messages',
        ];
    }

    private function _registerClassesToDIContainer(Application $app): void
    {
        $this->diContainer->setSingleton(UrlIndexRepository::class, function () {
            return new UrlIndexRepository();
        });

        /** @var UrlIndexRepository $urlIndexRepository */
        $urlIndexRepository = $this->diContainer->get(UrlIndexRepository::class);

        $this->diContainer->set(
            UrlIndexService::class,
            function () use ($urlIndexRepository, $app) {
                return new UrlIndexService($urlIndexRepository, $app->db);
            }
        );
        $this->diContainer->setSingleton(UrlFilterService::class, function () {
            return new UrlFilterService();
        });

        /** @var UrlFilterService $urlFilterService */
        $urlFilterService = $this->diContainer->get(UrlFilterService::class);

        $this->diContainer->set(
            UrlRule::class,
            function () use ($urlIndexRepository, $urlFilterService) {
                return new UrlRule($urlIndexRepository, $urlFilterService);
            }
        );
    }

    private function _registerRules(Application $app): void
    {
        if ($app instanceof \yii\web\Application && $app->id == $this->frontendAppId) {
            $app->getUrlManager()->addRules([
                [
                    'class' => __NAMESPACE__ . '\components\UrlRule',
                ],
            ]);
        }
    }
}