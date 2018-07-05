import { Message, MessageBox } from 'element-ui'
import i18n from '@/lang'
import _ from 'lodash'

export function SuccessMessage(message) {
  return (parameters) => {
    Message({
      message: message,
      type: 'success',
      duration: 3 * 1000
    })
  }
}

export function ErrorMessage(message) {
  return (parameters) => {
    Message({
      message: message,
      type: 'error',
      duration: 3 * 1000
    })
  }
}

export function Error422(err) {
  if (_.get(err, 'status') !== 422) {
    throw err
  }
  var message = _.get(err, 'data.error.errors')
  message = _.map(message, item => item).join('<br>')
  return Message({
    message: message,
    type: 'error',
    dangerouslyUseHTMLString: true,
    duration: 3 * 1000
  })
}

export function DeleteConfirm(name, action, withSuccessMessage) {
  if (withSuccessMessage === undefined) {
    withSuccessMessage = true
  }
  let promise = MessageBox.confirm(i18n.t('messages.delete_confirm.text', { name }),
    i18n.t('messages.delete_confirm.title'),
    {
      confirmButtonText: i18n.t('messages.delete_confirm.confirm'),
      cancelButtonText: i18n.t('messages.delete_confirm.cancel'),
      type: 'warning'
    })
  if (action) {
    promise = promise.then(() => {
      return action()
    })
  }
  if (withSuccessMessage) {
    promise = promise.then(() => {
      Message({
        type: 'success',
        message: i18n.t('messages.delete_confirm.success_text', { name })
      })
    })
  }
  return promise
}

export function TextCopyDialog(message) {
  const copyInputId = 'cpipt-' + new Date().getTime()
  setTimeout(() => {
    if (window) {
      var input = window.document.querySelector('#' + copyInputId)
      if (input) {
        input.select()
      }
    }
  }, 100)
  return MessageBox.alert('<p>' + i18n.t('messages.text_copy.text') + '</p>' +
    '<input id="' + copyInputId + '" type="text" value="' + message + '" autofocus onclick="javascript:this.select()" ' +
    'style="width: 100%;padding: .5em;border-radius: 4px;border: 1px solid #dcdfe6;color:#606266"/>', i18n.t('messages.text_copy.title'), {
    dangerouslyUseHTMLString: true,
    closeOnClickModal: true,
    closeOnPressEscape: true
  })
}
