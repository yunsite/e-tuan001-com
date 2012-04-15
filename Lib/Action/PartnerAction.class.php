<?php
class PartnerAction extends CommonAction {
    function index(){
        $name = 'Partner';
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map, 'id');
        }
        $this->display();
    }

    function setgooglemap(){
        $ll = strval($_GET['ll']);
        if(!$ll) $ll = '23.11,113.24';
        list($longi, $lati) = preg_split('/[,\s]+/',$ll,-1,PREG_SPLIT_NO_EMPTY);
        $this->assign('longi', $longi);
        $this->assign('lati', $lati);
        $this->display();
    }
    function edit(){
        $name = 'Partner';
        $model = D($name);
        $partner =  $model->getById($_GET['id']);
        $this->assign('partner', $partner);
        $this->display();
    }
}
?>