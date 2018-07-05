<template>
  <transition name="back-top-fade">
    <div class="scroll-to-top"
         :class="{ 'hover': hover }"
         :style="'right:'+right+';bottom:'+bottom+';'"
         v-show="showBackToTop"
         @mouseenter="hover = true"
         @mouseleave="hover = false"
         @click="toTop">
      <slot>
        <i class="el-icon-caret-top"></i>
      </slot>
    </div>
  </transition>
</template>

<script type="javascript">
  export default {
    name: 'scroll-top-top',
    props: {
      scrollBox: { // 滚动条所在的DOM对象或其选择器
        type: [String, Object],
        required: false,
        default: '.app-main'
      },
      animationTime: { // 滚动到顶部的动画时间，单位为毫秒，100 到 200 之间。默认值为 150
        type: Number,
        required: false,
        default: 150,
        validator (val) {
          return val >= 100 && val <= 200
        }
      },
      right: { // 距离右边多远
        type: String,
        required: false,
        default: "100px",
      },
      bottom: { // 距离下边多远
        type: String,
        required: false,
        default: "100px",
      },
    },
    data() {
      return {
        hover: false,
        showBackToTop: false,
        componentScrollBox: null,
        intervalTime: 10,
        scrollToTopTimer: null,
      }
    },
    mounted(){
      this.componentScrollBox = this.getScrollBoxElement();
      this.setupScrollBox();
    },
    beforeDestroy() {
      this.unsetScrollBox();
    },
    watch: {
      scrollBox(newValue){
        this.unsetScrollBox();
        this.componentScrollBox = this.getScrollBoxElement();
        this.setupScrollBox();
      }
    },
    methods: {
      getScrollBoxElement(){
        var element = null;
        if (this.scrollBox) {
          if (this.scrollBox.constructor === String) {
            element = window.document.querySelector(this.scrollBox);
          } else {
            element = this.scrollBox;
          }
        } else {
          element = this.$el.parentElement;
        }
        return element;
      },
      setupScrollBox(){
        this.throttledScrollHandler = _.throttle(this.handleScroll, 300);
        this.componentScrollBox.addEventListener('scroll', this.throttledScrollHandler);
        this.componentScrollBox.addEventListener('wheel', this.handleWheel);
      },
      unsetScrollBox(){
        this.componentScrollBox.removeEventListener('scroll', this.throttledScrollHandler);
        this.componentScrollBox.removeEventListener('wheel', this.handleWheel);
      },
      toTop() {
        this.hover = false;
        this.showBackToTop = false;
        this.startScrollToTop();
      },
      handleWheel(e){
        if (this.scrollToTopTimer) {
          clearInterval(this.scrollToTopTimer)
          this.scrollToTopTimer = null
        }
      },
      handleScroll(e) {
        const scrollTop = this.componentScrollBox.scrollTop;
        this.showBackToTop = scrollTop >= 0.5 * document.body.clientHeight;
        if (this.showHeader !== this.scrollTop > scrollTop) {
          this.showHeader = this.scrollTop > scrollTop;
        }
        if (scrollTop === 0) {
          this.showHeader = true;
        }
        this.scrollTop = scrollTop;
      },
      startScrollToTop(){
        if (this.componentScrollBox) {
          let scrollTop = this.componentScrollBox.scrollTop
          let speed = this.intervalTime / this.animationTime
          this.scrollToTopTimer = setInterval(() => {
            if (this.componentScrollBox) {
              scrollTop -= scrollTop * speed
              if (scrollTop <= 1) {
                scrollTop = 0
                this.clearTimer()
              }
              this.componentScrollBox.scrollTop = scrollTop
            } else {
              this.clearTimer()
            }
          }, this.intervalTime)
        }
      },
      clearTimer(){
        if (this.scrollToTopTimer !== Number.MIN_VALUE) {
          clearInterval(this.scrollToTopTimer)
          this.scrollToTopTimer = Number.MIN_VALUE
        }
        if (this.addScrollListenerTimeout !== Number.MIN_VALUE) {
          clearTimeout(this.addScrollListenerTimeout)
          this.addScrollListenerTimeout = Number.MIN_VALUE
        }
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables.scss";

  .scroll-to-top {
    background-color: white;
    position: fixed;
    size: 40px;
    width: 40px;
    border-radius: 20px;
    cursor: pointer;
    transition: .3s;
    box-shadow: 0 0 6px rgba(0, 0, 0, .12);
    z-index: 5;
    i {
      color: $blue;
      display: block;
      line-height: 40px;
      text-align: center;
      font-size: 18px;
    }
    &.hover {
      opacity: 1;
    }
  }

  .back-top-fade-enter,
  .back-top-fade-leave-active {
    transform: translateY(-30px);
    opacity: 0;
  }

</style>
