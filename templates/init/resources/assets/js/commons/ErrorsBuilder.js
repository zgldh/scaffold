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
    remove: function (key) {
      unset(this.$_errors[key]);
    },
    has: function (key) {
      return this.$_errors.hasOwnProperty(key);
    },
    removeAll: function () {
      this.$_errors = {};
    }
  };
};

export default ErrorsBuilder;