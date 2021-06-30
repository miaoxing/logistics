import Index from './index';
import {render} from '@testing-library/react';
import {MemoryRouter} from 'react-router';
import React from 'react';
import $, {Ret} from 'miaoxing';
import {bootstrap, createPromise, setUrl, resetUrl} from '@mxjs/test';
import {app} from '@mxjs/app';

bootstrap();

const path = 'admin/logistics-addresses';

describe(path, () => {
  beforeEach(function () {
    setUrl(path);
    app.page = {
      collection: path,
      index: true,
    };
  });

  afterEach(() => {
    resetUrl();
    app.page = {};
  });

  test('index', async () => {
    const promise = createPromise();

    $.http = jest.fn()
      // 读取列表数据
      .mockImplementationOnce(() => promise.resolve({
        ret: Ret.new({
          code: 1,
          data: [
            {
              id: 1,
              name: '联系人1',
              phone: '13800138000',
              region: {
                name: '天镇县',
                shortName: '天镇',
                parent: {
                  name: '大同市',
                  shortName: '大同',
                  parent: {
                    name: '山西省',
                    shortName: '山西',
                  },
                },
              },
              address: '199号',
              types: [1],
              sort: 51,
              updatedAt: '2020-01-01 00:00:00',
            },
          ],
        }),
      }));

    const {findByText} = render(<MemoryRouter>
      <Index/>
    </MemoryRouter>);

    await findByText('联系人1');
    await findByText('13800138000');
    await findByText('山西 大同 天镇 199号');
    await findByText('退货');
    await findByText('51');
    await findByText('2020-01-01 00:00:00');

    await Promise.all([promise]);
    expect($.http).toHaveBeenCalledTimes(1);
    expect($.http).toMatchSnapshot();
  });
});
