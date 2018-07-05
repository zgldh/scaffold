import i18n from '@/lang'
import user from '@/store/modules/user.js'
import { MessageBox } from 'element-ui'

export function SplitModelAction(permission) {
  if (permission.constructor !== String) {
    throw new Error('permission should be a string like Model@action')
  }
  return permission.split('@')
}

export function FindPermissionIndex(permissionName) {
  if (permissionName.constructor !== String) {
    throw new Error('permissionName should be a string like Model@action')
  }
  const sort = ['@index', '@show', '@store', '@update', '@destroy']
  var index = sort.findIndex(term => {
    return _.endsWith(permissionName, term)
  })
  if (index === -1) {
    index = 999
  }
  return index
}

export function SortPermission(permissions) {
  permissions.sort((p1, p2) => {
    var p1Model = p1.name.split('@')[0]
    var p2Model = p2.name.split('@')[0]
    if (p1Model === p2Model) {
      return FindPermissionIndex(p1.name) - FindPermissionIndex(p2.name)
    } else {
      return p1Model > p2Model ? 1 : -1
    }
  })
  return permissions
}

export function IsDefaultAction(actionName) {
  return user.state.defaultActions.indexOf(actionName) >= 0
}

export function ModelLang(modelName) {
  if (modelName) {
    return i18n.t(modelName + '.title')
  } else {
    return i18n.t('components.permission_matrix.model_row.other')
  }
}

export function PermissionLang(permissionName) {
  var model, action;
  [model, action] = SplitModelAction(permissionName)
  model = _.snakeCase(model)
  action = _.snakeCase(action)
  if (IsDefaultAction(action)) {
    return i18n.t('permission.default_actions.' + action)
  }
  return i18n.t(model + '.permissions.' + action)
}

export function PermissionFullLang(permissionName) {
  var model, action;
  [model, action] = SplitModelAction(permissionName)
  model = _.snakeCase(model)
  action = _.snakeCase(action)

  var modelLang = ModelLang(model)

  if (IsDefaultAction(action)) {
    return modelLang + ':' + i18n.t('permission.default_actions.' + action)
  }
  return modelLang + ':' + i18n.t(model + '.permissions.' + action)
}

export function RoleCopyDialog(oldRole) {
  return MessageBox.prompt(i18n.t('messages.role_copy.text'),
    i18n.t('messages.role_copy.title', { label: oldRole.label }),
    {
      confirmButtonText: i18n.t('global.terms.confirm'),
      cancelButtonText: i18n.t('global.terms.cancel'),
      inputPattern: /^[a-zA-Z]+[a-zA-Z0-9\-_]*$/,
      inputErrorMessage: i18n.t('messages.role_copy.input_error_message'),
      inputValue: 'copy-' + oldRole.name
    })
}
