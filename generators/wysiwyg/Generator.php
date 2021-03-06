<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace myzero1\gii\generators\wysiwyg;

use yii\gii\CodeFile;
use yii\helpers\Html;
use Yii;
use yii\helpers\StringHelper;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property string $controllerNamespace The controller namespace of the module. This property is read-only.
 * @property boolean $modulePath The directory that contains the module class. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator
{
    const ADMINLTE = 1;

    public $ns;
    public $themingID = self::ADMINLTE;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // other coding
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Redactor WYSIWYG Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Extension Redactor WYSIWYG for Yii2 framework.';
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        $this->addRequiresToComposer();

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;

        $code = <<<EOD
<?php
    ......
    'modules' => [
        ......
        'redactor' => [
            'class' => 'yii\\redactor\RedactorModule',
            // 'uploadDir' => '@webroot/path/to/uploadfolder',
            // 'uploadUrl' => '@web/path/to/uploadfolder',
            // 'imageAllowExtensions'=>['jpg','png','gif']
        ],
        ......
    ],
    ......
EOD;

$output = $output . '<pre>' . highlight_string($code, true) . '</pre>';

$output = $output . '<p> Add upload widget like following: </p>';

        $code2 = <<<EOD
<?php
    ......
    <?= \yii\\redactor\widgets\Redactor::widget([
        'model' => $model,
        'attribute' => 'body'
    ]) ?>

    //With ActiveForm

    <?= $form->field($model, 'body')->widget(\yii\\redactor\widgets\Redactor::className(), [
        'clientOptions' => [
            'imageManagerJson' => ['/redactor/upload/image-json'],
            'imageUpload' => ['/redactor/upload/image'],
            'fileUpload' => ['/redactor/upload/file'],
            'lang' => 'zh_cn',
            'plugins' => ['clips', 'fontcolor','imagemanager']
        ]
    ])?>
    ......
EOD;

$output = $output . '<pre>' . highlight_string($code2, true) . '</pre>';

$output = $output . '<p> Added the following requires to the section of require to composer.json. </p>';

$sComposerRequires = '';
foreach ($this->getComposerRequires()['require'] as $key => $value) {
    $sComposerRequires .= sprintf('        "%s":"%s",',$key,$value) . "\n";
}
$sComposerRequires = trim($sComposerRequires,",\n");

// $sComposerRequires = json_encode($this->getComposerRequires($this->themingID)['require'], JSON_PRETTY_PRINT);
// $sComposerRequires = trim($sComposerRequires,'{}');

        $code3 = <<<EOD
{
    ......
    "require": {
        ......
{$sComposerRequires}
        ......
    }
    ......
}
EOD;

        return $output . '<pre>' . highlight_string($code3, true) . '</pre>';
    }

    /**
     * @inheritdoc
     */
    public function noticeMessage()
    {

        $sComposerRequires = '';
        foreach ($this->getComposerRequires($this->themingID)['require'] as $key => $value) {
            $sComposerRequires .= sprintf('        "%s":"%s",',$key,$value) . "\n";
        }
        $sComposerRequires = trim($sComposerRequires,",\n");


        $code = <<<EOD
{
    ......
    "require": {
        ......
{$sComposerRequires}
        ......
    }
    ......
}
EOD;

        $code = highlight_string($code, true);

        $output = <<<EOD
<div class="panel panel-warning">
    <div class="panel-heading">
        Redactor WYSIWYG Generator will add the requires to composer.json
    </div>
    <div class="panel-body">
        {$code}
    </div>
</div>
EOD;

        return $output;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $myzero1RuntimeDir = $this->getMyzero1RuntimeDir();

        $files[] = new CodeFile(
            $myzero1RuntimeDir . '/wysiwyg_holdon.php',
            $this->render("wysiwyg_holdon.php")
        );

        return $files;
    }

    /**
     * @return boolean the directory that contains the module class
     */
    public function getMyzero1RuntimeDir()
    {
        $myzero1RuntimeDir = Yii::getAlias('@vendor/myzero1/yii2-gii/runtime');

        return $myzero1RuntimeDir;
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function addRequiresToComposer()
    {
        $path = \Yii::getAlias('@app/../composer.json');

        $composerContent = json_decode(file_get_contents($path),true);

        $aComposerRequires = $this->getComposerRequires($this->themingID);

        foreach ($aComposerRequires['require'] as $key => $value) {
            $composerContent['require'][$key] = $value;
        }

       file_put_contents($path, str_replace('\\', '', json_encode($composerContent, JSON_PRETTY_PRINT)));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getComposerRequires($id=1)
    {
        $aComposerRequires = array();

        switch ($id) {
            case 1:
                $aComposerRequires['require'] = [
                    'yiidoc/yii2-redactor' => '*',
                ];
                break;
        }

       return $aComposerRequires;
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function showExample()
    {
        $model = new \common\models\User();
        $example = \yii\redactor\widgets\Redactor::widget([
            'model' => $model,
            'attribute' => 'username'
        ]);

        return <<<EOF
<div class="panel panel-default">
    <div class="panel-heading">
        Upload example:
    </div>
    <div class="panel-body">
        {$example}
    </div>
</div>
EOF;
    }
}