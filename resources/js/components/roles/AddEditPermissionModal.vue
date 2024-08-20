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
                    <ValidationProvider name="Role" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="roleId">Select Role <span class="error">*</span></label>
                        <select id="roleId" class="form-control" v-model="roleId" :disabled="actionType === 'edit'">
                          <option :value="role.RoleId" v-for="(role,i) in roles">{{ role.RoleName }}</option>
                        </select>
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
  data() {
    return {
      title: '',
      roleId: '',
      filteredSubMenu: '',
      buttonText: '',
      actionType: '',
      buttonShow: true,
      allSubMenuId: []
    }
  },
  props: ['roles'],
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-user', (row) => {
      if (row) {
        this.axiosGet('role-permission/get-role-info/'+row.RoleId,(response) =>{
          let role = response.data;
          this.title = 'Update Role';
          this.buttonText = "Update";
          this.roleId = row.RoleId;
          role.forEach((item) => {
            this.allSubMenuId.push(item.SubMenuID)
          });
          this.buttonShow = true;
          this.actionType = 'edit';
        },(error) => {

        });
      } else {
        this.title = 'Add Role';
        this.buttonText = "Add";
        this.roleId = '';
        this.actionType = 'add'
      }
      this.getData()
      $("#add-edit-dept").modal("toggle");
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
  },
  methods: {
    getData() {
      this.axiosGet('role-permission/modal-data',(response) => {
        this.filteredSubMenu = response.allSubMenus
      })
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
      if (this.actionType === 'add') url = 'role-permission/create';
      else url = 'role-permission/update/'+this.roleId
      this.axiosPost(url, {
        roleId: this.roleId,
        selectedSubMenu: this.allSubMenuId
      }, (response) => {
        this.successNoti(response.message);
        $("#add-edit-dept").modal("toggle");
        bus.$emit('refresh-datatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })
    }
  }
}
</script>

<style>
.permission-grp {
  padding: 10px;
  border: 1px solid #e2e2e2;
  border-radius: 5px;
}
</style>