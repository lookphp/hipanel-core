<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\actions;

/**
 * Class SmartCreateAction.
 */
class SmartCreateAction extends SwitchAction
{
    /**
     * @var string View name, used for render of result on GET request
     */
    public $view;

    public function run()
    {
        $this->view = $this->view ?: $this->getScenario();
        return parent::run();
    }

    /** {@inheritdoc} */
    protected function getDefaultRules()
    {
        return array_merge(parent::getDefaultRules(), [
            'GET' => [
                'class'  => RenderAction::class,
                'view'   => $this->view,
                'data'   => $this->data,
                'params' => function ($action) {
                    $model = $action->controller->newModel(['scenario' => $action->scenario]);
                    return [
                        'model'  => $model,
                        'models' => [$model],
                    ];
                },
            ],
            'POST' => [
                'save'    => true,
                'success' => [
                    'class' => RedirectAction::class,
                    'url'   => function ($action) {
                        return count($action->collection->models) > 1
                            ? $action->controller->getSearchUrl(['ids' => $action->collection->ids])
                            : $action->controller->getActionUrl('view', ['id' => $action->model->id]);
                    },
                ],
                'error'   => [
                    'class'  => RenderAction::class,
                    'view'   => $this->view,
                    'data'   => $this->data,
                    'params' => function ($action) {
                        return [
                            'model'  => $action->collection->first,
                            'models' => $action->collection->models,
                        ];
                    },
                ],
            ],
        ]);
    }
}
