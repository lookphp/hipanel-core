<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class AjaxModal.
 *
 * @property $actionUrl string URL
 * @property $modalFormId string ID for modal form
 * @property $errorText string The error text for PNotify generation. On set will be processed through [[Yii::t()]]
 * @property $successText string The success text for PNotify generation. On set will be processed through [[Yii::t()]]
 * @property $loadingText string The text that will be displayed on submit button after press. On set will be processed through [[Yii::t()]]
 * @property $loadingHtml string The HTML to be appended to the modal body
 */
class AjaxModal extends \yii\bootstrap\Modal
{
    /**
     * @var bool
     */
    public $bulkPage = false;

    /**
     * @var bool
     */
    public $usePost = false;

    /**
     * @var string
     */
    public $scenario;

    /**
     * @var string URL
     */
    protected $_actionUrl;

    /**
     * @var string ID for modal form
     */
    protected $_modalFormId;

    /**
     * @var string The error text for PNotify generation.
     * On set will be processed through [[Yii::t()]]
     */
    protected $_errorText;

    /**
     * @var string The success text for PNotify generation.
     * On set will be processed through [[Yii::t()]]
     */
    protected $_successText;

    /**
     * @var string The text that will be displayed on submit button after press.
     * On set will be processed through [[Yii::t()]]
     */
    protected $_loadingText;

    /**
     * @var boolean whether to handle the submit of the form in the modal with special AJAX script
     * @see registerClientScript
     */
    public $handleSubmit = true;

    public function getActionUrl()
    {
        return $this->_actionUrl ? Url::to($this->_actionUrl) : Url::to($this->scenario);
    }

    public function setActionUrl($url)
    {
        $this->_actionUrl = $url;
    }

    public function getModalFormId()
    {
        return $this->_modalFormId ?: $this->scenario . '-form';
    }

    public function setModalFormId($id)
    {
        $this->_modalFormId = $id;
    }

    public function getErrorText()
    {
        return $this->_errorText ?: Yii::t('app', 'An error occurred. Try again later.');
    }

    public function setErrorText($text)
    {
        $this->_errorText = Yii::t('app', $text);
    }

    public function getSuccessText()
    {
        return $this->_successText ?: Yii::t('app', 'Settings saved');
    }

    public function setSuccessText($text)
    {
        $this->_successText = Yii::t('app', $text);
    }

    public function getLoadingText()
    {
        return $this->_loadingText ?: Yii::t('app', 'loading') . '...';
    }

    public function setLoadingText($text)
    {
        $this->_loadingText = Yii::t('app', $text);
    }

    public function init()
    {
        if (!$this->scenario) {
            throw new InvalidConfigException("Attribute 'scenario' is required");
        }

        Html::addCssClass($this->options['class'], 'text-left');

        $this->initAdditionalOptions();
        if ($this->handleSubmit !== false) {
            $this->registerClientScript();
        }
        parent::init();
    }

    protected function initAdditionalOptions()
    {
        $quotedHtml = Json::htmlEncode($this->loadingHtml);
        if (!isset($this->clientEvents['show.bs.modal'])) {
            if ($this->bulkPage) {
                if ($this->usePost) {
                    $this->clientEvents['show.bs.modal'] = new JsExpression("function() {
                        var selection = jQuery('div[role=\"grid\"]').yiiGridView('getSelectedRows');
                        $.post('{$this->actionUrl}', {selection: selection}).done(function (data) {
                            $('#{$this->id} .modal-body').html(data);
                        });
                    }");
                } else {
                    $this->clientEvents['show.bs.modal'] = new JsExpression("function() {
                        var selection = jQuery('div[role=\"grid\"]').yiiGridView('getSelectedRows');
                        $.get('{$this->actionUrl}', {selection: selection}).done(function (data) {
                            $('#{$this->id} .modal-body').html(data);
                        });
                    }");
                }
            } else {
                if ($this->usePost) {
                    $this->clientEvents['show.bs.modal'] = new JsExpression("function() {
                        $.post('{$this->actionUrl}').done(function (data) {
                            $('#{$this->id} .modal-body').html(data);
                        });
                    }");
                } else {
                    $this->clientEvents['show.bs.modal'] = new JsExpression("function() {
                        $.get('{$this->actionUrl}').done(function (data) {
                            $('#{$this->id} .modal-body').html(data);
                        });
                    }");
                }
            }
        }
        if (!isset($this->clientEvents['hidden.bs.modal'])) {
            $this->clientEvents['hidden.bs.modal'] = new JsExpression("function() {
                jQuery('#{$this->id} .modal-body').html({$quotedHtml});
            }");
        }
    }

    protected function registerClientScript()
    {
        $url = is_string($this->handleSubmit) ? $this->handleSubmit : $this->actionUrl;
        Yii::$app->view->registerJs(<<<JS
            jQuery(document).on('submit', '#{$this->modalFormId}', function(event) {
                event.preventDefault();
                var form = jQuery(this);
                var btn = jQuery('form[data-pjax] button').button('{$this->loadingText}');
                jQuery.ajax({
                    url: '$url',
                    type: 'POST',
                    timeout: 0,
                    data: form.serialize(),
                    error: function() {
                        new PNotify({
                            text: "{$this->errorText}",
                            type: 'error',
                            buttons: {
                                sticker: false
                            },
                            icon: false
                        });
                    },
                    success: function() {
                        jQuery('#$this->id').modal('hide');
                        new PNotify({
                            text: "{$this->successText}",
                            type: 'info',
                            buttons: {
                                sticker: false
                            },
                            icon: false
                        });
                        btn.button('reset');
                    }
                });
            });
JS
        );
    }

    public function getLoadingHtml()
    {
        return <<<HTML
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                <span class="sr-only">{$this->loadingText}</span>
            </div>
        </div>
HTML;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderBodyBegin()
    {
        return Html::beginTag('div', ['class' => 'modal-body']) . $this->loadingHtml;
    }

    /**
     * {@inheritdoc}
     */
    protected function registerClientEvents()
    {
        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                if ($handler instanceof \Closure) {
                    $this->clientEvents[$event] = call_user_func($handler, $this);
                }
            }
        }

        parent::registerClientEvents();
    }
}
