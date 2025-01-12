<template>
  <div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
      <router-link :to="{name: 'Dashboard'}" class="logo">
<!--        <span><img :src="`${mainOrigin}assets/images/logo-svg.png`" alt="" height="80%" /> </span><i><img :src="`${mainOrigin}assets/images/logo-svg.png`" alt="" height="22" /></i>-->
      </router-link>
    </div>
    <nav class="navbar-custom">
      <ul class="navbar-right list-inline float-right mb-0">
        <!-- full screen -->
        <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
          <a class="nav-link waves-effect" href="#" id="btn-fullscreen"><i class="mdi mdi-fullscreen noti-icon"></i></a>
        </li>
        <!-- User Info -->
        <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
          <router-link to="#" style="padding: 0" class="nav-link waves-effect">
            {{ me.UserName }} ({{ me.UserId }})
          </router-link>
        </li>
        <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Level2'"><span class="badge badge-info">Field Force</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Level3'"><span class="badge badge-info">Zonal Head</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Level4'"><span class="badge badge-info">DGM</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Level5'"><span class="badge badge-info">NSM</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Portfolio'"><span class="badge badge-info">Portfolio</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='DirectorMarketing'"><span class="badge badge-info">Marketing Director</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='DED'"><span class="badge badge-info">HOB</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Distributor'"><span class="badge badge-info">Distributor</span></a>
          <a href="javascript:" class="nav-link waves-effect" v-if="me.UserLevel==='Admin'"><span class="badge badge-info">Admin</span></a>
        </li>
        <li class="dropdown notification-list list-inline-item">
          <div class="dropdown notification-list nav-pro-img">
            <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <img :src="`${mainOrigin}assets/images/avatar.png`" alt="user" class="rounded-circle" />
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
              <!-- item-->
              <a href="javascript:" class="dropdown-item">{{ me.UserId }}</a>
              <a class="dropdown-item text-danger" href="javascript:" @click="openUserModel"><i class="mdi mdi-tag-faces text-info"></i> Change Password </a>
              <a class="dropdown-item text-danger" href="javascript:" @click="logout"><i class="mdi mdi-power text-danger"></i> Logout</a>
            </div>
          </div>
        </li>
      </ul>
      <ul class="list-inline menu-left mb-0" id="menu-collapse">
        <li class="float-left">
          <button class="button-menu-mobile open-left waves-effect" @click="toggleSidebar"><i class="mdi mdi-menu"></i></button>
        </li>
      </ul>
    </nav>
      <div v-if="myModel">
          <transition name="model">
              <div class="modal-mask">
                  <div class="modal-wrapper">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h4 class="modal-title"> Update Password</h4>
                                  <button type="button" v-if="myModelclose == false"  @click="myModel=false" class="close"><span aria-hidden="true">&times;</span></button>
                              </div>
                              <div class="modal-body">
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label>User Id</label>
                                              <input type="text" class="form-control"  v-model="me.UserId" readonly/>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="form-group" style="margin:0">
                                    <ValidationProvider name="Password" mode="eager" rules="required|min:6" v-slot="{ errors }">
                                      <div class="form-group">
                                        <label for="user-password">Password</label>
                                        <input type="password" v-model="updatePassword" class="form-control" :class="{'error-border': errors[0]}"
                                               id="user-password" placeholder="Password" autocomplete>
                                        <small>Minimum eight characters, at least one letter, one number and one special character</small>
                                        <span class="error-message">{{ errors[0] }}</span>
                                      </div>
                                    </ValidationProvider>
                                  </div>
                                  <div class="form-group" style="margin:0">
                                      <label> Confirm Password</label>
                                      <input type="password" class="form-control" v-model="confirmUpdatePassword"  />
                                  </div>
                                  <br />
                                  <div align="center">
                                      <button class="btn btn-primary" @click="updateUserModel"> Update</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </transition>
      </div>
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
      baseurl:'',
      myModel:false,
      myModelclose:false,
      updatePassword:'',
      confirmUpdatePassword:''
    }
  },
  mounted() {
    this.checkUserUpdate()
    // $(".navbar-custom").metisMenu()
    // $('#side-menu').metisMenu({
    //   toggle: true
    // });
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  methods: {
      openUserModel(){
          this.myModel = true;
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
    },
    checkUserUpdate(){
        axios.get(baseurl + "api/user/check-user-update"  , this.config()).then(response => {
          this.userUpdate =response.data.data[0].LastPasswordUpdated
          if (this.userUpdate.length >0){
            this.myModel = true;
            this.myModelclose = true;
          }

        }).catch(e => {

        });
    },

    //Update User Password
      updateUserModel(){
        const pattern = /^(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;

        if (!pattern.test(this.updatePassword)) {
          this.errorNoti('Password must be at least 6 characters long and ' +
              ' one number,' +
              ' and one special character (@$!%*?&)');

          return false;
        }
        let url = 'user/password-change';
        this.axiosPost(url, {
          userId: this.me.UserId,
          updatePassword: this.updatePassword,
          confirmUpdatePassword: this.confirmUpdatePassword,
        }, (response) => {
          this.successNoti(response.message);
          this.myModel = false;
          this.logout();
        }, (error) => {
          this.errorNoti(error);
        })

      },
    toggleSidebar(e) {
      $("body").toggleClass("enlarged")
    },
  }
}
</script>

<style scoped>

</style>