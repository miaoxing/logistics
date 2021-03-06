import Page from './new';
import {fireEvent, render, screen} from '@testing-library/react';
import {MemoryRouter} from 'react-router';
import React from 'react';
import {app} from '@mxjs/app';
import $, {Ret} from 'miaoxing';
import {waitFor} from '@testing-library/dom';
import {bootstrap, createPromise, setUrl, resetUrl} from '@mxjs/test';

bootstrap();

const path = 'admin/logistics-addresses';

describe(path, () => {
  beforeEach(() => {
    setUrl(path + '/new');
    app.page = {
      collection: path,
      index: false,
    };
  });

  afterEach(() => {
    resetUrl();
    app.page = {};
  });

  test('form', async () => {
    const promise = createPromise();
    const promise2 = createPromise();
    const promise3 = createPromise();
    const promise4 = createPromise();
    const promise5 = createPromise();

    $.http = jest.fn()
      // 读取一级地区
      .mockImplementationOnce(() => promise.resolve(Ret.new({
        code: 1,
        data: [{
          id: 140000,
          name: '山西省',
          shortName: '山西',
          hasChildren: true,
        }],
      })))
      // 读取默认数据
      .mockImplementationOnce(() => promise2.resolve(Ret.new({
        code: 1,
        data: {
          id: 1,
          name: '联系人1',
          phone: '13800138000',
          region: {
            id: 140222,
            name: '天镇县',
            shortName: '天镇',
            parent: {
              id: 140200,
              name: '大同市',
              shortName: '大同',
              parent: {
                id: 140000,
                name: '山西省',
                shortName: '山西',
              },
            },
          },
          address: '199号',
          types: [1],
          sort: 51,
        },
      })))
      // 读取二级地区
      .mockImplementationOnce(() => promise3.resolve(Ret.new({
        code: 1,
        data: [{
          id: 140200,
          parentId: 140000,
          name: '大同市',
          shortName: '大同',
          hasChildren: true,
        }],
      })))
      // 读取三级地区
      .mockImplementationOnce(() => promise4.resolve(Ret.new({
        code: 1,
        data: [{
          hasChildren: false,
          id: 140222,
          name: '天镇县',
          parentId: 140200,
          shortName: '天镇',
        }],
      })))
      // 提交
      .mockImplementationOnce(() => promise5.resolve({
        code: 1,
      }));

    const {getByLabelText} = render(<MemoryRouter>
      <Page/>
    </MemoryRouter>);

    await Promise.all([promise, promise2, promise3, promise4]);
    expect($.http).toHaveBeenCalledTimes(4);
    expect($.http).toMatchSnapshot();

    // 看到表单加载了数据
    await waitFor(() => expect(getByLabelText('顺序').value).toBe('51'));
    expect(getByLabelText('联系人姓名').value).toBe('联系人1');

    // 提交表单
    fireEvent.click(screen.getByText('提 交'));

    await Promise.all([promise5]);
    expect($.http).toHaveBeenCalledTimes(5);
    expect($.http).toMatchSnapshot();
  });
});
