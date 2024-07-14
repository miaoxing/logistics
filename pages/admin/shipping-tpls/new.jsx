/**
 * @share [id]/edit
 */
import { useEffect, useRef, useState } from 'react';
import { CListBtn } from '@mxjs/a-clink';
import { Page, PageActions } from '@mxjs/a-page';
import { Form, FormItem, FormAction, FormList } from '@mxjs/a-form';
import {
  Radio,
  Switch,
  Table,
  InputNumber,
  TreeSelect,
  Typography, theme,
} from 'antd';
import $ from 'miaoxing';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import { FormItemSort, InputPrice } from '@miaoxing/admin';
import PropTypes from 'prop-types';
import clsx from 'clsx';
import { Section } from '@mxjs/a-section';

// 默认的物流服务编号，即"快递"
const DEFAULT_SERVICE_ID = 1;

const FeeFormItem = (props) => {
  return <FormItem required className="mb-0" {...props}>
    <InputPrice className="w-[70px]"/>
  </FormItem>;
};

const NumberFormItem = (props) => {
  return <FormItem required className="mb-0" {...props}>
    <InputNumber min={1} max={1000} precision={0} controls={false} className="w-[70px]"/>
  </FormItem>;
};

const {useToken} = theme;
const FormText = ({className, children, ...rest}) => {
  const {token} = useToken();

  return (
    <div className={clsx('flex items-center', className)} style={{height: token.controlHeight}} {...rest}>
      {children}
    </div>
  );
};

FormText.propTypes = {
  children: PropTypes.node,
};

const New = () => {
  const form = useRef();

  // 加载区域
  const [regions, setRegions] = useState([]);
  useEffect(() => {
    $.get({
      url: 'regions',
      params: { virtual: 0, parentId: '中国', include: 'children' },
    }).then(({ret}) => {
      if (ret.isSuc()) {
        const data = ret.data.map(region => {
          return {
            title: region.name,
            value: region.id,
            children: region.children.map(child => {
              return {
                title: child.name,
                value: child.id,
              };
            }),
          };
        });
        setRegions(data);
      } else {
        $.ret(ret);
      }
    });
  }, []);

  const createRule = (rule = {}) => {
    return {
      key: new Date().getTime(),
      startAmount: 1,
      addAmount: 1,
      ...rule,
    };
  };

  return (
    <Page>
      <PageActions>
        <CListBtn/>
      </PageActions>

      <Form
        formRef={form}
        afterLoad={({ret}) => {
          // 将物流服务的编号作为索引
          const services = {};

          if (ret.data.rules.length === 0) {
            // 新记录创建一个默认规则
            services[DEFAULT_SERVICE_ID] = {
              id: DEFAULT_SERVICE_ID,
              rules: [
                createRule({isDefault: true}),
              ],
            };
          } else {
            ret.data.rules.forEach(function (rule) {
              if (typeof services[rule.serviceId] === 'undefined') {
                services[rule.serviceId] = {
                  id: rule.serviceId,
                  rules: [],
                };
              }
              services[rule.serviceId].rules.push(rule);
            });
          }

          ret.data.services = Object.values(services);
          delete ret.data.rules;
        }}
        beforeSubmit={values => {
          if (values.isFreeShipping) {
            return values;
          }

          let rules = [];
          values.services.forEach(service => {
            rules = rules.concat(service.rules.map(rule => {
              rule.serviceId = service.id;
              return rule;
            }));
          });
          values.rules = rules;
          delete values.services;

          return values;
        }}
      >
        {({isFreeShipping, valuationType, services = []}) => {
          const unit = valuationType === 1 ? '件' : ' kg ';

          return <>
            <Section>
              <FormItem label="名称" name="name" required/>

              <FormItem label="是否包邮" name="isFreeShipping" valuePropName="checked">
                <Switch/>
              </FormItem>

              {!isFreeShipping && <>
                <FormItem label="计价方式" name="valuationType">
                  <Radio.Group>
                    <Radio value={1}>按件</Radio>
                    <Radio value={2}>按重量</Radio>
                  </Radio.Group>
                </FormItem>

                <FormItem label="运费规则" wrapperCol={{span: 18}}>
                  <FormText className="text-gray-500 mb-2">请设置地区对应的运费，未设置的地区使用默认运费。</FormText>

                  <FormList
                    name="services">
                    {(fields) => (
                      <>
                        {fields.map((field, index) => {
                          // 每次使用新的对象，以便表格会重新加载
                          const rules = [...services[index].rules];

                          // 增加规则
                          const addRule = (e) => {
                            e.preventDefault();
                            services[index].rules.push(createRule());
                            form.current.setFieldsValue({
                              services: services,
                            });
                          };

                          // 删除规则
                          const deleteRule = (ruleIndex, e) => {
                            e.preventDefault();
                            $.confirm('删除后将不能还原，确认删除？', result => {
                              if (!result) {
                                return;
                              }

                              services[index].rules.splice(ruleIndex, 1);
                              form.current.setFieldsValue({
                                services: services,
                              });
                            });
                          };

                          return <Table
                            key={field.key}
                            columns={[
                              {
                                title: '地区',
                                dataIndex: 'regionIds',
                                className: 'align-top',
                                render: (cell, row, ruleIndex) => {
                                  if (row.isDefault) {
                                    return <FormText>默认</FormText>;
                                  }

                                  // 找到其他规则已选中的地区
                                  let otherRegionIds = [];
                                  rules.forEach((rule, index) => {
                                    if (index !== ruleIndex) {
                                      otherRegionIds = otherRegionIds.concat(rule.regionIds);
                                    }
                                  });

                                  // 禁用已选中的地区
                                  const treeData = regions.map(region => {
                                    const disabled = otherRegionIds.includes(region.value);
                                    return {
                                      ...region,
                                      disabled,
                                      children: region.children.map(child => {
                                        return {
                                          ...child,
                                          disabled: disabled || otherRegionIds.includes(child.value),
                                        };
                                      }),
                                    };
                                  });

                                  return (
                                    <FormItem name={[index, 'rules', ruleIndex, 'regionIds']} required
                                              style={{marginBottom: 0}}
                                    >
                                      <TreeSelect treeData={treeData} treeCheckable
                                                  showCheckedStrategy={TreeSelect.SHOW_PARENT} placeholder="请选择"
                                                  treeNodeFilterProp="title"
                                      />
                                    </FormItem>
                                  );
                                },
                              },
                              {
                                title: '运费',
                                dataIndex: 'startAmount',
                                align: 'center',
                                width: 460,
                                className: 'align-top',
                                render: (value, row, ruleIndex) => {
                                  return <div className="flex align-start justify-between">
                                    <NumberFormItem name={[index, 'rules', ruleIndex, 'startAmount']}/>
                                    <FormText>{unit}内</FormText>
                                    <FeeFormItem name={[index, 'rules', ruleIndex, 'startFee']}/>
                                    <FormText>元，每增加</FormText>
                                    <NumberFormItem name={[index, 'rules', ruleIndex, 'addAmount']}/>
                                    <FormText>{unit}</FormText>
                                    <FeeFormItem name={[index, 'rules', ruleIndex, 'addFee']}/>
                                    <FormText>元</FormText>
                                  </div>;
                                },
                              },
                              {
                                title: '操作',
                                dataIndex: 'id',
                                align: 'center',
                                width: 100,
                                className: 'align-top',
                                render: (value, row, ruleIndex) => {
                                  return row.isDefault ? <span title="默认地区不能删除">-</span> : (
                                    <Typography.Link type="danger" onClick={deleteRule.bind(this, ruleIndex)} href="#"
                                                     title="删除">
                                      <DeleteOutlined/>
                                    </Typography.Link>
                                  );
                                },
                              },
                            ]}
                            bordered
                            dataSource={rules}
                            rowKey={(record) => record.id || record.key}
                            footer={() => {
                              return <a onClick={addRule} href="#">
                                <PlusOutlined/> 为指定地区设置运费
                              </a>;
                            }}
                            pagination={false}
                          />;
                        })}

                      </>
                    )}
                  </FormList>
                </FormItem>
              </>}

              <FormItemSort extra={
                <>
                  大的显示在前面，按从大到小排列。<br/>
                  创建商品时，将默认选中最大顺序的运费模板。
                </>
              }
              />

            </Section>
            <FormAction/>
          </>;
        }}
      </Form>
    </Page>
  );
};

export default New;
