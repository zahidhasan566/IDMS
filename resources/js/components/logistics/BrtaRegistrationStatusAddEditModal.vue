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
                                        <ValidationProvider name="Registration Method" mode="eager" v-slot="{ errors }"   rules="required">
                                            <div class="form-group">
                                                <label for="Registration Method">Registration Method<span class="error">*</span></label>
                                                <select class="form-control" name="active"
                                                        @change="checkRegistrationMethod"
                                                        v-model="registrationMethod">
                                                    <option value="r">Registered</option>
                                                    <option value="f">File Handover to Customer</option>
                                                    <option value="u">Unregistered</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                            <div class="form-group">

                                                <label for="tagNo">Issue Date <span class="error">*</span></label>
                                                <datepicker v-model="issueDate" :dayStr="dayStr"
                                                            placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                                            </div>
                                    </div>
                                    <div class="col-12 col-md-3" v-if="registerFlag">
                                            <div class="form-group">

                                                <label for="BRTA Bank Deposit Slip ">BRTA Bank Deposit Slip </label>
                                                <select class="form-control" name="active"
                                                        v-model="bankDepositSlip">
                                                    <option value="">N/A</option>
                                                    <option value="Y">Done</option>
                                                    <option value="N">Pending</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                    </div>
                                    <div class="col-12 col-md-3" v-if="fileHandoverFlag || unRegisterFlag">
                                            <div class="form-group">
                                                <label for="Reg. Documents File Completed">Reg. Documents File Completed </label>
                                                <select class="form-control" name="active"
                                                        v-model="regDocFileComplete">
                                                    <option value="">N/A</option>
                                                    <option value="Y">Done</option>
                                                    <option value="N">Pending</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                    </div>
                                    <div class="col-12 col-md-3" v-if="registerFlag">
                                            <label for="BRTA Registration Number ">BRTA Registration Number <span class="error">*</span></label>
                                            <input type="text" class="form-control"
                                                   id="brtaRegistrationNumber"
                                                   data-required="true"
                                                   v-model="brtaRegistrationNumber" name="brtaRegistrationNumber" placeholder="BRTA Registration Number">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                    </div>
                                    <div class="col-12 col-md-3" v-if="fileHandoverFlag">
                                            <div class="form-group">

                                                <label for="File Received By Customer">File Received By Customer</label>
                                                <select class="form-control" name="active"
                                                        v-model="fileReceiveByCustomer">
                                                    <option value="">N/A</option>
                                                    <option value="Y">Done</option>
                                                    <option value="N">Pending</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
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
            brtaRegistrationStatusId:0,
            chassisNo:'',
            registrationMethod:'',
            issueDate:'',
            dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            bankDepositSlip:'',
            brtaRegistrationNumber:'',
            registerFlag: false,
            fileHandoverFlag: false,
            unRegisterFlag: false,
            regDocFileComplete:'',
            fileReceiveByCustomer:'',

        }
    },
    computed: {},
    created() {},
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-brta-registration', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('logistics/get/brta-registration-status/modal/' + row.BRTA_RegistrationStatusID, function (response) {
                    instance.title = 'Update Brta Registration Status';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if(response.brtaRegistration){
                        instance.registrationMethod = "r"
                        instance.checkRegistrationMethod()
                        instance.brtaRegistrationStatusId =  response.brtaRegistration.BRTA_RegistrationStatusID
                        instance.chassisNo =  response.brtaRegistration.ChassisNO
                        instance.issueDate = response.brtaRegistration.IssueDate
                        instance.bankDepositSlip = response.brtaRegistration.BRTA_BankDeposite
                        instance.brtaRegistrationNumber = response.brtaRegistration.BRTA_RegistrationNumber
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
        bus.$off('add-edit-brta-registration')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        checkRegistrationMethod(){
            if(this.registrationMethod==='r'){
                this.registerFlag= true
                this.fileHandoverFlag = false
                this.unRegisterFlag = false
            }
            else if(this.registrationMethod==='f'){
                this.fileHandoverFlag = true
                this.unRegisterFlag = false
                this.registerFlag= false
            }
            else{
                this.unRegisterFlag= true
                this.fileHandoverFlag = false
                this.registerFlag= false
            }
        },
        checkFieldValue(){
            if(this.registrationMethod==='r'){
                if(this.bankDepositSlip==='' || this.brtaRegistrationNumber===''){
                    this.errors.push('error')
                    this.errorNoti('Bank deposit slip and brta registration number need');
                }
                else{
                    this.errors=[]
                }
            }
            else if(this.registrationMethod==='f'){
                if(this.regDocFileComplete==='' || this.fileReceiveByCustomer===''){
                    this.errors.push('error')
                    this.errorNoti('Reg. Documents File Completed and File Received By Customer need');
                }
                else{
                    this.errors=[]
                }
            }
            else{
                if(this.regDocFileComplete===''){
                    this.errors.push('error')
                    this.errorNoti('Reg. Documents File Completed need');
                }
                else{
                    this.errors=[]
                }
            }

        },
        onSubmit() {
            this.checkFieldValue()
            if(this.errors.length===0){
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = 'logistics/update-brta-registration-list';
                this.axiosPost(submitUrl, {
                    brtaRegistrationStatusId: this.brtaRegistrationStatusId,
                    chassisNo: this.chassisNo,
                    registrationMethod: this.registrationMethod,
                    issueDate: this.issueDate,
                    bankDepositSlip: this.bankDepositSlip,
                    brtaRegistrationNumber: this.brtaRegistrationNumber,
                    regDocFileComplete: this.regDocFileComplete,
                    fileReceiveByCustomer: this.fileReceiveByCustomer,
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
