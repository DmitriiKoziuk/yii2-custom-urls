<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2CustomUrls;

use yii\di\Container;
use yii\web\Application as WebApp;
use yii\base\Application as BaseApp;
use yii\console\Application as ConsoleApp;

use DmitriiKoziuk\yii2Base\BaseModule;

use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;

use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

use DmitriiKoziuk\yii2CustomUrls\components\UrlRule;
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
        /** @var BaseApp $app */
        $app = $this->module;
        $this->initLocalProperties($app);
        $this->registerTranslations($app);
        $this->registerClassesToDIContainer($app);
        $this->registerRules($app);
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
            UrlIndexModule::class,
        ];
    }

    private function initLocalProperties(BaseApp $app)
    {
        if (empty($this->backendAppId)) {
            throw new \InvalidArgumentException('Property backendAppId not set.');
        }
        if (empty($this->frontendAppId)) {
            throw new \InvalidArgumentException('Property frontendAppId not set.');
        }
        if ($app instanceof ConsoleApp) {
            $app->controllerMap['migrate']['migrationNamespaces'][] = __NAMESPACE__ . '\migrations';
        }
    }

    private function registerTranslations(BaseApp $app): void
    {
        $app->i18n->translations[self::TRANSLATE] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath'       => '@DmitriiKoziuk/yii2CustomUrls/messages',
        ];
    }

    private function registerClassesToDIContainer(BaseApp $app): void
    {
        $this->diContainer->setSingleton(UrlFilterService::class, function () {
            return new UrlFilterService();
        });

        /** @var UrlFilterService $urlFilterService */
        $urlFilterService = $this->diContainer->get(UrlFilterService::class);
        /** @var UrlIndexServiceInterface $urlIndexService */
        $urlIndexService = $this->diContainer->get(UrlIndexService::class);

        $this->diContainer->set(
            UrlRule::class,
            function () use ($urlIndexService, $urlFilterService) {
                return new UrlRule($urlIndexService, $urlFilterService);
            }
        );
    }

    private function registerRules(BaseApp $app): void
    {
        if ($app instanceof WebApp && $app->id == $this->frontendAppId) {
            $app->getUrlManager()->addRules([
                [
                    'class' => __NAMESPACE__ . '\components\UrlRule',
                ],
            ]);
        }
    }
}