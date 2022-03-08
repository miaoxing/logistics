<?php

namespace Miaoxing\Logistics\Migration;

use Wei\Migration\BaseMigration;

class V20210312231350CreateLogisticsAddressesTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('logistics_addresses')->tableComment('物流地址')
            ->bigId()
            ->uBigInt('app_id')->comment('应用编号')
            ->uInt('region_id')->comment('地区编号')
            ->string('name', 16)->comment('联系人姓名')
            ->string('phone', 16)->comment('联系人电话')
            ->string('address')->comment('详细地址')
            ->string('postal_code', 8)->comment('邮编')
            ->string('types', 16)->comment('使用场景')
            ->string('remark')->comment('备注')
            ->uSmallInt('sort')->comment('顺序')->defaults(50)
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('logistics_addresses');
    }
}
