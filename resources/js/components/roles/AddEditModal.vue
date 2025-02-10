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
                    <ValidationProvider name="Role ID" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="roleId">Role ID <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="roleId" placeholder="Role ID" id="roleId" :disabled="actionType === 'edit'">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Role Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="roleName">Role Name <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="roleName"
                               v-model="roleName" placeholder="Role Name">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-12">
                    <ValidationProvider name="Description" mode="eager" rules="max:100" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="designation">Designation </label>
                        <textarea rows="3" class="form-control" v-model="roleDescription"></textarea>
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
  data() {
    return {
      title: '',
      roleId: '',
      roleName: '',
      roleDescription: '',
      buttonText: '',
      actionType: '',
      buttonShow: true,
    }
  },
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-user', (row) => {
      if (row) {
        let instance = this;
        this.axiosGet('roles/get-role-info/'+row.RoleId,(response) =>{
          let role = response.data;
          instance.title = 'Update Role';
          instance.buttonText = "Update";
          instance.roleId = role.RoleId;
          instance.roleName = role.RoleName;
          instance.roleDescription = role.RoleDescription;
          instance.buttonShow = true;
          instance.actionType = 'edit';
        },(error) => {

        });
      } else {
        this.title = 'Add Role';
        this.buttonText = "Add";
        this.roleId = '';
        this.roleName = '';
        this.roleDescription = '';
        this.actionType = 'add'
      }
      $("#add-edit-dept").modal("toggle");
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
  },
  methods: {
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'roles/create';
      else url = 'roles/update/'+this.roleId
      this.axiosPost(url, {
        roleId: this.roleId,
        roleName: this.roleName,
        roleDescription: this.roleDescription
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