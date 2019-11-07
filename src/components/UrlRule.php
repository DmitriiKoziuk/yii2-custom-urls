<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2CustomUrls\components;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager;
use yii\web\UrlRuleInterface;
use DmitriiKoziuk\yii2Base\exceptions\StringDoesNotMatchException;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2CustomUrls\CustomUrlsModule;
use DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamException;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamValueException;

final class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var UrlIndexService
     */
    private $urlIndexService;

    /**
     * @var UrlFilterService
     */
    private $filterService;

    public function __construct(
        UrlIndexService $urlIndexService,
        UrlFilterService $filterService,
        array $config = []
    ) {
        parent::__construct($config);
        $this->urlIndexService = $urlIndexService;
        $this->filterService = $filterService;
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
        $url = $this->cutOutGetParamsFromUrl($url);
        try {
            $this->filterService->parseUrl($url);
        } catch (
            StringDoesNotMatchException |
            AddingDuplicateParamException |
            AddingDuplicateParamValueException $e
        ) {
            throw new NotFoundHttpException(
                Yii::t(CustomUrlsModule::ID, 'Page not found.')
            );
        }
        if (! $this->filterService->isParamsInTheAlphabeticalOrder()) {
            throw new NotFoundHttpException(
                Yii::t(CustomUrlsModule::ID, 'Page not found.')
            );
        }
        $url = $this->cutOutFilterParamsFromUrl($url);
        $urlData = $this->urlIndexService->getUrlByUrl($url);
        if (empty($urlData)) {
            return false;
        }
        $route = ! empty($urlData->module_name) ? $urlData->module_name . '/' : '';
        $route .= $urlData->controller_name . '/' . $urlData->action_name;
        return [
            $route,
            [
                'urlData' => $urlData,
                'filterParams' => $this->filterService->getParsedParams(),
                'getParams' => $request->getQueryParams(),
            ]
        ];
    }

    /**
     * Creates a URL according to the given route and parameters.
     * @param UrlManager $manager the URL manager
     * @param string $route the route. It should not have slashes at the beginning or the end.
     * @param array $params the parameters
     * @return string|bool the created URL, or false if this rule cannot be used for creating this URL.
     * @throws AddingDuplicateParamValueException
     * @throws StringDoesNotMatchException
     */
    public function createUrl($manager, $route, $params)
    {
        if ('customUrl/create' == $route) {
            if (empty($params['url'])) {
                throw new InvalidArgumentException("'url' param must be set.");
            }
            $url = $params['url'];
            if (! empty($params['filterParams'])) {
                $this->filterService->addParams($params['filterParams'], true);
                $url .= $this->filterService->getFilterString();
            }
            if (! empty($params['getParams'])) {
                $url .= '?' . http_build_query($params['getParams']);
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
    private function cutOutGetParamsFromUrl(string $url): string
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
    private function cutOutFilterParamsFromUrl(string $url): string
    {
        $isFilter = mb_strpos($url, $this->filterService->getFilterMark());
        if (false !== $isFilter) {
            $url = mb_substr($url, 0, $isFilter);
        }
        return $url;
    }
}