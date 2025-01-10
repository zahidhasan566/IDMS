<template>
    <div id="rat-page">
        <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
                            Close
                        </button>
                    </div>
                    <ValidationObserver v-slot="{ handleSubmit }">
                        <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction" @submit.prevent="handleSubmit(onSubmit)"
                              @keydown.enter="$event.preventDefault()">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="User Id" mode="eager" rules="required"
                                                            v-slot="{ errors }">

                                                <label for="userId">User Id <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="userId"
                                                       data-required="true"
                                                       v-model="userId" name="User Id" placeholder="User Id">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                      <ValidationProvider name="Customer Code" mode="eager" rules="required"
                                                          v-slot="{ errors }">
                                        <label for="customerCode">Customer Code <span class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               id="customerCode"
                                               data-required="true"
                                               v-model="customerCode" name="Customer Code" placeholder="Customer Code">
                                        <span class="error-message"> {{ errors[0] }}</span>
                                      </ValidationProvider>

                                    </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="Region" mode="eager" rules="required"
                                                        v-slot="{ errors }">

                                      <label for="Region">Region Name <span class="error">*</span></label>
                                      <input type="text" class="form-control"
                                             id="region"
                                             data-required="true"
                                             v-model="region" name="region" placeholder="Region">
                                      <span class="error-message"> {{ errors[0] }}</span>

                                    </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="User Type" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="userType">User Type<span class="error">*</span></label>
                                            <select class="form-control" name="userType"
                                                    v-model="userType">
                                                <option value="">Select</option>
                                                <option :value="singleCustomer.RoleId" v-for="(singleCustomer , index) in roles"
                                                        :key="index">{{ singleCustomer.RoleName }}</option>
                                              </select>

                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="active" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="tagNo">Active<span class="error">*</span></label>
                                            <select class="form-control" name="active"
                                                    v-model="active">
                                               <option value="">Select</option>
                                                <option value="Y">Yes</option>
                                                <option value="N">No</option>
                                            </select>
                                          <span class="error-message"> {{ errors[0] }}</span>

                                        </ValidationProvider>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: end;margin-top:10px">
                                <submit-form v-if="buttonShow" :name="buttonText"/>
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
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            roles: [],
            userId:'',
            customerCode:'',
            region:'',
            userType:'',
            active:'',

        }
    },
    computed: {},
    created() {},
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-user-customer', (row) => {
        {
                this.title = 'Add User Customer';
                this.buttonText = "Add";
                this.transferNo = '';

                this.status = '';
                this.buttonShow = true;
                this.actionType = 'add'
            }
            $("#add-edit-dept").modal("toggle");
        })

      this.getAllRoles()
    },
    destroyed() {
        bus.$off('add-edit-user-customer')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
      getAllRoles() {
        this.axiosGet('jobCard/get-all-roles',(response) => {
          console.log(response.data)
          this.roles = response.data
        })
      },
        onSubmit() {
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'jobCard/user-customer/store';
                }

                this.axiosPost(submitUrl, {
                    userId: this.userId,
                    customerCode: this.customerCode,
                    region: this.region,
                    userType: this.userType,
                    active: this.active,
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

<style src="vue-multiselect/dist/vue-multiselect.min.css">
.card-header {
    background: linear-gradient(269deg, rgb(0 0 0), #007bffb8) !important;
}
</style>
<style>
.datepicker .vue-input, .date-range-picker .vue-input, .timepicker .vue-input, .datetime-picker .vue-input {
    padding: 7px !important;
}
</style>
