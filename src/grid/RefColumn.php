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

use hipanel\widgets\RefFilter;

class RefColumn extends DataColumn
{
    /** @var string gtype for [[Ref::getList()]] */
    public $gtype;

    /**
     * @var array additional search options for [[Ref::getList()]]
     */
    public $findOptions = [];

    /**
     * @var string Dictionary name for i18n module
     */
    public $i18nDictionary;

    /** {@inheritdoc} */
    protected function renderFilterCellContent()
    {
        return RefFilter::widget([
            'attribute' => $this->getFilterAttribute(),
            'model' => $this->grid->filterModel,
            'i18nDictionary' => $this->i18nDictionary,
            'gtype' => $this->gtype,
            'findOptions' => $this->findOptions,
            'options' => $this->filterInputOptions,
        ]);
    }
}
