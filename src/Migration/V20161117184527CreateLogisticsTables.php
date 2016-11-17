<?php

namespace MyNamespace\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161117184527CreateLogisticsTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->scheme->table('logistics')
            ->id()
            ->string('kuaidi100Id', 32)
            ->string('name', 16)
            ->string('logo')
            ->exec();

        //系统自带的快递服务商

        /*CREATE TABLE `logistics` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `kuaidi100Id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apixName` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'apix接口的快递商家名称',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='系统自带的快递服务商';*/
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->scheme->dropIfExists('logistics');
    }
}
