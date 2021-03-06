<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\grid;

use hipanel\helpers\ArrayHelper as AH;
use hipanel\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

class CurrentColumn extends DataColumn
{
    public $uses = [];

    public function init()
    {
        parent::init();
        $request = AH::merge([
            'wrapper'   => 'results',
            'term'      => 'search:term',
        ], $this->uses);
        $requests = [];
        $back_request = [];
        foreach ($request as $k => $v) {
            if ($k === 'term') {
                $requests[] = $v;
            } elseif (is_array($v)) {
                $requests[] = "{$k}:" . json_encode($v);
                $back_request[] = "{$k}:" . json_encode($v);
            } else {
                $requests[] = "$k:'{$v}'";
                $back_request[] = "$k:'{$v}'";
            }
        }
        \Yii::configure($this, [
            'format'                => 'html',
            'filterInputOptions'    => ['id' => 'id'],
            'filter'                => Select2::widget([
                'attribute' => 'id',
                'model'     => $this->grid->filterModel,
                'url'       => Url::toRoute(['list']),
                'settings'  => [
                    'ajax'      => [
                        'data' => new JsExpression('function(term,page) { return {' . implode(', ', $requests) . '}; }'),
                    ],
                    'initSelection'      => new JsExpression('function (elem, callback) {
                        var id=$(elem).val();
                        $.ajax("' . Url::toRoute(['list']) . '?id=" + id, {
                            dataType: "json",
                            data : {' . implode(', ', $back_request) . '}
                        }).done(function(data) {
                            callback(data.results[0]);
                        });
                    }'),
                ],
            ]),
        ]);
    }
}
