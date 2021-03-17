/**
 * @share [id]/edit
 */
import React, {useRef} from 'react';
import {CListBtn} from '@mxjs/a-clink';
import {Page, PageActions} from '@mxjs/a-page';
import {Form, FormAction, FormItem} from '@mxjs/a-form';
import RegionCascader from '@mxjs/a-region-cascader';
import {Checkbox, Col, Row, Tag} from 'antd';
import $ from 'miaoxing';

export default () => {
  const form = useRef();

  return (
    <Page>
      <PageActions>
        <CListBtn/>
      </PageActions>

      <Form
        formRef={form}
        afterLoad={(ret) => {
          if (ret.data.region) {
            const region = ret.data.region;
            ret.data.regionIds = [
              region.parent?.parent?.id,
              region.parent?.id,
              region.id,
            ];
          }
        }}
        beforeSubmit={values => {
          values.regionId = values.regionIds[values.regionIds.length - 1];
          delete values.regionIds;
          return values;
        }}
      >
        {() => {
          return <>
            <FormItem label="联系人姓名" name="name" required/>

            <FormItem label="联系人电话" name="phone" required/>

            <FormItem label="地区" name="regionIds" required>
              <RegionCascader parentId="中国" url={$.url('api/regions')} fieldNames={{
                label: 'shortName',
                value: 'id',
                children: 'children'
              }}/>
            </FormItem>

            <FormItem label="详细地址" name="address" required/>

            <FormItem label="邮编" name="postalCode"/>

            <FormItem label="使用场景" name="types">
              <Checkbox.Group>
                <Row>
                  <Col span={32}>
                    <Checkbox value={1}>
                      退货
                    </Checkbox>
                  </Col>
                </Row>
              </Checkbox.Group>
            </FormItem>

            <FormItem label="备注" name="remark" type="textarea"/>

            <FormItem label="顺序" name="sort" type="number" extra="大的显示在前面，按从大到小排列。"/>

            <FormItem name="id" type="hidden"/>

            <FormAction/>
          </>
        }}
      </Form>
    </Page>
  );
};
