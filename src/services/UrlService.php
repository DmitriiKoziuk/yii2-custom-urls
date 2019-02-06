<?php
namespace DmitriiKoziuk\yii2CustomUrls\services;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntityDeleteException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlCreateForm;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2CustomUrls\data\UrlData;
use DmitriiKoziuk\yii2CustomUrls\repositories\UrlIndexRepository;

/**
 * Class UrlService
 * @package DmitriiKoziuk\yii2CustomUrls\services
 */
final class UrlService extends EntityActionService
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
     * @return UrlData
     * @throws DataNotValidException
     * @throws EntityNotValidException
     * @throws EntitySaveException
     */
    public function addUrlToIndex(UrlCreateForm $createForm): UrlData
    {
        if (! $createForm->validate()) {
            $e = new DataNotValidException('Data for creation url not valid');
            $e->addErrors($createForm->getErrors());
            throw $e;
        }
        $urlIndex = new UrlIndexRecord();
        $urlIndex->setAttributes($createForm->getAttributes());
        $this->_urlIndexRepository->save($urlIndex);
        $urlData = new UrlData();
        $urlData->setAttributes($urlIndex->getAttributes());
        return $urlData;
    }

    /**
     * @param UrlUpdateForm $inputData
     * @return UrlData
     * @throws DataNotValidException
     * @throws EntityNotFoundException
     * @throws EntitySaveException
     */
    public function updateUrlInIndex(UrlUpdateForm $inputData): UrlData
    {
        if (! $inputData->validate()) {
            $e = new DataNotValidException('Data for update url not valid');
            $e->addErrors($inputData->getErrors());
            throw $e;
        }
        $urlIndexEntity = $this->_urlIndexRepository->findEntity(
            $inputData->controller_name,
            $inputData->action_name,
            $inputData->entity_id,
            $inputData->module_name
        );
        if (empty($urlIndexEntity)) {
            throw new EntityNotFoundException('Entity not found.');
        }
        $urlIndexEntity = $this->_updateUrlIndex($urlIndexEntity, $inputData->url);
        $urlData = new UrlData();
        $urlData->setAttributes($urlIndexEntity->getAttributes());
        return $urlData;
    }

    /**
     * @param string $url
     * @return UrlData
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteUrlFromIndex(string $url): UrlData
    {
        $this->beginTransaction();
        $inputData = new UrlData(['url' => $url]);
        try {
            $urlIndexRecord = $this->_urlIndexRepository->findByUrl($url);
            if (empty($urlIndexRecord)) {
                throw new EntityNotFoundException('Record not found');
            }
            $this->_deleteUrlIndex($urlIndexRecord);
            $this->commitTransaction();
        } catch (EntityNotFoundException $e) {
            $inputData->addError('Record not found', $e->getMessage());
        } catch (EntityDeleteException $e) {
            $inputData->addErrors($e->getErrors());
        }
        $this->rollbackTransaction();
        return $inputData;
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
            $urlData = new UrlData();
            $urlData->setAttributes($urlIndexRecord->getAttributes());
            return $urlData;
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