<template>
    <el-input class="password-input" :type="type" auto-complete="off"
              :placeholder="placeholder" v-model="password" :autofocus="autofocus">
        <auto-icon slot="suffix" :icon-class="iconClass" @click="onToggleButtonClick"/>
    </el-input>
</template>

<script type="javascript">
  const TYPE_MAP = {
    'text': 'password',
    'password': 'text'
  }

  export default {
    name: 'password-input',
    props: {
      value: {
        required: true
      },
      toggleButton: {
        type: Boolean,
        default: true
      },
      placeholder: {
        type: String
      },
      autofocus: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        password: '',
        type: 'password'
      }
    },
    computed: {
      iconClass() {
        return this.type === 'password' ? 'ion-md-eye' : 'ion-md-eye-off';
      }
    },
    mounted() {
      this.password = this.value;
    },
    watch: {
      value(newValue) {
        this.password = newValue;
      },
      password(newValue) {
        this.$emit('input', newValue);
      }
    },
    methods: {
      onToggleButtonClick() {
        this.type = TYPE_MAP[this.type];
      }
    }
  }
</script>

<style lang="scss" scoped>
    .password-input {
    }
</style>
