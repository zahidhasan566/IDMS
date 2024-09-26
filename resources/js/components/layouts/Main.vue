<template>
  <div id="wrapper">
    <Topbar v-if="!$route.meta.hideTopBar" />
    <Leftbar v-if="!$route.meta.hideSideMenu" />
    <div class="content-page">
      <div class="content">
        <router-view></router-view>
        <Footer/>
      </div>
    </div>
  </div>
</template>
<script>
import Topbar from './Topbar.vue'
import Leftbar from './Leftbar'
import Footer from './Footer'

export default {
  components: {
    Topbar,
    Leftbar,
    Footer
  },
  mounted() {
    // setTimeout(() => {
    //   $("#side-menu").metisMenu();
    // },1000)
  }
}
</script>
