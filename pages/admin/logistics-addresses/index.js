import React from 'react';
import {CTableDeleteLink, Table, TableProvider, useTable} from '@mxjs/a-table';
import {CEditLink, CNewBtn} from '@mxjs/a-clink';
import {Page, PageActions} from '@mxjs/a-page';
import {LinkActions} from '@mxjs/actions';
import {Tag} from 'antd';

const typeNames = {
  1: '退货'
};

export default () => {
  const [table] = useTable();

  return (
    <Page>
      <PageActions>
        <CNewBtn/>
      </PageActions>

      <TableProvider>
        <Table
          tableApi={table}
          columns={[
            {
              title: '联系人姓名',
              dataIndex: 'name',
            },
            {
              title: '联系人电话',
              dataIndex: 'phone',
            },
            {
              title: '地址',
              dataIndex: 'address',
              render: (value, {region}) => [
                region?.parent?.parent?.shortName,
                region?.parent?.shortName,
                region?.shortName,
                value
              ].join(' '),
            },
            {
              title: '使用场景',
              dataIndex: 'types',
              render: (value) => value.length ?
                value.map(type => <Tag key={type} color="orange">{typeNames[type]}</Tag>) : '-',
            },
            {
              title: '顺序',
              dataIndex: 'sort',
              sorter: true,
            },
            {
              title: '最后更改时间',
              dataIndex: 'updatedAt',
              width: 180,
            },
            {
              title: '操作',
              dataIndex: 'id',
              render: (id) => (
                <LinkActions>
                  <CEditLink id={id}/>
                  <CTableDeleteLink id={id}/>
                </LinkActions>
              ),
            },
          ]}
        />
      </TableProvider>
    </Page>
  );
};
