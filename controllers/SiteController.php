<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\TelegramUrl;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['site/admin']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->getId() == 101) {
                return $this->redirect(['/vk/admin']);
            }
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @param $item
     * @return string
     */
    public function actionJoinchat($item)
    {
        $this->layout = 'link';
        return $this->render('generate_link', [
            'item' => $item
        ]);
    }

    /**
     * @param $item
     * @return string
     */
    public function actionResolve()
    {
        $this->layout = 'link';
        return $this->render('generate_link', [
//            'item' => $item
        ]);
    }

    /**
     * @param $item
     * @return string
     */
    public function actionAdmin()
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->getId() != 100) {
            return $this->redirect(['site/login']);
        }

        $model = new TelegramUrl();
        $model->load(Yii::$app->request->post());
        $link = $model->generateLink();

        return $this->render('admin', [
            'model' => $model,
        ]);
    }
}
