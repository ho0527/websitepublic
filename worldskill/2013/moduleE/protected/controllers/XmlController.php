<?php
/**
 * XML / XSD 功能：檢視、下載並以題目提供的 XML Schema 驗證。
 */
class XmlController extends Controller
{
    public function actionIndex()
    {
        $builder = new LvbXmlBuilder();
        $xml     = $builder->build();
        $result  = LvbXmlBuilder::validate($xml);

        $this->breadcrumbs = array('XML');
        $this->operations  = $this->buildOperations();
        $this->render('index', array(
            'xml'    => $xml,
            'result' => $result,
        ));
    }

    /**
     * 直接輸出 XML 檔供下載。
     */
    public function actionDownload()
    {
        $builder = new LvbXmlBuilder();
        $xml     = $builder->build();

        Yii::app()->request->sendFile('lvb_system.xml', $xml, 'application/xml', false);
    }

    /**
     * 於瀏覽器中直接顯示 XML（Content-Type: application/xml）。
     */
    public function actionDisplay()
    {
        $builder = new LvbXmlBuilder();
        header('Content-Type: application/xml; charset=UTF-8');
        echo $builder->build();
        Yii::app()->end();
    }

    private function buildOperations()
    {
        return array(
            'Display XML'  => array('xml/display'),
            'Download XML' => array('xml/download'),
            'Validate XML' => array('xml/index'),
        );
    }
}
