import _ from 'lodash'
import querystring from 'querystring'

export function buildDataTablesParameters() {
  this.datatablesParameters.draw++
  this._draw = this.datatablesParameters.draw
  this.datatablesParameters._ = new Date().getTime()
  return this.datatablesParameters
}

export function UnifiedValue(value, dayEnd) {
  var result = value
  if (_.isDate(value)) {
    result = value.getFullYear() + '-' + (value.getMonth() + 1) + '-' + value.getDate()
    if (dayEnd) {
      result += ' 23:59:59'
    } else {
      result += ' ' + value.getHours() + ':' + value.getMinutes() + ':' + value.getSeconds()
    }
  }
  return result
}

function normalizeSearchValue(rawValue) {
  if (rawValue && rawValue.constructor === Boolean) {
    return rawValue ? 1 : 0
  }
  return rawValue
}

export function SerializerDatatablesParameters(params, url = '') {
  // In case this.resource contains columns
  const urlParams = querystring.parse(url)
  const len = _.get(urlParams, 'columns.length', 0)
  var parameters = { ...params }
  delete parameters.order
  delete parameters.columns
  delete parameters.search
  var columnMaxIndex = len
  params.columns.forEach((element, index) => {
    if (!element.data) {
      return
    }
    index += len
    columnMaxIndex++
    parameters['columns[' + index + '][data]'] = element.data
    parameters['columns[' + index + '][name]'] = element.name
    parameters['columns[' + index + '][label]'] = element.label || null
    parameters['columns[' + index + '][searchable]'] = element.searchable
    parameters['columns[' + index + '][orderable]'] = element.orderable
    parameters['columns[' + index + '][search][value]'] = _.get(element, 'search.value', null)
    parameters['columns[' + index + '][search][regex]'] = _.get(element, 'search.regex', false)
    if (element.search && element.search.hasOwnProperty('advance')) {
      for (var operator in element.search.advance) {
        if (element.search.advance.hasOwnProperty(operator)) {
          var value = element.search.advance[operator]
          if (Array.isArray(value)) {
            for (var valueIndex = 0; valueIndex < value.length; valueIndex++) {
              parameters['columns[' + index + '][search][advance][' + operator + '][' + valueIndex + ']'] = normalizeSearchValue(value[valueIndex])
            }
          } else {
            parameters['columns[' + index + '][search][advance][' + operator + ']'] = normalizeSearchValue(element.search.advance[operator])
          }
        }
      }
    }
  })
  params.order.forEach((element, index) => {
    var columnIndex = isNaN(element.column) ? params.columns.findIndex(column => {
      return column.data === element.column
    }) : parseInt(element.column)

    if (columnIndex === -1) {
      parameters['columns[' + columnMaxIndex + '][data]'] = element.column
      parameters['columns[' + columnMaxIndex + '][name]'] = element.column
      parameters['columns[' + columnMaxIndex + '][orderable]'] = true
      columnIndex = columnMaxIndex
      columnMaxIndex++
    }

    parameters['order[' + index + '][column]'] = columnIndex
    parameters['order[' + index + '][dir]'] = element.dir || 'asc'
  })
  parameters['search[value]'] = _.get(params, 'search.value', null)
  parameters['search[regex]'] = _.get(params, 'search.regex', false)
  var parameterString = []
  for (var key in parameters) {
    if (parameters.hasOwnProperty(key)) {
      if ([undefined, null, ''].indexOf(parameters[key]) === -1) {
        parameterString.push(encodeURIComponent(key) + '=' + encodeURIComponent(parameters[key]))
      } else {
        parameterString.push(encodeURIComponent(key) + '=')
      }
    }
  }
  parameterString = parameterString.join('&')
  return parameterString
}
