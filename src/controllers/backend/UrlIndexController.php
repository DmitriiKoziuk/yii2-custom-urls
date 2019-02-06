<?php

namespace DmitriiKoziuk\yii2CustomUrls\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2CustomUrls\data\UrlIndexSearchParams;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\services\UrlIndexSearchService;
use DmitriiKoziuk\yii2CustomUrls\services\UrlService;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;

/**
 * UrlController implements the CRUD actions for UrlIndex model.
 */
class UrlIndexController extends Controller
{
    protected $_urlIndexService;

    public function __construct(
        string $id,
        Module $module,
        UrlService $urlIndexService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_urlIndexService = $urlIndexService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UrlIndex models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchParams = new UrlIndexSearchParams();
        $searchParams->load(Yii::$app->request->queryParams);
        $searchService = new UrlIndexSearchService($searchParams);
        $dataProvider = $searchService->getActiveDataProvider();

        return $this->render('index', [
            'searchModel' => $searchParams,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UrlIndex model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $urlIndexData = $this->_findData($id);
        return $this->render('view', [
            'model' => $urlIndexData,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    public function actionCreate()
    {
        $urlCreateForm = new UrlCreateForm();

        if (
            Yii::$app->request->isPost &&
            $urlCreateForm->load(Yii::$app->request->post()) &&
            $urlCreateForm->validate()
        ) {
            $urlData = $this->_urlIndexService->addUrlToIndex($urlCreateForm);

            if ($urlData->hasErrors()) {
                Yii::$app->session->setFlash('error', $urlData->getErrorsAsString());
            } else {
                return $this->redirect(['index', 'id' => $urlData->url]);
            }
        }

        return $this->render('create', [
            'urlIndexInputData' => $urlCreateForm,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    public function actionUpdate($id)
    {
        $urlUpdateForm = new UrlUpdateForm();
        if (
            Yii::$app->request->isPost &&
            $urlUpdateForm->load(Yii::$app->request->post()) &&
            $urlUpdateForm->validate()
        ) {
            $urlData = $this->_urlIndexService->updateUrlInIndex($urlUpdateForm);

            if ($urlData->hasErrors()) {
                Yii::$app->session->setFlash('error', $urlData->getErrorsAsString());
            }
        }

        return $this->render('update', [
            'urlIndexInputData' => $urlData,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $urlData = new UrlData();
        $urlData->url = $id;

        if ($urlData->validate()) {
            $urlData = $this->_urlIndexService
                ->deleteUrlFromIndex($urlData);
        }

        if ($urlData->hasErrors()) {
            Yii::$app->session->setFlash('error', $urlData->getErrorsAsString());
        }

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     * @return UrlData
     * @throws NotFoundHttpException
     */
    private function _findData($id): UrlData
    {
        if (($model = $this->_urlIndexService->getUrlData($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
