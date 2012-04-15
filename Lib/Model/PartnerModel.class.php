<?php
class PartnerModel extends CommonModel {
    protected $trueTableName ='partner';//
    // 自动验证设置
    protected $_validate	 =	 array(
        array('username','require','用户名必须！',1),
        array('title','require','商户名称！',1),
        array('phone','require','联系电话必须！',1),
        array('address','require','商户地址必须！',1),
        array('username','','此用户名已经存在',0,'unique',self::MODEL_INSERT),
    );
    // 自动填充设置
	protected $_auto	 =	 array(
		array('create_time','time',self::MODEL_INSERT,'function'),
	);
}
?>