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
                    <ValidationProvider name="Staff ID" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label>User ID <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="userId" placeholder="User ID" :disabled="actionType==='edit'" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="User Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">User Name <span class="error">*</span></label>
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
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Role" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="status">Role <span class="error">*</span></label>
                        <select class="form-control" v-model="role">
                          <option value="">Select</option>
                          <option :value="role.RoleId" v-for="(role,i) in roles" :key="i">{{ role.RoleName }}</option>
                        </select>
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
      roles: [],
      role: '',
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
        this.selectedBusiness = [];
        this.selectedDepartment = [];
        this.axiosGet('user/get-user-info/'+row.UserId,(response) => {
          let user = response.data;
          this.title = 'Update User';
          this.buttonText = "Update";
          this.userId = user.UserId;
          this.userName = user.UserName;
          this.designation = user.Designation;
          this.role = user.RoleId;
          this.buttonShow = true;
          this.actionType = 'edit';
        },(error) => {

        });
      } else {
        this.title = 'Add User';
        this.buttonText = "Add";
        this.userId = '';
        this.userName = '';
        this.designation = '';
        this.role = '';
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
      this.axiosGet('roles/all',function (response) {
        instance.roles = response.data;
      },function (error) {

      });
    },
    changeBusiness(value) {
      this.business = value
      this.filteredSubMenu = this.checkSubMenus()
    },
    checkSubMenus() {
      let data = this.allSubMenu
      let filtered = []
      if (this.business.Business == '94' || this.business.Business == '97') {
        data.forEach(function(item) {
          filtered.push(item);
        });
      } else {
        data.forEach(function(item) {
          if (item.MenuID !== 'Finance') {
            filtered.push(item);
          }
        });
      }
      return filtered;
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'user/add';
      else url = 'user/update'
      this.axiosPost(url, {
        userId: this.userId,
        userName: this.userName,
        designation: this.designation,
        role: this.role,
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
