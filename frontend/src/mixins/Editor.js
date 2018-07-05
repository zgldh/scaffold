import EditorTitle from '@/components/EditorTitle'
import _ from 'lodash'

var editorMixin = {
  components: {
    EditorTitle
  },
  data() {
    return {
      loading: false
    }
  },
  computed: {
    isCreating() {
      return this.$route.path.indexOf('/create') >= 0
    }
  },
  methods: {
    errorHandler(response) {
      this.loading = false
      const status = _.get(response, 'status')
      if (status === 422) {
        const errors = _.get(response, 'data.error.errors')
        _.forEach(errors, (data, key) => {
          // this.$refs.form.
          var errorMessage = data[0]
          var formItem = findEleFormItem(this.$refs.form.$children, key)
          if (formItem) {
            formItem._data.validateMessage = errorMessage
            formItem._data.validateState = 'error'
            // formItem.error = data[0]
          }
        })
      }
    }
  }
}

function findEleFormItem(items, fieldName) {
  return items.find(item => {
    return fieldName === item.prop
  })
}

export default editorMixin
