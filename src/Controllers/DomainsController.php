<?php

namespace App\Controllers;

use App\Controllers\Dtos\DomainDto;
use App\Controllers\Params\GetDomainPricesParams;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class DomainsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    /**
     * Get domain with prices
     *
     * @return DomainDto[]
     * @throws BadRequestHttpException
     */
    public function actionCheck(): array
    {
        $params = new GetDomainPricesParams();
        if (!$params->load(Yii::$app->request->get(), '') || !$params->validate()) {
            throw new BadRequestHttpException();
        }

        $command = Yii::$app->db->createCommand('SELECT * FROM tld');
        $tlds = $command->queryAll();

        /** @var DomainDto[] $dtos */
        $dtos = [];

        foreach ($tlds as $tld) {
            $domain = sprintf('%s.%s', $params->search, $tld['tld']);
            $domainId = $command->setSql('SELECT id FROM domain WHERE domain = :domain')->bindValue('domain', $domain)->queryScalar();
            $dtos[] = new DomainDto($tld['tld'], $domain, $tld['price'], empty($domainId));
        }

        // todo найти список tld из таблицы
        // todo создать список доменов
        // todo проверить домены на корректность имени
        // todo проверить наличие домена в таблице domain
        // todo создать список dto с ценами для списка доменов

        return $dtos;
    }
}
