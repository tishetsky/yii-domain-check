<?php

namespace App\Controllers\Params;

use yii\base\Model;

class GetDomainPricesParams extends Model
{
    /** @var string */
    public $search;

    public function rules()
    {
        return [
            ['search', 'match', 'pattern' => '#^[a-z][0-9a-z-]+$#'],
            ['search', 'required']
        ];
    }

}
