<?php
$currentUrl = Yii::$app->request->pathInfo;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/avatar5.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>
                    <?php if (!Yii::$app->user->isGuest) : ?>
                        <?= Yii::$app->user->identity['full_name']; ?>
                    <?php endif; ?>
                </p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">-->
<!--            <div class="input-group">-->
<!--                <input type="text" name="q" class="form-control" placeholder="Search..."/>-->
<!--              <span class="input-group-btn">-->
<!--                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--            </div>-->
<!--        </form>-->
        <!-- /.search form -->

        <?php

        $menu = [
          'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
          'activateParents'=>true,
          'items' => [
            ['label' => 'Menu LCAPP', 'options' => ['class' => 'header']],
            //['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
            ['label' => 'Home', 'icon' => 'home fa-fw', 'url' => ['/']],
          ],
        ];

        if(Yii::$app->user->identity['role'] === 'admin' || Yii::$app->user->identity['role'] === 'event_processor') {
            $menu['items'][] =   [
                'label' => 'Events',
                'icon' => 'sitemap fa-fw',
                'url' => '#',
                'items' => [
                    ['label' => 'All Events', 'icon' => 'dashboard fa-fw', 'url' => ['/events/'],
                        'active' => strpos($currentUrl, 'events') !== false],
                    ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/events/create'],],
                ],
            ];
        }

        if(Yii::$app->user->identity['role'] === 'admin') {
          $menu['items'][] =   [
            'label' => 'Teacher',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Teachers', 'icon' => 'dashboard fa-fw', 'url' => ['/teacher/'],
                'active' => strpos($currentUrl, 'teacher') !== false && strpos($currentUrl, 'teacher-outcome') === false,],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/teacher/create'],],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'Teacher Outcome',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Outcomes', 'icon' => 'dashboard fa-fw', 'url' => ['/teacher-outcome/'],
                'active' => strpos($currentUrl, 'teacher-outcome') !== false,],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/teacher-outcome/create'],],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'Partners',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Partner', 'icon' => 'dashboard fa-fw', 'url' => ['/partner/'],
                'active' => strpos($currentUrl, 'partner') !== false && strpos($currentUrl, 'partner-rule') === false && strpos($currentUrl, 'partner-rule-action') === false],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/partner/create'],],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'Partner Rule',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Rules', 'icon' => 'dashboard fa-fw', 'url' => ['/partner-rule/'],
                'active' => strpos($currentUrl, 'partner-rule') !== false && strpos($currentUrl, 'partner-rule-action') === false],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/partner-rule/create'],],
            ],
          ];
          
          $menu['items'][] =   [
            'label' => 'Partner Rule Action',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Actions', 'icon' => 'dashboard fa-fw', 'url' => ['/partner-rule-action/'],
                'active' => strpos($currentUrl, 'partner-rule-action') !== false],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/partner-rule-action/create'],],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'HashTags',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All HashTags', 'icon' => 'dashboard fa-fw', 'url' => ['/hash-tag/'],
                'active' => strpos($currentUrl, 'hash-tag') !== false,],
              ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/hash-tag/create'],],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'Lovestar',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Lovestars', 'icon' => 'dashboard fa-fw', 'url' => ['/lovestar/'],
                'active' => strpos($currentUrl, 'lovestar') !== false && strpos($currentUrl, 'teaching-transaction') === false],
            ],
          ];
	
          $menu['items'][] =   [
            'label' => 'Teaching Transaction',
            'icon' => 'sitemap fa-fw',
            'url' => '#',
            'items' => [
              ['label' => 'All Transactions', 'icon' => 'dashboard fa-fw', 'url' => ['/teaching-transaction/'],
                'active' => strpos($currentUrl, 'teaching-transaction') !== false,],
            ],
          ];

            $menu['items'][] =   [
                'label' => 'Invitation Codes',
                'icon' => 'sitemap fa-fw',
                'url' => '#',
                'items' => [
                    ['label' => 'Invitation Codes', 'icon' => 'dashboard fa-fw', 'url' => ['/invitation-codes/'],
                        'active' => strpos($currentUrl, 'invitation-codes') !== false,],
                    ['label' => 'Add New', 'icon' => 'pencil fa-fw', 'url' => ['/invitation-codes/create'],],
                    ['label' => 'View Logs', 'icon' => 'dashboard fa-fw', 'url' => ['/invitation-codes/logs'],],
                ],
            ];
            
          $menu['items'][] = [
            'label' => 'Settings',
            'icon' => 'gears fa-fw',
            'url' => '#',
            'items' => [
              [
                'label' => 'Users',
                'icon' => 'child fa-fw',
                'url' => '#',
                'items' => [
                  ['label' => 'All Users', 'icon' => 'group fa-fw', 'url' => ['settings/user'],
                    'active' => strpos($currentUrl, 'settings/user') !== false],
                  [
                    'label' => 'Create New',
                    'icon' => 'pencil fa-fw',
                    'url' => ['settings/user/create'],
                  ],
                ],
              ],
                [
                    'label' => 'Languages',
                    'icon' => 'language fa-fw',
                    'url' => '#',
                    'items' => [
                        ['label' => 'All Languages', 'icon' => 'dashboard fa-fw', 'url' => ['settings/languages'],
                            'active' => strpos($currentUrl, 'settings/languages') !== false],
                        [
                            'label' => 'Create New',
                            'icon' => 'pencil fa-fw',
                            'url' => ['settings/languages/create'],
                        ],
                    ],
                ],
//              [
//                'label' => 'Facebook',
//                'icon' => 'facebook fa-fw',
//                'url' => ['settings/facebook'],
//                'active' => strpos($currentUrl, 'settings/facebook') !== false,
//              ],
            ],
          ];
        }

        echo dmstr\widgets\Menu::widget($menu) ?>

    </section>

</aside>
