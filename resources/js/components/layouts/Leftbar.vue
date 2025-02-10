<template>
  <div class="left side-menu">
      <div style="width: 200px;
    position: fixed;
    bottom: 0;
    top: 70px;
    margin-top: 0;">
    <div class="slimscroll-menu" id="remove-scroll">
      <!--- Sidemenu -->
      <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu" id="side-menu">
          <li class="menu-title">Main</li>
          <li>
            <router-link :to="{name: 'Dashboard'}" class="waves-effect" role="button">
              <i class="ti-home"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li v-for="(menu,index) in menus" :key="index" v-if="menu.sub_menus.length > 0">
            <router-link :to="`${baseUrl}${menu.MenuLink === '#' ? '?':menu.MenuLink}`" class="waves-effect" :id="`menu${index}`" role="button">
              <i :class="`${menu.MenuIcon}`"></i>
              <span>
                {{ menu.MenuName }}
              </span>
              <span class="float-right menu-arrow" v-if="menu.sub_menus.length > 0"><i
                  class="mdi mdi-chevron-right"></i></span>
            </router-link>
            <ul class="submenu" v-if="menu.sub_menus.length > 0">
              <li v-for="(subMenu,index2) in menu.sub_menus" :key="index2">
                <router-link :to="`${baseUrl+subMenu.SubMenuLink}`">
                  <span>{{ subMenu.SubMenuName }}</span>
                </router-link>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <!-- Sidebar -->
      <div class="clearfix"></div>
    </div>
    </div>
    <!-- Sidebar -left -->
  </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";

export default {
  mixins: [Common],
  data() {
    return {
      authUser: {},
      menus: [],
      baseUrl: baseurl
    }
  },
  mounted() {
    setTimeout(() => {
      $("#side-menu").metisMenu();
    },2000)
    this.getData();
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  methods: {
    getData() {
      this.axiosGet('app-supporting-data', (response) => {
        this.menus = response.menus;
        this.$store.commit('me', response.user);
      }, (error) => {
        this.errorNoti(error)
      })
    },
    logout() {
      this.axiosPost("logout", {}, (response) => {
            localStorage.setItem("token", "");
            this.$router.push(this.mainOrigin + "login");
            this.successNoti(response.message)
          },
          (error) => {
            this.errorNoti(error);
          }
      );

    }
  }
}
</script>

<style scoped>
.topnav {
  margin-top: 0;
}

.pad-cus {
  padding: 5px 20px;
  color: #ffffff;
  margin: 0 15px;
  font-size: 10px;
  border-radius: 5px;
  border: 1px solid #ffffff;
}

.pad-cus-badge {
  padding: 10px 20px;
  margin: 0 10px 0 0;
  font-weight: bold;
  letter-spacing: 0.09em;
}

.logout {
  border-radius: 5px;
  margin-left: 5px;
  border: 1px solid #ffffff;
  background: #333547;
  color: #ffffff;
  font-size: 17px;
  letter-spacing: 0.09em;
}
</style>
