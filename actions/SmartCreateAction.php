<?php

namespace hipanel\actions;

use Yii;

/**
 * Class SmartCreateAction
 */
class SmartCreateAction extends SwitchAction
{
    public function init()
    {
        parent::init();
        $this->setItems([
            'GET' => [
                'class'  => 'hipanel\actions\RenderAction',
                'view'   => 'create',
                'params' => [
                    'models' => function ($action) {
                        return [$action->controller->newModel(['scenario' => $action->scenario])];
                    },
                ],
            ],
            'POST validate' => [
                'class'  => 'hipanel\actions\ValidateAction',
            ],
            'POST' => [
                'save'    => true,
                'success' => [
                    'class' => 'hipanel\actions\RedirectAction',
                    'url'   => function ($action) {
                        return count($action->collection->models)>1
                            ? $action->controller->getSearchUrl(['ids' => $action->collection->ids])
                            : $action->controller->getActionUrl('view', ['id' => $action->model->id])
                        ;
                    }
                ],
                'error'   => [
                    'class'  => 'hipanel\actions\RenderAction',
                    'view'   => 'create',
                    'params' => [
                        'models' => function ($action) {
                            return $action->collection->models;
                        },
                    ],
                ],
            ],
        ]);
    }
}