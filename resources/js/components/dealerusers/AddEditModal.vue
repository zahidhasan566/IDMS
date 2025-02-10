<template>
  <div>
    <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="User Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">Name <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="name"
                               v-model="userName" placeholder="User Name">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Designation" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="designation">Designation <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="designation"
                               v-model="designation" placeholder="Designation">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="password" mode="eager" rules="required|min:6"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">Password <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="password"
                               v-model="password" name="password" placeholder="Password">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="confirm" mode="eager" rules="required|min:6|confirmed:password"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="confirm">Confirm <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="confirm"
                               v-model="confirm"
                               name="confirm" placeholder="Confirm Password">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12">
                    <p class="font-weight-bold">Submenu Permission</p>
                    <div class="form-group">
                      <input type="checkbox" @change="markToggle">
                      <span>Mark All</span>
                    </div>
                  </div>
                  <div class="col-12 col-md-6" v-for="(submenu,index) in filteredSubMenu" :key="index">
                    <div class="form-group permission-grp">
                      <div class="form-check">
                        <p>{{submenu.MenuName}}</p>
                        <div v-for="(sub,index2) in submenu.all_sub_menus" :key="index2">
                          <input class="form-check-input" type="checkbox" :value="sub.SubMenuID" :checked="sub.checked" v-model="allSubMenuId" :id="'allSubMenu'+index+'-'+index2">
                          <label class="form-check-label" :for="'allSubMenu'+index+'-'+index2">
                            {{sub.SubMenuName}}
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <submit-form v-if="buttonShow" :name="buttonText"/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import {mapGetters} from "vuex";

export default {
  mixins: [Common],
  // components: {Multiselect},
  data() {
    return {
      title: '',
      userId: '',
      userName: '',
      designation: '',
      password: '',
      confirm: '',
      filteredSubMenu: [],
      allSubMenuId: [],
      buttonText: '',
      type: 'add',
      actionType: '',
      buttonShow: true
    }
  },
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-user', (row) => {
      if (row) {
        this.axiosGet('user/get-user-info/'+row.UserId,(response) => {
          let user = response.data;
          this.title = 'Update Dealer User';
          this.buttonText = "Update";
          this.userId = user.UserId;
          this.userName = user.UserName;
          this.designation = user.Designation;
          response.allSubMenu.forEach((item) => {
            this.allSubMenuId.push(item.SubMenuID)
          });
          this.buttonShow = true;
          this.actionType = 'edit';
        },(error) => {

        });
      } else {
        this.title = 'Add Dealer User';
        this.buttonText = "Add";
        this.userId = '';
        this.userName = '';
        this.designation = '';
        this.password = '';
        this.actionType = 'add'
      }
      this.getData();
      $("#add-edit-dept").modal("toggle");
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosGet('role-permission/modal-data',function (response) {
        instance.filteredSubMenu = response.allSubMenus;
      },function (error) {

      });
    },
    markToggle(e) {
      if (e.target.checked) {
        this.allSubMenuId = []
        this.filteredSubMenu.map((menu) => {
          menu.all_sub_menus.map((sub) => {
            this.allSubMenuId.push(sub.SubMenuID)
          })
        })
      } else {
        this.allSubMenuId = []
      }
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'dealer/user/create';
      else url = 'dealer/user/update/'+this.userId
      this.axiosPost(url, {
        userName: this.userName,
        designation: this.designation,
        menus: this.allSubMenuId,
        password: this.password
      }, (response) => {
        this.successNoti(response.message);
        $("#add-edit-dept").modal("toggle");
        bus.$emit('refresh-datatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })
    },
    changeUserType(val) {
      if (val === null) {
        this.allowedVisible = false
      } else if (val.RoleID === 'RepresentativeUser') {
        this.allowedVisible = true
      } else {
        this.allowedVisible = false
      }
    },
    loadFromHR(e) {
      var staffId = e.target.value;
      let instance = this;
      this.axiosGet('user/hr-data?staffId='+staffId,function (response){
        if (response.data.length === 0) {
          instance.staffName = ""
          instance.designation = ""
          instance.department = ""
          instance.deptCode = ""
          instance.buttonShow = false
          instance.errorNoti('No staff found with this staff ID!')
        } else {
          instance.staffName = response.data.Name
          instance.designation = response.data.DesgName
          instance.department = response.data.DeptName
          instance.deptCode = response.data.DeptCode
          instance.buttonShow = true
        }
      },function (error){

      });
    },
    changeAllowedBusiness(row) {
      let all = row.filter(function (item) {
        return item.Business === 'All'
      })
      if (all.length > 0) {
        this.selectedBusiness = all
      }
    },
    changeAllowedDepartment(row) {
      let all = row.filter(function (item) {
        return item.DeptCode === 'All'
      })
      if (all.length > 0) {
        this.selectedDepartment = all
      }
    },
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
