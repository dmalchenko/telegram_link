<?php

namespace app\controllers;


use app\models\Token;
use app\models\Wall;
use VK\Exceptions\VKClientException;
use VK\Exceptions\VKOAuthException;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class VkController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = 'vk';
        return parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->getId() != 101) {
            return $this->redirect(['site/login']);
        }

        $oauth = new VKOAuth();
        $client_id = Yii::$app->params['vk_id'];
        $redirect_uri = Yii::$app->params['vk_redirect_uri'];
        $display = VKOAuthDisplay::PAGE;
        $scope = array(VKOAuthUserScope::WALL, VKOAuthUserScope::GROUPS);

        $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope);

        $token = Token::findOne(['id' => 1]);
        if (!$token) {
            $token = new Token();
        }

        $wall = Wall::find()->asArray()->all();
        return $this->render('index', [
            'wall' => $wall,
            'vk_url' => $browser_url,
            'token' => $token
        ]);
    }

    /**
     * @throws VKClientException
     * @throws VKOAuthException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSetCode()
    {
        $code = Yii::$app->request->get('code');
        $oauth = new VKOAuth();
        $client_id = Yii::$app->params['vk_id'];
        $client_secret = Yii::$app->params['vk_secret'];
        $redirect_uri = Yii::$app->params['vk_redirect_uri'];

        $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
        $access_token = $response['access_token'];
        $expires_in = $response['expires_in'];

        if ($access_token && $expires_in) {
            $token = Token::findOne(['id' => 1]);
            if ($token) {
                $token->access_token = $access_token;
                $token->expires_in = $expires_in + time();
                $token->update();
            } else {
                $token = new Token();
                $token->access_token = $access_token;
                $token->expires_in = $expires_in + time();
                $token->save();
            }
        }

        return $this->redirect('index');
    }

}