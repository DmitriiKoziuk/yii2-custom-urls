<?php
namespace DmitriiKoziuk\yii2CustomUrls\components;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager;
use yii\web\UrlRuleInterface;

use DmitriiKoziuk\yii2Base\exceptions\StringDoesNotMatchException;

use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\repositories\UrlIndexRepository;
use DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamException;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamValueException;

final class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var UrlIndexRepository
     */
    private $_urlIndexRepository;

    /**
     * @var UrlFilterService
     */
    private $_filterService;

    public function __construct(
        UrlIndexRepository $urlIndexRepository,
        UrlFilterService $filterService,
        array $config = []
    ) {
        parent::__construct($config);
        $this->_urlIndexRepository = $urlIndexRepository;
        $this->_filterService = $filterService;
    }

    /**
     * @todo: parse request with non latin characters.
     * @param UrlManager $manager
     * @param Request $request
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function parseRequest($manager, $request)
    {
        $url = $request->getUrl();
        $url = $this->_cutOutGetParamsFromUrl($url);
        try {
            $this->_filterService->parseUrl($url);
        } catch (
            StringDoesNotMatchException |
            AddingDuplicateParamException |
            AddingDuplicateParamValueException $e
        ) {
            throw new NotFoundHttpException(
                Yii::t(CustomUrlsModule::ID, 'Page not found.')
            );
        }
        if (! $this->_filterService->isParamsInTheAlphabeticalOrder()) {
            throw new NotFoundHttpException(
                Yii::t(CustomUrlsModule::ID, 'Page not found.')
            );
        }
        $url = $this->_cutOutFilterParamsFromUrl($url);
        $urlIndexRecord = $this->_urlIndexRepository->findByUrl($url);
        if (empty($urlIndexRecord)) {
            return false;
        }
        $urlData = new UrlData($urlIndexRecord);
        $route = ! empty($urlData->getModuleName()) ? $urlData->getModuleName() . '/' : '';
        $route .= $urlData->getControllerName() . '/' . $urlData->getActionName();
        return [
            $route,
            [
                'urlData' => $urlData,
                'filterService' => $this->_filterService,
            ]
        ];
    }

    /**
     * Creates a URL according to the given route and parameters.
     * @param UrlManager $manager the URL manager
     * @param string $route the route. It should not have slashes at the beginning or the end.
     * @param array $params the parameters
     * @return string|bool the created URL, or false if this rule cannot be used for creating this URL.
     */
    public function createUrl($manager, $route, $params)
    {
        if ('customUrl' == $route) {
            if (empty($params['url'])) {
                throw new InvalidArgumentException("'url' param must be set.");
            }
            $url = $params['url'];
            if (! empty($params['filterService']) && $params['filterService'] instanceof UrlFilterService) {
                /** @var UrlFilterService $filterService */
                $filterService = $params['filterService'];
                $url .= $filterService->getFilterString();
            }
            return $url;
        }
        return false;
    }

    /**
     * Return url without get params (?param1=value1&param2=value2).
     * @param string $url
     * @return string
     */
    private function _cutOutGetParamsFromUrl(string $url): string
    {
        $isGetParams = mb_strpos($url, '?');
        if (false !== $isGetParams) {
            $url = mb_substr($url, 0, $isGetParams);
        }
        return $url;
    }

    /**
     * Return url without filter params (/filter:param1=value1).
     * @param string $url
     * @return string
     */
    private function _cutOutFilterParamsFromUrl(string $url): string
    {
        $isFilter = mb_strpos($url, $this->_filterService->getFilterMark());
        if (false !== $isFilter) {
            $url = mb_substr($url, 0, $isFilter);
        }
        return $url;
    }
}