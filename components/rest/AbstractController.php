<?php

namespace app\components\rest;

use Yii;
use yii\web\Response;

/**
 * Class AbstractController
 *
 * @package app\components\rest
 */
abstract class AbstractController extends \yii\rest\Controller
{
    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_FAIL = 'fail';

    // Коды ошибок
    const ERROR_UNKNOWN = 1; // Произошла неизвестная ошибка.
    const ERROR_TOKEN_NOT_CORRECT = 2; // Токен авторизации не корректен
    const ERROR_ELEMENT_NOT_FOUND = 3; // Эллемент не найден
    const ERROR_METHOD_NOT_FOUND = 4; // Метод не найден
    const ERROR_ACCESS_DENIED = 5; // Доступ запрещён
    const ERROR_PARAMETERS_ERROR = 6; // Один из необходимых параметров был не передан или неверен.
    const ERROR_LOCK = 7; // Заблокированно
    const ERROR_ELEMENT_IS_FOUND = 8; // Эллемент уже существует
    const ERROR_USER_TOKEN_EXPIRED = 9; // Токен пользователя истек

    /**
     * @var string Формат ответа
     */
    protected $responseFormat = Response::FORMAT_JSON;
    /**
     * @var array Данные для ответа
     */
    protected $responseData = [
        'status' => self::RESPONSE_STATUS_FAIL,
        'code' => self::ERROR_TOKEN_NOT_CORRECT,
        'data' => [],
    ];

    /**
     * @return array
     */
    public function behaviors()
    {
        Yii::$app->user->enableSession = false;

        return [];
    }

    /**
     * @param $message
     * @return $this
     */
    protected function addMessage($message)
    {
        if (!isset($this->responseData['message'])) {
            $this->responseData['message'] = [];
        }

        $this->responseData['message'][] = $message;

        return $this;
    }

    /**
     * @param null $statusCode
     * @return array
     */
    protected function fail($statusCode = null)
    {
        if (is_null($statusCode)) {
            $statusCode = 400;
        }

        Yii::$app->response->statusCode = $statusCode;
        $this->responseData['status'] = self::RESPONSE_STATUS_FAIL;
        unset($this->responseData['data']);

        return $this->respond();
    }

    /**
     * @return array
     */
    protected function respond()
    {
        Yii::$app->response->format = $this->responseFormat;

        return $this->responseData;
    }

    /**
     * Возвращает код ошибки по сообщению
     *
     * @param $message
     * @return false|int|string
     */
    public function getErrorCode($message)
    {
        return array_search($this->getErrors(), $message);
    }

    /**
     * Список ошибок
     *
     * @return array
     */
    public function getErrors()
    {
        return [
            self::ERROR_UNKNOWN => 'Unknown error occurred.', // Когда что-то пошло совсем не так
            self::ERROR_TOKEN_NOT_CORRECT => 'Invalid authentication token.', // Токен авторизации не корректен
            self::ERROR_ELEMENT_NOT_FOUND => 'Item not found.', // 404, когда нет элемента
            self::ERROR_METHOD_NOT_FOUND => 'Unknown method passed.', // 404
            self::ERROR_ACCESS_DENIED => 'Access denied.', // Доступ запрещён
            self::ERROR_PARAMETERS_ERROR => 'One of the parameters specified was missing or invalid', // Один из необходимых параметров был не передан или неверен.
            self::ERROR_LOCK => 'Item is block', // Заблокированно
            self::ERROR_ELEMENT_IS_FOUND => 'Item exist.', // уже существует
        ];
    }

    /**
     * Успешный ответ
     */
    protected function success()
    {
        Yii::$app->response->statusCode = 200;
        $this->responseData['status'] = self::RESPONSE_STATUS_SUCCESS;
        unset($this->responseData['code']);

        return $this->respond();
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setDataValue($key, $value)
    {
        $this->responseData['data'] += [$key => $value];
    }

    /**
     * Чтоб сортировка работала
     *
     * @param $key
     * @param $value
     * @param boolean $hard
     */
    protected function setData($key, $value, $hard = false)
    {
        if ($hard) {
            $this->responseData[$key] = $value;
        } else {
            array_push($this->responseData['data'], [$key => $value]);
        }
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addDataValue($key, $value)
    {
        if (!array_key_exists($key, $this->responseData['data'])) {
            $this->responseData['data'][$key] = [];
            $this->responseData['data'][$key][] = $value;
        } else {
            if (is_array($this->responseData['data'][$key])) {
                $this->responseData['data'][$key][] = $value;
            }
        }
    }

    /**
     * @param $value
     */
    protected function addData($value)
    {
        $this->responseData['data'][] = $value;
    }

    /**
     * @param $code
     */
    protected function addCode($code)
    {
        $this->responseData['code'] = $code;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addField($key, $value)
    {
        if (isset($this->responseData[$key])) {
            $this->responseData[$key] = $value;
        } else {
            $this->responseData += [$key => $value];
        }
    }

    /**
     * @param \yii\base\Model $model
     * @param boolean $first
     */
    protected function addMessagesByModel($model, $first = false)
    {
        $errorsAttributes = $model->getErrors();

        foreach ($errorsAttributes as $errorsAttribute) {
            foreach ($errorsAttribute as $error) {
                $this->addMessage($error);
                $this->addCode(self::ERROR_PARAMETERS_ERROR);

                if ($first) {
                    return;
                }
            }
        }
    }
}
