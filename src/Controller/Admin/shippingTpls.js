describe('admin/shipping-tpls 检查页面是否可以访问', function () {
  before(function () {
    casper.start();
  });

  ['index', 'index.json', 'new', 'create', 'edit', 'update', 'destroy'].forEach(function (action) {
    it('可以访问' + action, function () {
      casper.thenOpen(casper.config.baseUrl + '/admin/shipping-tpls/' + action, function (response) {
        response.status.should.not.equal(500);
      });
    });
  });
});
