<?php
namespace DmitriiKoziuk\yii2CustomUrls\services;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntityDeleteException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlDeleteForm;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\repositories\UrlIndexRepository;

/**
 * Class UrlService
 * @package DmitriiKoziuk\yii2CustomUrls\services
 */
final class UrlIndexService extends DBActionService
{
    private $_urlIndexRepository;

    public function __construct(
        UrlIndexRepository $urlIndexRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_urlIndexRepository = $urlIndexRepository;
    }

    /**
     * @param UrlCreateForm $createForm
     * @return UrlCreateForm
     * @throws DataNotValidException
     * @throws EntityNotValidException
     * @throws EntitySaveException
     */
    public function addUrlToIndex(UrlCreateForm $createForm): UrlCreateForm
    {
        if ($createForm->validate()) {
            $urlIndexRecord = new UrlIndexRecord();
            $urlIndexRecord->setAttributes($createForm->getAttributes());
            $this->_urlIndexRepository->save($urlIndexRecord);
        }
        return $createForm;
    }

    /**
     * @param UrlUpdateForm $urlUpdateForm
     * @return UrlUpdateForm
     * @throws DataNotValidException
     * @throws EntitySaveException
     */
    public function updateUrlInIndex(UrlUpdateForm $urlUpdateForm): UrlUpdateForm
    {
        if ($urlUpdateForm->validate()) {
            $urlIndexRecord = $this->_urlIndexRepository->findEntity(
                $urlUpdateForm->controller_name,
                $urlUpdateForm->action_name,
                $urlUpdateForm->entity_id,
                $urlUpdateForm->module_name
            );
            if (empty($urlIndexRecord)) {
                $urlUpdateForm->addError('entity', 'Url for update not found.');
            }
            $this->_updateUrlIndex($urlIndexRecord, $urlUpdateForm->url);
        }
        return $urlUpdateForm;
    }

    /**
     * @param UrlDeleteForm $urlDeleteForm
     * @return UrlDeleteForm
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteUrlFromIndex(UrlDeleteForm $urlDeleteForm): UrlDeleteForm
    {
        $this->beginTransaction();
        try {
            $urlIndexRecord = $this->_urlIndexRepository->findByUrl($urlDeleteForm->url);
            if (empty($urlIndexRecord)) {
                throw new EntityNotFoundException('Record not found');
            }
            $this->_deleteUrlIndex($urlIndexRecord);
            $this->commitTransaction();
        } catch (EntityNotFoundException $e) {
            $urlDeleteForm->addError('Record not found', $e->getMessage());
        } catch (EntityDeleteException $e) {
            $urlDeleteForm->addErrors($e->getErrors());
        }
        $this->rollbackTransaction();
        return $urlDeleteForm;
    }

    /**
     * @param string $url
     * @return UrlData|null
     */
    public function getUrlData(string $url): ?UrlData
    {
        $urlIndexRecord = $this->_urlIndexRepository->findByUrl($url);
        if (empty($urlIndexRecord)) {
            return null;
        } else {
            return new UrlData($urlIndexRecord);
        }
    }

    /**
     * @param UrlIndexRecord $urlIndexRecord
     * @param string $newUrl
     * @return UrlIndexRecord
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntitySaveException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException
     */
    private function _updateUrlIndex(UrlIndexRecord $urlIndexRecord, string $newUrl): UrlIndexRecord
    {
        $urlIndexRecord->url = $newUrl;
        $this->_urlIndexRepository->save($urlIndexRecord);
        return $urlIndexRecord;
    }

    /**
     * @param \DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord $urlIndexRecord
     * @throws \Throwable
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityDeleteException
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteUrlIndex(UrlIndexRecord $urlIndexRecord): void
    {
        $this->_urlIndexRepository->delete($urlIndexRecord);
    }
}