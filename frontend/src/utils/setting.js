import i18n from '@/lang'

export default function setting_name(bundle, name) {
  var langTerm = 'setting.bundles.' + bundle + '.' + name
  var result = i18n.t(langTerm)
  if (result.hasOwnProperty('_name')) {
    result = result._name
  }
  return result
}
