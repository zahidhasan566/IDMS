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
                                    <div class="col-md-3">
                                        <ValidationProvider name="Dealer" mode="eager" v-slot="{ errors }"   rules="required">
                                            <div class="form-group">
                                                <label>Dealer <span class="error">*</span></label>
                                                <select name="dealerCode" class="form-control" v-model="dealerCode" style="margin: 0">
                                                    <option :value="singleCustomer.CustomerCode" v-for="(singleCustomer , index) in customers"
                                                            :key="index">{{ singleCustomer.CustomerCode }}-{{ singleCustomer.CustomerName }}</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="bayName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">

                                                <label for="tagNo">Bay Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="bayName"
                                                       data-required="true"
                                                       v-model="bayName" name="bayName" placeholder="Bay Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                            <div class="form-group">

                                                <label for="tagNo">Comments</label>
                                                <input type="text" class="form-control"
                                                       id="bayName"
                                                       data-required="true"
                                                       v-model="comments" name="Comments" placeholder="Comments">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="active" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="tagNo">Active<span class="error">*</span></label>
                                            <select class="form-control" name="active"
                                                    v-model="active">
                                                <option value="Y">Yes</option>
                                                <option value="N">No</option>
                                            </select>

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
            bayName:'',
            comments:'',
            active:'',
            bayCode:'',
            dealerCode:'',
            customers:[]

        }
    },
    computed: {},
    created() {},
    mounted() {
        this.getSupportingData()
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-jobCard-bay', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('jobCard/get/bay/modal/' + row.BayCode +'/'+ row.ServiceCenterCode, function (response) {
                    instance.title = 'Update Bay List';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if(response.existingBayInfo){
                        instance.dealerCode = response.existingBayInfo.ServiceCenterCode
                        instance.bayCode= response.existingBayInfo.BayCode;
                        instance.bayName = response.existingBayInfo.BayName;
                        instance.comments = response.existingBayInfo.Comment;
                        instance.active = response.existingBayInfo.Active;
                    }
                }, function (error) {

                });
            } else {
                this.title = 'Add Bay';
                this.buttonText = "Add";
                this.transferNo = '';

                this.status = '';
                this.buttonShow = true;
                this.actionType = 'add'
            }
            $("#add-edit-dept").modal("toggle");
        })
    },
    destroyed() {
        bus.$off('add-edit-jobCard-bay')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        getSupportingData(){
            let instance = this;
            this.axiosGet('jobCard/bay-supporting-data', function (response) {
               instance.customers =  response.customers

            }, function (error) {
            });
        },
        onSubmit() {
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'jobCard/bay-add';
                }
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'jobCard/bay-update';
                }
                this.axiosPost(submitUrl, {
                    dealerCode: this.dealerCode,
                    bayCode: this.bayCode,
                    bayName: this.bayName,
                    comments: this.comments,
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
