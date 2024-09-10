/**
 * @share [id]/edit
 */
import { CListBtn } from '@mxjs/a-clink';
import { Page, PageActions } from '@mxjs/a-page';
import { Form, FormActions, FormItem } from '@mxjs/a-form';
import RegionCascader from '@mxjs/a-region-cascader';
import { Checkbox } from 'antd';
import { FormItemSort } from '@miaoxing/admin';
import Input from '@mxjs/a-input';
import { Section } from '@mxjs/a-section';
import { useQuery } from '@mxjs/query';

const New = () => {
  const { data } = useQuery('consts/logisticsAddressModel-type');
  const items = data?.items || [];

  return (
    <Page>
      <PageActions>
        <CListBtn/>
      </PageActions>

      <Form
        afterLoad={({ret}) => {
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
        <Section>
          <FormItem label="联系人姓名" name="name" required>
            <Input maxLength={16}/>
          </FormItem>

          <FormItem label="联系人电话" name="phone" required>
            <Input maxLength={16}/>
          </FormItem>

          <FormItem label="地区" name="regionIds" required
                    rules={[
                      {
                        validator: async (rule, value) => {
                          if (value && value.length !== 3) {
                            throw new Error('请选择地区到第三级');
                          }
                        },
                      },
                    ]}
          >
            <RegionCascader parentId="中国" url="regions" fieldNames={{
              label: 'shortName',
              value: 'id',
              children: 'children',
            }}/>
          </FormItem>

          <FormItem label="详细地址" name="address" required>
            <Input maxLength={255}/>
          </FormItem>

          <FormItem label="邮编" name="postalCode">
            <Input maxLength={8}/>
          </FormItem>

          <FormItem label="使用场景" name="types">
            <Checkbox.Group>
              {Object.values(items).map(type => (
                <Checkbox key={type.id} value={type.id}>
                  {type.name}
                </Checkbox>
              ))}
            </Checkbox.Group>
          </FormItem>

          <FormItem label="备注" name="remark">
            <Input.TextArea maxLength={255}/>
          </FormItem>

          <FormItemSort/>
        </Section>

        <FormActions/>
      </Form>
    </Page>
  );
};

export default New;
