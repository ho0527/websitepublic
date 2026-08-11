<?php
/**
 * Yii 應用程式設定檔。
 * 使用 path 格式網址（透過 PATH_INFO），例如 index.php/line/create
 */
$webRoot = dirname(dirname(__DIR__)); // moduleE 目錄

return array(
    'basePath'   => dirname(__DIR__),
    'name'       => 'LVB Leipziger Verkehrsbetriebe',
    'defaultController' => 'site',
    'charset'    => 'UTF-8',
    'language'   => 'en',

    // 自動載入 models 與 components 目錄下的類別
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    'components' => array(

        // 資料庫：題目提供的 db_lvb.sql 匯入為 ws2013_lvb
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=ws2013_lvb',
            'username'         => 'root',
            'password'         => '',
            'charset'          => 'utf8',
            'emulatePrepare'   => true,
            'enableProfiling'  => false,
        ),

        // 工作階段：關閉瀏覽器後一小時內仍可維持登入
        'session' => array(
            'sessionName'  => 'LVBSESSID',
            'timeout'      => 3600,
            'cookieParams' => array(
                'lifetime' => 3600,
                'httpOnly' => true,
            ),
        ),

        // 登入狀態
        'user' => array(
            'allowAutoLogin' => false,
            'loginUrl'       => array('site/login'),
        ),

        // 乾淨網址：使用 PATH_INFO，不需要修改 nginx 設定
        'urlManager' => array(
            'urlFormat'      => 'path',
            'showScriptName' => true,
            'rules'          => array(
                '<controller:\w+>/<id:\d+>'                  => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'     => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>'              => '<controller>/<action>',
            ),
        ),

        // Yii 內建資源（jQuery 等）發佈目錄，與本專案自有 assets 分開
        'assetManager' => array(
            'basePath' => $webRoot . '/protected/runtime/published',
            'baseUrl'  => 'protected/runtime/published',
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),

        'log' => array(
            'class'  => 'CLogRouter',
            'routes' => array(
                array('class' => 'CFileLogRoute', 'levels' => 'error, warning'),
            ),
        ),
    ),

    // 全站共用參數
    'params' => array(
        'companyName'      => 'LVB Leipziger Verkehrsbetriebe',
        // 車輛／路線／司機共用的車種清單
        'vehicleTypes'     => array(
            'Tram'        => 'Tram',
            'Autobus'     => 'Autobus',
            'Nightliner'  => 'Nightliner',
            'Regionalbus' => 'Regional Bus',
        ),
        'maxStationsPerLine' => 7,   // 每條中間路線固定 7 個站點
        'maxVehiclesPerLine' => 10,  // 每條中間路線最多 10 台車
        'uploadMapPath'      => $webRoot . '/uploads/maps',
        'uploadAvatarPath'   => $webRoot . '/uploads/avatars',
        'uploadMapUrl'       => 'uploads/maps',
        'uploadAvatarUrl'    => 'uploads/avatars',
        'xsdFile'            => $webRoot . '/material/Data/lvb_system.xsd',
    ),
);
