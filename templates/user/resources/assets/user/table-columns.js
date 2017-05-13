export default [
  {data: 'id', name: 'id', title: 'ID'},
  {data: 'name', name: 'name', title: '用户名'},
  {data: 'email', name: 'email', title: 'Email'},
  {
    data: 'gender', name: 'gender', title: '性别',
    render: function (data) {
      let map = {'Male': '男', 'Female': '女'};
      return data ? map[data] : '';
    }
  },
  {data: 'mobile', name: 'mobile', title: '手机'},
  {
    data: 'is_active', name: 'is_active', title: '激活',
    render: function (data) {
      let map = {1: '激活', 2: '停用'};
      return map[data];
    }
  },
  {data: 'last_login_at', name: 'last_login_at', title: '上次登录'},
  {data: 'login_times', name: 'login_times', title: '登录次数'}
];