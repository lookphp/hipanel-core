<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\assets;

use yii\web\AssetBundle;

class ElementQueryAsset extends AssetBundle
{
    public $sourcePath = '@bower/elementquery';

    public $js = [
        'elementQuery.min.js',
    ];
}
