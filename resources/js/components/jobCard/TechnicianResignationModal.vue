<template>
    <div id="rat-page">
        <div class="modal fade" id="add-edit-dept3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
                            Close
                        </button>
                    </div>
                    <ValidationObserver v-slot="{ handleSubmit }">
                        <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction"
                              @submit.prevent="handleSubmit(onSubmit)"
                              @keydown.enter="$event.preventDefault()">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-12 col-md-5">
                                        <ValidationProvider name="Resignation Date" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="joiningDate">Date Of Resignation </label>
                                            <datepicker v-model="resignationDate" :dayStr="dayStr"
                                                        placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-7">
                                        <ValidationProvider name="Reason of Resign" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="reasonOfResignation"> Reason of Resign</label>
                                            <multiselect v-model="reasonOfResignation" :options="reasonOfResignList"
                                                         :multiple="false"
                                                         :close-on-select="true"
                                                         :clear-on-select="false" :preserve-search="false"
                                                         placeholder="Select Reason Of Resign"
                                                         label="ReasonOfResign" track-by="ReasonOfResign">

                                            </multiselect>
                                        </div>
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
            staffId: '',
            joiningDate: '',
            dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            technicianCode:'',
            resignationDate:'',
            reasonOfResignation:'',
            reasonOfResignList:[]
        }
    },
    computed: {},
    created() {
    },
    mounted() {
        $('#add-edit-dept3').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept3').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        this.getResignSupportingData();
        bus.$on('resignation-job-card-technician', (row) => {
            if (row) {
                console.log(row)
                this.technicianCode =  row.TechnicianCode
                this.title = 'Resignation Technician';
                this.buttonText = "Resign";
                this.buttonShow = true;
                this.actionType = 'add';
                $("#add-edit-dept3").modal("toggle");
            }
        })
    },
    destroyed() {
        bus.$off('resignation-job-card-technician')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept3").modal("toggle");
        },
        getResignSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/technician-resign-supporting-data', function (response) {
                instance.reasonOfResignList = response.reasonOfResignList;
            }, function (error) {
            });
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            var submitUrl = 'jobCard/technician-resignation';

            this.axiosPost(submitUrl, {
                technicianCode: this.technicianCode,
                resignationDate:this.resignationDate,
                reasonOfResignation:this.reasonOfResignation,
            }, (response) => {
                this.successNoti(response.message);
                $("#add-edit-dept3").modal("toggle");
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
<script setup>
</script>