<?php

namespace app\controllers;


use app\models\Group;
use app\models\Token;
use app\models\VkFilterClass;
use app\models\Wall;
use DateTime;
use VK\Exceptions\VKClientException;
use VK\Exceptions\VKOAuthException;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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

        $url_template = 'https://vk.com/ohmsk?w=wall-77253035_%s';

        $vkFilter = new VkFilterClass();
        $vkFilter->load(Yii::$app->request->post());

        $query = Wall::find()->orderBy(['likes_group' => SORT_DESC]);

        if ($vkFilter->begin_date && $vkFilter->end_date) {
            $format = "m/d/Y";
            $begin_date_obj = DateTime::createFromFormat($format, $vkFilter->begin_date);
            $begin_date = $begin_date_obj->format('U');
            $begin_date = strtotime('midnight', $begin_date);

            $end_date_obj = DateTime::createFromFormat($format, $vkFilter->end_date);
            $end_date = $end_date_obj->format('U');
            $end_date = strtotime('+1 day midnight', $end_date);
            $query->andFilterWhere(['and', ['>', 'created_at', $begin_date], ['<', 'created_at', $end_date]]);
        }

        $wall = $query->asArray()->all();

        return $this->render('index', [
            'wall' => $wall,
            'url_template' => $url_template,
            'vk_filter' => $vkFilter
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

        return $this->redirect('admin');
    }

    public function actionAdmin()
    {
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

        $access_token = $token->access_token;

        $group = Group::findOne(1) ?: new Group();
        if ($group->load(Yii::$app->request->post())) {
            $group->statusOff();
            $group_name = preg_replace('/https:\/\/vk.com\//', '', $group->link);
            $g = json_decode(file_get_contents("https://api.vk.com/method/groups.getById?group_id=$group_name&v=5.74&access_token=$access_token"), true);
            $group->group_id = intval(ArrayHelper::getValue($g, 'response.0.id'));
            $group->save();
        }

        return $this->render('admin', [
            'vk_url' => $browser_url,
            'token' => $token,
            'group' => $group,
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRunParser()
    {
        $group = Group::findOne(1) ?: new Group();
        $group->statusRun();
        $group->update();
        return $this->redirect('admin');
    }

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionOffParser()
    {
        $group = Group::findOne(1) ?: new Group();
        $group->statusOff();
        $group->update();

        return $this->redirect('admin');
    }

}