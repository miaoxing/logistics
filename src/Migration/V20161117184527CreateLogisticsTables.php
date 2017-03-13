<?php

namespace Miaoxing\Logistics\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161117184527CreateLogisticsTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('logistics')
            ->tableComment('系统自带的快递服务商')
            ->smallInt('id')
            ->string('kuaidi100Id', 32)
            ->string('name', 16)
            ->string('logo')
            ->primary('id')
            ->exec();

        $this->db->insertBatch('logistics', [
            ['id' => 1, 'kuaidi100Id' => '', 'name' => '快递'],
            ['id' => 2, 'kuaidi100Id' => '', 'name' => '到店自提'],
            ['id' => 3, 'kuaidi100Id' => 'youzhengguonei', 'name' => '中国邮政'],
            ['id' => 4, 'kuaidi100Id' => 'ems', 'name' => 'EMS'],
            ['id' => 5, 'kuaidi100Id' => 'shunfeng', 'name' => '顺丰速运'],
            ['id' => 6, 'kuaidi100Id' => 'yuantong', 'name' => '圆通速递'],
            ['id' => 7, 'kuaidi100Id' => 'shentong', 'name' => '申通快递'],
            ['id' => 8, 'kuaidi100Id' => 'zhongtong', 'name' => '中通快递'],
            ['id' => 9, 'kuaidi100Id' => 'yunda', 'name' => '韵达快运'],
            ['id' => 10, 'kuaidi100Id' => 'lianbangkuaidi', 'name' => '联邦快递'],
            ['id' => 11, 'kuaidi100Id' => 'huitongkuaidi', 'name' => '百世汇通'],
            ['id' => 12, 'kuaidi100Id' => 'ganzhongnengda', 'name' => '港中能达'],
            ['id' => 13, 'kuaidi100Id' => 'jd', 'name' => '京东'],
        ]);

        $this->schema->table('shippingTpls')
            ->id()
            ->int('appId')
            ->string('logisticsIds', 64)->comment('支持的物流服务编号')
            ->string('useLogisticsIds', 128)->comment('使用"xx"运费模板 0默认 -1自定义')
            ->string('name', 32)
            ->bool('freeShipping')->comment('是否包邮')
            ->timestampsV1()
            ->int('createUser')
            ->int('updateUser')
            ->timestamp('deleteTime')
            ->int('deleteUser')
            ->exec();

        $this->schema->table('shippingTplRules')
            ->id()
            ->int('shippingTplId')
            ->smallInt('logisticsId')->defaults(1)->comment('对应物流服务商编号')
            ->bool('isDefault')
            ->text('areas')->comment('用于逻辑判断的区域')
            ->text('areaNames')->comment('用于后台展示的区域')
            ->decimal('startFee', 10, 2)
            ->decimal('plusFee', 10, 2)
            ->timestampsV1()
            ->int('createUser')
            ->int('updateUser')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('logistics');
        $this->schema->dropIfExists('shippingTpls');
        $this->schema->dropIfExists('shippingTplRules');
    }
}
