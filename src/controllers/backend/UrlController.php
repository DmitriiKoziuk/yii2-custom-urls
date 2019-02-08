<?php

namespace DmitriiKoziuk\yii2CustomUrls\controllers\backend;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlSearchForm;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlDeleteForm;
use DmitriiKoziuk\yii2CustomUrls\services\UrlSearchService;
use DmitriiKoziuk\yii2CustomUrls\services\UrlIndexService;

/**
 * UrlController implements the CRUD actions for UrlIndex model.
 */
class UrlController extends Controller
{
    protected $_urlIndexService;

    public function __construct(
        string $id,
        Module $module,
        UrlIndexService $urlIndexService,
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
        $searchForm = new UrlSearchForm();
        $searchForm->load(Yii::$app->request->queryParams);
        $searchService = new UrlSearchService($searchForm);
        $dataProvider = $searchService->getActiveDataProvider();

        return $this->render('index', [
            'searchModel' => $searchForm,
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
        $urlData = $this->_findData($id);
        return $this->render('view', [
            'urlData' => $urlData,
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
            $urlCreateForm->load(Yii::$app->request->post())
        ) {
            $urlCreateForm = $this->_urlIndexService->addUrlToIndex($urlCreateForm);
            if ($urlCreateForm->hasErrors()) {
                Yii::$app->session->setFlash('error', $urlCreateForm->getErrorsAsString());
            } else {
                return $this->redirect(['index', 'id' => $urlCreateForm->url]);
            }
        }
        return $this->render('create', [
            'urlCreateForm' => $urlCreateForm,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     */
    public function actionUpdate($id)
    {
        $urlUpdateForm = new UrlUpdateForm();
        if (
            Yii::$app->request->isPost &&
            $urlUpdateForm->load(Yii::$app->request->post())
        ) {
            $urlUpdateForm = $this->_urlIndexService->updateUrlInIndex($urlUpdateForm);
            if ($urlUpdateForm->hasErrors()) {
                Yii::$app->session->setFlash('error', $urlUpdateForm->getErrorsAsString());
            }
        } else {
            $urlUpdateForm = $this->_findData($id)->getUpdateForm();
        }
        return $this->render('update', [
            'urlUpdateForm' => $urlUpdateForm,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $urlDeleteForm = new UrlDeleteForm([
            'url' => $id,
        ]);
        $urlDeleteForm = $this->_urlIndexService
            ->deleteUrlFromIndex($urlDeleteForm);
        if ($urlDeleteForm->hasErrors()) {
            Yii::$app->session->setFlash('error', $urlDeleteForm->getErrorsAsString());
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
