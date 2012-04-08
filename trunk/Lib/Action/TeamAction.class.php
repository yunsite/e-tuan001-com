<?php
class TeamAction extends CommonAction {
    //过滤查询字段
    function _filter(&$map){
        if('' != trim($_GET['state'])){
            $now = time();
            $state = trim($_GET['state']);
            switch ($state) {
                case 'auditing':
                    $map['audit_status'] = 0;
                    break;
                case 'pass':
                    $map['audit_status'] = -1;
                    break;
                case 'success':
                    //$map['audit_status'] = 1;
                    $map['end_time'] = array('gt', $now);
                    $map['now_number'] = array('exp','>=`min_number`');//当前团购数量大于最低数量
                    break;
                default:
                    break;
            }
        }
    }
    function index(){

        //默认map
        $now = time();
        $map['system'] = 'Y';
        $map['end_time'] = array('gt', $now);
        $map['audit_status'] = array('neq', -1);
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        //print_r($map);exit;
        $name = 'Team';
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map, 'id');
        }
        $this->display();
    }
    function detail(){
        $model = D('Team');
        $team = $model->getById(trim($_GET['id']));
        if(0 == $team['audit_status']){
            $this->display('auditing');
            exit;
        }
        if(-1 == $team['audit_status']){
            $this->display('pass');
            exit;
        }

        $order = D('Order');
        $paycount = intval($order->where(array('state' => 'pay','team_id' => trim($_GET['id']),))->count());
        $buycount = intval($order->where(array('state' => 'pay','team_id' => trim($_GET['id']),))->sum('quantity'));
        $onlinepay = floatval($order->where(array('state' => 'pay','team_id' => trim($_GET['id']),))->sum('money'));
        $creditpay = floatval($order->where(array('state' => 'pay','team_id' => trim($_GET['id']),))->sum('credit'));
        $cardpay = floatval($order->where(array('state' => 'pay','team_id' => trim($_GET['id']),))->sum('card'));
        $this->assign('paycount', $paycount);
        $this->assign('buycount', $buycount);
        $this->assign('onlinepay', $onlinepay);
        $this->assign('creditpay', $creditpay);
        $this->assign('cardpay', $cardpay);
        $this->assign('team', $team);
        $this->display('detail');
    }
}




/*
* 列表获取参数部分方法
*/
//根据类型id获取类型的名称
function get_group_name($group_id){
    $model = new Model();
    $group = $model->query('select id,name from category where id = '.$group_id);
    return $group[0]['name'];
}
//获取时间值
function get_date($begin_time, $end_time){
    return "上线：".date('Y-m-d', $begin_time)."<br />下线：".date('Y-m-d', $end_time);
}
//获取成交数量
function get_number($now_munber, $min_number){
    return $now_munber." / ".$min_number;
}
//获取价格
function get_price($team_price, $market_price){
    return "团购价：￥".moneyit($team_price)."<br>原价：￥<lebel style='text-decoration:line-through'>".moneyit($market_price)."<label>";
}
//团购状态
function team_state(&$team) {
    if ( $team['now_number'] >= $team['min_number'] ) {
        if ($team['max_number']>0) {
            if ( $team['now_number']>=$team['max_number'] ){
                if ($team['close_time']==0) {
                    $team['close_time'] = $team['end_time'];
                }
                return $team['state'] = 'soldout';
            }
        }
        if ( $team['end_time'] <= time() ) {
            $team['close_time'] = $team['end_time'];
        }
        return $team['state'] = 'success';
    } else {
        if ( $team['end_time'] <= time() ) {
            $team['close_time'] = $team['end_time'];
            return $team['state'] = 'failure';
        }
    }
    return $team['state'] = 'none';
}
function state_explain($team, $error='false') {
    $state = team_state($team);
    $state = strtolower($state);
    switch($state) {
        case 'none': return '正在进行中';
        case 'soldout': return '已售光';
        case 'failure': if($error) return '团购失败';
        case 'success': return '团购成功';
        default: return '已结束';
    }
}
function totle_money($a, $b){
    return $a+$b;
}
?>