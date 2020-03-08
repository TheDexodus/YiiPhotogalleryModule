<?php

namespace app\modules\page\controllers;

use app\modules\page\models\Category;
use Throwable;
use Yii;
use app\modules\page\models\Image;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AdminImageController implements the CRUD actions for Image model.
 */
class AdminImageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->getIdentity(true)->username === 'admin' ;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Image models.
     *
     * @param int|null $categoryId
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($id = null)
    {
        if (null === $id) {
            throw new NotFoundHttpException();
        }

        if (!Category::findOne(['id' => $id]) instanceof Category) {
            throw new NotFoundHttpException('Category not founded');
        }

        $dataProvider = new ActiveDataProvider(
            [
                'query' => Image::find()->where(['category_id' => $id]),
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Image model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Image model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionCreate()
    {
        $model = new Image(['scenario' => Image::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post())) {
            $model->date = date('Y-m-d');

            if (!isset(Yii::$app->request->post()['Image']['category_id']) || !($category = Category::findOne(
                        ['id' => Yii::$app->request->post()['Image']['category_id']]
                    )) instanceof Category) {
                throw new NotFoundHttpException('Category not founded');
            }
            $model->category_id = $category->id;

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile !== null) {
                $model->extension = $model->imageFile->extension;

                if ($model->save(false) && $model->upload()) {
                    $model->image = $model->id.'.'.$model->imageFile->extension;
                    $model->save(false);

                    $model->category->calculateCount();
                    $model->category->save();

                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $model->delete();
            }
        }

        return $this->render(
            'create',
            [
                'model'      => $model,
                'categories' => Category::getTitleList(),
            ]
        );
    }

    /**
     * Updates an existing Image model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Image::SCENARIO_UPDATE);
        $lastCategory = $model->category;

        if ($model->load(Yii::$app->request->post())) {
            //$model->date = date('Y-m-d');

            if (!isset(Yii::$app->request->post()['Image']['category_id']) || !($category = Category::findOne(
                        ['id' => Yii::$app->request->post()['Image']['category_id']]
                    )) instanceof Category) {
                throw new NotFoundHttpException('Category not founded');
            }
            $model->category_id = $category->id;

            if ($model->save()) {
                $lastCategory->calculateCount();
                $lastCategory->save();
                $category->calculateCount();
                $category->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render(
            'update',
            [
                'model'      => $model,
                'categories' => Category::getTitleList(),
            ]
        );
    }

    /**
     * Deletes an existing Image model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $categoryId = $this->findModel($id)->category_id;
        $this->findModel($id)->fullDelete();

        return $this->redirect(['/photo/admin/category/view?id='.$categoryId]);
    }

    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
