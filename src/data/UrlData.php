<?php
namespace DmitriiKoziuk\yii2CustomUrls\data;

use DmitriiKoziuk\yii2Base\data\Data;
use DmitriiKoziuk\yii2CustomUrls\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2CustomUrls\records\UrlIndexRecord;

class UrlData extends Data
{
    /**
     * @var UrlIndexRecord
     */
    private $_urlIndexRecord;

    public function __construct(UrlIndexRecord $urlIndexRecord)
    {
        $this->_urlIndexRecord = $urlIndexRecord;
    }

    public function getUrl(): string
    {
        return $this->_urlIndexRecord->url;
    }

    public function getRedirectToUrl(): ?string
    {
        return $this->_urlIndexRecord->redirect_to_url;
    }

    public function getModuleName(): ?string
    {
        return $this->_urlIndexRecord->module_name;
    }

    public function getControllerName(): string
    {
        return $this->_urlIndexRecord->controller_name;
    }

    public function getActionName(): string
    {
        return $this->_urlIndexRecord->action_name;
    }

    public function getEntityId(): string
    {
        return $this->_urlIndexRecord->entity_id;
    }

    public function getCreatedAt(): int
    {
        return $this->_urlIndexRecord->created_at;
    }

    public function getUpdatedAt(): int
    {
        return $this->_urlIndexRecord->updated_at;
    }

    public function getUpdateForm(): UrlUpdateForm
    {
        $form = new UrlUpdateForm();
        $form->setAttributes($this->_urlIndexRecord->getAttributes());
        return $form;
    }
}