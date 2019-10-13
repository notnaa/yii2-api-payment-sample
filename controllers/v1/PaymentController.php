<?php

namespace app\controllers\v1;

use app\exceptions\PaymentException;
use app\models\forms\payment\PaymentChangeForm;
use app\services\PaymentService;
use yii\rest\Controller;

/**
 * Class PaymentController
 *
 * @package app\controllers\v1
 */
class PaymentController extends Controller
{
    /** @var PaymentService */
    private $paymentService;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();
        $this->paymentService = \Yii::$container->get(PaymentService::class);
    }

    /**
     * @return array
     */
    public function actionChange(): array
    {
        $post = \Yii::$app->request->post();

        try {
            $paymentChargeForm = new PaymentChangeForm();

            if (!$paymentChargeForm->load($post, '') || !$paymentChargeForm->validate()) {
                throw new PaymentException(null, PaymentException::INCORRECT_DATA);
            }

            $transactionHistory = $this->paymentService->changeBalance($paymentChargeForm);

            $response = [
                'success' => true,
                'data' => [
                    'transaction_id' => $transactionHistory->id,
                ],
            ];
        } catch (PaymentException $e) {
            $response = [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }

        return $response;
    }
}
