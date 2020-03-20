<?php

namespace app\modules\page\controllers;

use app\modules\page\models\Image;
use Throwable;
use Yii;
use app\modules\page\models\Category;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminCategoryController implements the CRUD actions for Category model.
 */
class AdminCategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs'  => [
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
                            return Yii::$app->user->getIdentity(true)->username === 'admin';
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => Category::find(),
                'pagination' => [
                    'pageSize' => 10,
                ],
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
     * Displays a single Category model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $model->getImages(),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );

        return $this->render(
            'view',
            [
                'model'        => $model,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {
            $model->calculateCount();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);

            }
        }

        return $this->render(
            'create',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Updates an existing Category model.
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

        if ($model->load(Yii::$app->request->post())) {
            $model->calculateCount();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);

            }
        }

        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing Category model.
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
        $oldCategory = $this->findModel($id);

        if (!isset(Yii::$app->request->post()['delete-method'])) {
            throw new NotFoundHttpException('Delete method not select');
        }

        $deleteMethod = Yii::$app->request->post()['delete-method'];

        if ($deleteMethod === 'move-images') {
            if (!isset(Yii::$app->request->post()['select-category'])) {
                throw new NotFoundHttpException('Category not found');
            }

            $selectedCategoryId = Yii::$app->request->post()['select-category'];

            if (!($category = Category::findOne(['id' => $selectedCategoryId])) instanceof Category) {
                throw new NotFoundHttpException('Category not found');
            }

            if ($category->id === $oldCategory->id) {
                throw new NotFoundHttpException('Can\'t move images in this category');
            }

            /** @var Image $image */
            foreach ($oldCategory->images as $image) {
                $image->category_id = $category->id;
                $image->save(false);
            }

            $category->calculateCount();
            $category->save();
        } else {
            /** @var Image $image */
            foreach ($oldCategory->images as $image) {
                $image->fullDelete();
            }
        }

        $oldCategory->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
