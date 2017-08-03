import _ from 'lodash';
import {BuildHttpRequestPayload} from './Utils.js';

export var mixin = {
  data: function () {
    return {
      errors: {},
      missingErrors: [],
      saving: false,
      loading: false
    };
  },
  beforeMount: function () {
  },
  mounted: function () {
  },
  methods: {
    _onSave: function (event) {
      return new Promise((resolve) => {
        this.$refs.form.validate(valid => {
          if (valid) {
            this.saving = true;
            this.errors = {};
            this.missingErrors = [];
            let payload = BuildHttpRequestPayload(this.form);
            if (this.form.id) {
              payload.append('_method', 'put');
            }
            let require = axios.post(this.resource, payload);
            require.then(result => {
              this.$message({
                type: 'success',
                message: "保存完毕"
              });
              this.saving = false;
              resolve(result);
            }).catch(({response}) => {
              if (response.status == 422) {
                // this.$refs.form.$children
                this._distributeErrorMessages(response.data);
              }
              console.log('error', this.$refs.form, response);
              this.saving = false;
              reject(response);
            })
          }
          else {
            reject('Invalid');
          }
        });
      });
    },
    _distributeErrorMessages(errors){
      this.missingErrors = [];
      let firstField = null;
      _.forEach(errors, (errorArray, fieldName) => {
        let field = FindFieldComponent(fieldName, this.$refs.form);
        if (!firstField) {
          firstField = field;
        }
        if (field) {
          this.errors[fieldName] = errorArray[0];
        }
        else {
          if (!_.isArray(errorArray)) {
            errorArray = [errorArray];
          }
          this.missingErrors.push.apply(this.missingErrors, errorArray);
        }
      });

      if (this.missingErrors.length) {
        this.$el.scrollIntoView({block: "start", behavior: "smooth"});
      }
      else {
        firstField.$el.scrollIntoView({block: "start", behavior: "smooth"});
      }
    }
  }
};

function FindFieldComponent (fieldName, form) {
  let field = null;
  for (let i = 0; i < form.$children.length; i++) {
    field = form.$children[i];
    if (field.prop === fieldName) {
      break;
    }
  }
  return field;
}