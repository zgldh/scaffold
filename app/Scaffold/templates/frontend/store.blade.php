<?php
/**
 * @var $moduleNameSpace  Name/Space/Haha
 * @var $moduleName  someThingLikeThis
 */
?>
/**
 * You should import APIs which you need. Just like:
 * import { RoleIndex } from '@/api/user'
 **/

const {{$moduleName}} = {
  namespaced: true,
  state: {
    /**
     State can hold some common data like enum items or values cross pages.
     list: []
     **/
  },
  getters: {},
  mutations: {
    /**
     Mutation function name should be SNAKE_CASE in uppercase.
     setList: (state, list) => {
        state.list = list
      }
     **/
  },
  actions: {
    /**
     Actions should be able to async if it calls APIs.
     async LoadList({ commit, state }) {
        if (state.list.length) {
          return state.list
        } else {
          var response = await RoleIndex()
          commit('setList', response.data)
          return response.data
        }
      }
     **/
  }
}

export default {{$moduleName}}
