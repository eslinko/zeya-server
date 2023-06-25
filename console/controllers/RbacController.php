<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные

        //Создадим для примера права для доступа к админке
        $dashboard = $auth->createPermission('adminPanel');
        $dashboard->description = 'Админ панель';
        $auth->add($dashboard);

        //Добавляем объект определяющий правила для ролей пользователей, он будет сохранен в файл rules.php
        $rule = new UserRoleRule();
        $auth->add($rule);

        //Добавляем роли
        $editor = $auth->createRole('editor');
        $editor->description = 'Редактор';
        $editor->ruleName = $rule->name;
        $auth->add($editor);

        //Добавляем потомков
        $auth->addChild($editor, $dashboard);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $admin->ruleName = $rule->name;
        $auth->add($admin);
        $auth->addChild($admin, $editor);
    }
}