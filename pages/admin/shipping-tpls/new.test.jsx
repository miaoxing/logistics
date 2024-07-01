import Page from './new';
import {fireEvent, render, screen} from '@testing-library/react';
import {MemoryRouter} from 'react-router';
import {app} from '@mxjs/app';
import $, {Ret} from 'miaoxing';
import {waitFor} from '@testing-library/dom';
import {bootstrap, createPromise, setUrl, resetUrl} from '@mxjs/test';
import {act} from 'react-dom/test-utils';

bootstrap();

const path = 'admin/shipping-tpls';

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

    $.http = jest.fn()
      // 读取默认数据
      .mockImplementationOnce(() => promise.resolve({
        ret: Ret.suc({
          data: {
            name: '测试',
            sort: 50,
            rules: [
              {
                id: 1,
                serviceId: 1,
                isDefault: true,
                regionIds: [],
                startAmount: 1,
                startFee: 2,
                addAmount: 3,
                addFee: 4,
              },
              {
                id: 2,
                serviceId: 1,
                isDefault: false,
                regionIds: [1],
                startAmount: 1,
                startFee: 2,
                addAmount: 3,
                addFee: 4,
              },
            ],
          },
        }),
      }))
      // 读取区域数据
      .mockImplementationOnce(() => promise2.resolve({
        ret: Ret.suc({
          data: [{
            id: 1,
            name: '默认地区',
            children: [],
          }],
        }),
      }))
      // 提交
      .mockImplementationOnce(() => promise3.resolve({
        ret: Ret.suc(),
      }));

    const {getByLabelText} = render(<MemoryRouter>
      <Page/>
    </MemoryRouter>);

    await Promise.all([promise, promise2]);
    expect($.http).toHaveBeenCalledTimes(2);
    expect($.http).toMatchSnapshot();

    // 看到表单加载了数据
    await waitFor(() => {
      expect(getByLabelText('顺序').value).toBe('50');
    }, {timeout: 2000});
    expect(getByLabelText('名称').value).toBe('测试');

    // 提交表单
    await act(async () => {
      fireEvent.click(screen.getByText('提 交'));
    });

    await Promise.all([promise3]);
    expect($.http).toHaveBeenCalledTimes(3);
    expect($.http).toMatchSnapshot();
  });
});
