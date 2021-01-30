<?php

namespace Miaoxing\Logistics\Migration;

use Wei\Migration\BaseMigration;
use Wei\Time;

class V20161117184527CreateLogisticsTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('logistics_services')->tableComment('物流服务')
            ->string('name', 16)->comment('名称')
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();

        $this->db->batchInsert('logistics_services', [
            ['name' => '快递'],
        ]);

        $this->schema->table('logistics_providers')->tableComment('物流服务商')
            ->id()
            ->string('kuaidi100_id', 32)->comment('快递100的编号')
            ->string('name', 16)->comment('名称')
            ->string('logo')->comment('图标')
            ->mediumInt('sort')->defaults(50)->comment('顺序')
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();

        $this->db->batchInsert('logistics_providers', [
            ['kuaidi100_id' => 'youzhengguonei', 'name' => '中国邮政'],
            ['kuaidi100_id' => 'ems', 'name' => 'EMS'],
            ['kuaidi100_id' => 'shunfeng', 'name' => '顺丰速运'],
            ['kuaidi100_id' => 'yuantong', 'name' => '圆通速递'],
            ['kuaidi100_id' => 'shentong', 'name' => '申通快递'],
            ['kuaidi100_id' => 'zhongtong', 'name' => '中通快递'],
            ['kuaidi100_id' => 'yunda', 'name' => '韵达快运'],
            ['kuaidi100_id' => 'lianbangkuaidi', 'name' => '联邦快递'],
            ['kuaidi100_id' => 'huitongkuaidi', 'name' => '百世汇通'],
            ['kuaidi100_id' => 'ganzhongnengda', 'name' => '港中能达'],
            ['kuaidi100_id' => 'jd', 'name' => '京东'],
            ['kuaidi100_id' => 'pengcheng', 'name' => '鹏程快递'],
            ['kuaidi100_id' => 'youshuwuliu', 'name' => '优速物流'],
            ['kuaidi100_id' => 'zhongyuankuaidi', 'name' => '中源快递'],
            ['kuaidi100_id' => 'tiantiankuaidi', 'name' => '天天快递'],
            ['kuaidi100_id' => 'zhaijisong', 'name' => '宅急送'],
        ]);

        $this->schema->table('shipping_tpls')->tableComment('运费模板')
            ->id()
            ->uInt('app_id')
            ->string('service_ids', 64)->comment('支持的物流服务编号')
            ->string('name', 32)->comment('名称')
            ->uInt('send_region_id')->comment('发货地址')
            ->smallInt('send_time')->comment('发货时间。以小时为单位，-1表示无')->defaults(-1)
            ->bool('is_free_shipping')->comment('是否包邮')
            ->uTinyInt('valuation_type')->comment('计价方式。1:按件;2:按重量')->defaults(1)
            ->mediumInt('sort')->comment('顺序')->defaults(50)
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();

        $this->schema->table('shipping_tpl_rules')
            ->id()
            ->uInt('app_id')
            ->uInt('shipping_tpl_id')
            ->uSmallInt('service_id')->defaults(1)->comment('物流服务编号')
            ->bool('is_default')->comment('是否默认')
            ->string('region_ids', 2048)->comment('指定的区域编号')
            ->uSmallInt('start_amount')->comment('首费数量')
            ->uDecimal('start_fee')->comment('首费金额')
            ->uSmallInt('add_amount')->comment('增费数量')
            ->uDecimal('add_fee')->comment('增费金额')
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();

        $now = Time::now();
        $this->db->batchInsert('shipping_tpls', [
            [
                'app_id' => 1,
                'service_ids' => '1',
                'name' => '包邮',
                'is_free_shipping' => true,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
            [
                'app_id' => 1,
                'service_ids' => '1',
                'name' => '按件 首件10 加件8',
                'is_free_shipping' => false,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
        ]);

        $this->db->batchInsert('shipping_tpl_rules', [
            [
                'shipping_tpl_id' => 2,
                'is_default' => true,
                'start_amount' => 1,
                'start_fee' => 10,
                'add_amount' => 1,
                'add_fee' => 10,
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('logistics_services');
        $this->schema->dropIfExists('logistics_providers');
        $this->schema->dropIfExists('shipping_tpls');
        $this->schema->dropIfExists('shipping_tpl_rules');
    }
}
