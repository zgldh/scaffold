/**
 * Created by zhangwb-pc on 03/28/2018.
 */

export default {
  app_name: '管理平台',
  navbar: {
    my_profile: '个人页面',
    log_out: '退出登录',
    dashboard: '首页',
    github: '项目地址',
    screenfull: '全屏',
    language: '切换语言',
    notification: '通知',
    theme: '换肤'
  },
  routes: {
    dashboard: '首页',
    my_profile: '个人页面',
    role_list: '角色',
    role_create: '创建角色',
    role_edit: '编辑角色'
  },
  list: {
    advance_search: '高级搜索',
    export_button: '导出为 CSV',
    export_file_name: '导出-{title}-{timestamp}'
  },
  pages: {
    login: {
      sign_in: '登录',
      forget_password: '忘记密码了？'
    },
    password: {
      forget_title: '发送重置密码邮件',
      reset_title: '重置我的密码',
      forget_note: '请输入您的注册邮箱，您将收到重置密码邮件',
      forget_send: '发送',
      reset_password: '重置密码',
      send_email_success: '重置密码邮件发送成功！请查询邮件重置密码',
      reset_success: '密码修改成功！请使用新密码登录',
      reset_error: 'Token 已过期，请重新获取重置密码邮件',
      back_to_login: '返回登录'
    },
    role: {
      terms: {
        edit_permission: '编辑权限',
        copy_complete_text: '复制完毕，要立即编辑该角色么？'
      }
    },
    my_profile: {
      basic: '基本信息',
      change_password: '修改密码',
      old_password: '旧密码',
      new_password: '新密码',
      repeat: '重复新密码'
    }
  },
  components: {
    avatar_editor: {
      button_text: '修改头像',
      title: '头像'
    },
    advance_search: {
      add_button: '添加搜索'
    },
    editor_title: {
      create: '创建{name}',
      edit: '编辑{name}'
    },
    list_title: '{name}列表',
    lang_select: {
      complete_text: '切换语言完毕'
    },
    permission_matrix: {
      model_row: {
        other: '其他'
      }
    },
    permission_editor_dialog: {
      warning: '如果你不知道你在做什么，不要改这个字段！'
    },
    notification: {
      latest: '最新',
      mark_all_as_read: '全部标为已读',
      mark_as_read: '标为已读',
      mark_as_unread: '标为未读',
      no_notification: '太好了！没有任何通知！',
      no_more: '没有更多了'
    }
  },
  messages: {
    role_copy: {
      title: '复制角色 {label}',
      text: '请输入新角色的内部名称',
      input_error_message: '只能以字母开头，且使用以下字符: a-zA-Z0-9-_'
    },
    text_copy: {
      complete: '复制完成',
      title: '复制失败',
      text: '请手工复制以下内容'
    },
    session_expired: {
      title: '确定登出',
      text: '你已被登出，可以取消继续留在该页面，或者重新登录',
      confirm: '重新登录',
      cancel: '取消'
    },
    delete_confirm: {
      title: '删除确认',
      text: '真的要删除“{name}”吗？',
      confirm: '删除',
      cancel: '取消',
      success_text: '{name}已经被删除了',
      cancel_text: '已取消'
    }
  },
  validator: {
    // Form Item
    'default': '%s 输入有误',
    required: '请输入 %s',
    'enum': '%s 必须为 %s 其中之一',
    whitespace: '%s 不可为空',
    date: {
      format: '%s 日期 %s 不符合该格式 %s',
      parse: '%s 该日期无法被解析, %s 无效',
      invalid: '%s 日期 %s 无效'
    },
    types: {
      string: '%s 不是一个有效的 %s',
      method: '%s 不是一个 %s (函数)',
      array: '%s 不是一个有效的 %s',
      object: '%s 不是一个有效的 %s',
      number: '%s 不是一个有效的 %s',
      date: '%s 不是一个有效的 %s',
      boolean: '%s 不是一个有效的 %s',
      integer: '%s 不是一个有效的 %s',
      float: '%s 不是一个有效的 %s',
      regexp: '%s 不是一个有效的 %s',
      email: '%s 不是一个有效的 %s',
      url: '%s 不是一个有效的 %s',
      hex: '%s 不是一个有效的 %s'
    },
    string: {
      len: '%s 长度必须是 %s 个字符',
      min: '%s 长度至少是 %s 个字符',
      max: '%s 长度最多是 %s 个字符',
      range: '%s 长度必须介于 %s 至 %s 个字符之间'
    },
    number: {
      len: '%s 必须等于 %s',
      min: '%s 不能小于 %s',
      max: '%s 不能大于 %s',
      range: '%s 必须介于 %s 和 %s 之间'
    },
    array: {
      len: '%s 长度必须是 %s',
      min: '%s 长度不能小于 %s',
      max: '%s 长度不能大于 %s',
      range: '%s 长度必须介于 %s 和 %s 之间'
    },
    pattern: {
      mismatch: '%s 值 %s 无法匹配 %s'
    }
  }
}
