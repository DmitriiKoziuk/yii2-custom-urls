<?php
namespace DmitriiKoziuk\yii2CustomUrls;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleService;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function bootstrap($app)
    {
        /** @var ConfigService $configService */
        $configService = Yii::$container->get(ConfigService::class);
        $app->setModule(CustomUrlsModule::ID, [
            'class' => CustomUrlsModule::class,
            'diContainer' => Yii::$container,
            'backendAppId' => $configService->getValue(
                ConfigManagerModule::GENERAL_CONFIG_NAME,
                'backendAppId'
            ),
            'frontendAppId' => $configService->getValue(
                ConfigManagerModule::GENERAL_CONFIG_NAME,
                'frontendAppId'
            ),
        ]);
        /** @var CustomUrlsModule $module */
        $module = $app->getModule(CustomUrlsModule::ID);
        /** @var ModuleService $moduleService */
        $moduleService = Yii::$container->get(ModuleService::class);
        $moduleService->registerModule($module);
    }
}