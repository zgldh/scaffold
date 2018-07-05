import _ from 'lodash'
import router from '@/router'

export const PARAMS_PAGE_SIZE = 'ps'
export const PARAMS_PAGE = 'p'
export const PARAMS_SEARCH_TERM = 't'
export const PARAMS_SEARCH_PREFIX = 's-'
export const PARAMS_SORT_COLUMN = 'sc'
export const PARAMS_SORT_DIRECTION = 'scd'

export const MARK_YES = '✓'
export const MARK_NO = '✗'

function normalizeValue(rawValue) {
  if (rawValue && rawValue.constructor === Boolean) {
    return rawValue ? MARK_YES : MARK_NO
  }
  return rawValue
}

export function updateSearchParams(fieldName, value) {
  return updateParams(PARAMS_SEARCH_PREFIX + fieldName, normalizeValue(value))
}

export function removeSearchParams(fieldName) {
  return removeByPrefix(PARAMS_SEARCH_PREFIX + fieldName)
}

export function getSearchParams(fieldName) {
  const rawValue = router.currentRoute.query[PARAMS_SEARCH_PREFIX + fieldName]

  if (rawValue === MARK_YES) {
    return true
  } else if (rawValue === MARK_NO) {
    return false
  }
  return rawValue
}

export function updateParams() {
  const payload = JSON.parse(JSON.stringify(router.currentRoute.query))
  let queries = {}
  if (arguments.length === 2) {
    queries[arguments[0]] = arguments[1]
  } else {
    queries = arguments[0]
  }
  _.forEach(queries, (data, key) => {
    if (data === null) {
      delete payload[key]
    } else {
      payload[key] = data
    }
  })
  router.push({
    path: router.currentRoute.path,
    query: payload
  })
}

export function removeByPrefix(searchPrefix) {
  const payload = JSON.parse(JSON.stringify(router.currentRoute.query))
  _.forEach(payload, (data, key) => {
    if (key.indexOf(searchPrefix) !== -1) {
      delete payload[key]
    }
  })
  router.push({
    path: router.currentRoute.path,
    query: payload
  })
}

export function hasPrefix(searchPrefix) {
  var itemFound = false
  _.forEach(router.currentRoute.query, (data, key) => {
    if (key.indexOf(searchPrefix) !== -1) {
      itemFound = true
    }
  })
  return itemFound
}
