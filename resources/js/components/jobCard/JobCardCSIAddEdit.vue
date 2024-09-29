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
                                            <div class="form-group">
                                              <p>Job Card No : <span style="font-weight: bold">{{jobCardNo}}</span></p>
                                            </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <p>Dealer : <span style="font-weight: bold">{{dealerCode}}</span></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <p>Customer Name : <span style="font-weight: bold">{{customerName}}</span></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <p>Customer Phone No : <span style="font-weight: bold">{{mobile}}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="csiQuestion.length>0">

                                    <div class="row form-group" v-for="(item,index) in csiQuestion" :key="index">
                                    <div class="col-md-8">
                                        <ValidationProvider :name="`question ${index+1} is required`" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <p> {{ index +1}} : {{item.QuestionName}}</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="radio" class="form-group" v-model="item.Answer" value="2"/> &nbsp; খুব খারাপ
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="radio" class="form-group" v-model="item.Answer" value="4"/> &nbsp; খারাপ
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="radio" class="form-group" v-model="item.Answer" value="6"/>&nbsp; সাধারণ
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="radio" class="form-group" v-model="item.Answer" value="8"/> &nbsp; ভালো
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="radio" class="form-group" v-model="item.Answer" value="10"/> &nbsp;খুব ভালো
                                                </div>
                                            </div>
                                        </div>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
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
            bayName:'',
            comments:'',
            active:'',
            bayCode:'',
            dealerCode:'',
            jobCardNo:'',
            customerName:'',
            mobile:'',
            customers:[],
            csiQuestion:[],

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
        bus.$on('add-edit-jobCard-csi', (row) => {
            console.log(row)
            if (row) {
                let instance = this;
                instance.title = 'Update CSI List';
                instance.buttonText = "Update";
                instance.buttonShow = true;
                instance.actionType = 'edit';
                instance.dealerCode= row.Service_Dealar
                instance.mobile = row.Mobile_No
                instance.jobCardNo= row.Job_Card_No
                instance.customerName= row.Customer_Name
            }
            $("#add-edit-dept").modal("toggle");
        })
    },
    destroyed() {
        bus.$off('add-edit-jobCard-csi')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        getSupportingData(){
            let instance = this;
            this.axiosGet('jobCard/csi-supporting-data', function (response) {
                instance.csiQuestion =  response.csiQuestion

            }, function (error) {
            });
        },
        onSubmit() {
            console.log(this.csiQuestion)
            this.$store.commit('submitButtonLoadingStatus', true);
            var submitUrl = 'jobCard/csi-add-data';
            this.axiosPost(submitUrl, {
                dealerCode: this.dealerCode,
                jobCardNo: this.jobCardNo,
                customerName: this.customerName,
                mobile: this.mobile,
                csiQuestion: this.csiQuestion,
            }, (response) => {
                this.successNoti(response.message);
                $("#add-edit-dept").modal("toggle");
                bus.$emit('add-edit-jobCard-csi-data-refresh',)

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
