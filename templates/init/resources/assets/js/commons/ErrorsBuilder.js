let ErrorsBuilder = function () {
  return {
    $_errors: {},
    get: function (key) {
      if (this.$_errors.hasOwnProperty(key)) {
        var errors = this.$_errors[key];
        if (errors.constructor == Array) {
          return errors.reduce((previous, current) => {
            return previous + "\n" + current;
          }, '');
        }
        return this.$_errors[key];
      }
    },
    set: function (key, error) {
      this.$_errors[key] = error;
    },
    /**
     * Set Laravel 422 errors
     * @param errors
     */
    setAll: function (errors, noFocus) {
      Object.keys(errors).map(function (key, index) {
        this.set(key, errors[key]);
      }.bind(this), 0);
    },
    remove: function (key) {
      unset(this.$_errors[key]);
    },
    has: function (key) {
      return this.$_errors.hasOwnProperty(key);
    },
    removeAll: function () {
      this.$_errors = {};
    },
    focusFirstErrorField: function (className) {
      setTimeout(function () {
        className = className ? className : 'has-error';
        var div = document.getElementsByClassName(className)[0];
        if (div) {
          var input = div.getElementsByTagName('input')[0];
          if (input) {
            input.focus();
          }
        }
      }, 1);
    }
  };
};

export default ErrorsBuilder;