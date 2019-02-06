<?php
namespace DmitriiKoziuk\yii2CustomUrls;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleService;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2ConfigManager\ConfigManager as ConfigModule;

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
        $app->setModule(CustomUrls::ID, [
            'class' => CustomUrls::class,
            'diContainer' => Yii::$container,
            'backendAppId' => $configService->getValue(
                ConfigModule::GENERAL_CONFIG_NAME,
                'backendAppId'
            ),
            'frontendAppId' => $configService->getValue(
                ConfigModule::GENERAL_CONFIG_NAME,
                'frontendAppId'
            ),
        ]);
        /** @var CustomUrls $module */
        $module = $app->getModule(CustomUrls::ID);
        /** @var ModuleService $moduleService */
        $moduleService = Yii::$container->get(ModuleService::class);
        $moduleService->registerModule($module);
    }
}