<template>
    <div id="rat-page">
        <div class="modal fade" id="add-edit-dept2" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
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
                              @submit.prevent="handleSubmit(onSubmitTraining)"
                              @keydown.enter="$event.preventDefault()">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <ValidationProvider name="Training Name" mode="eager" v-slot="{ errors }"
                                                            rules="required">
                                            <div class="form-group">
                                                <label>Training Name <span class="error">*</span></label>
                                                <select name="dealerCode" class="form-control"
                                                        :disabled="type==='add'? false:true"
                                                        v-model="trainingId"
                                                        style="margin: 0">
                                                    <option :value="singleData.Id"
                                                            v-for="(singleData , index) in trainingList"
                                                            :key="index">{{ singleData.TrainingName }}
                                                    </option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="Training Date" mode="eager" v-slot="{ errors }"
                                                            rules="required">
                                            <div class="form-group">
                                                <label for="joiningDate">Training Date </label>

                                                <datepicker v-model="trainingDate" :dayStr="dayStr"
                                                            placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="Marks">Marks</label>
                                            <input type="number" class="form-control"
                                                   id="Marks"
                                                   data-required="true"
                                                   v-model="marks" name="marks"
                                                   placeholder="Marks">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: end;margin-top:5px">
                                <submit-form v-if="buttonShow" :name="buttonText"/>
                            </div>
                        </form>
                    </ValidationObserver>
                    <div class="table-responsive modal-body" v-if="existingTrainingList.length">
                        <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>TechnicianCode</th>
                                <th>TrainingName</th>
                                <th>TrainingDate</th>
                                <th>Mark</th>
                                <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(singleData, i) in existingTrainingList"
                                :key="singleData.id"
                                v-if="existingTrainingList.length">
                                <td>{{ ++i }}</td>
                                <td>{{ singleData.TechnicianCode }}</td>
                                <td>{{ singleData.TrainingName }}</td>
                                <td>{{ singleData.TrainingDate }}</td>
                                <td>{{ singleData.Mark }}</td>
                                <td><a href="javascript:" style="font-size: 11px;padding: 4px" class="btn btn-success"
                                       @click="editTraining(singleData.TrainingId)"> <i class="ti-pencil-alt"> Edit</i></a></td>
                            </tr>
                            </tbody>
                        </table>
                        <br>

                    </div>
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
            technicianCode: '',
            trainingList: [],
            trainingId: '',
            active: '',
            trainingDate: '',
            marks: '',
            existingTrainingList: [],
            submitStatus: true
        }
    },
    computed: {},
    created() {
    },
    mounted() {
        $('#add-edit-dept2').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept2').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('training-job-card-technician', (row) => {
            this.technicianCode = row.TechnicianCode
            this.getSupportingData();
            let instance = this;
            this.title = 'Technician Training';
            this.buttonText = "Add";
            this.transferNo = '';
            this.status = '';
            this.buttonShow = true;
            this.actionType = 'add'
            $("#add-edit-dept2").modal("toggle");
        })

    },
    destroyed() {
        bus.$off('training-job-card-technician')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept2").modal("toggle");
        },
        getSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/technician-training-supporting-data/' + this.technicianCode, function (response) {
                instance.trainingList = response.trainingList;
                instance.customers = response.customers
                instance.existingTrainingList = response.existingTrainingList
            }, function (error) {
            });
        },
        editTraining(trainingId) {
            this.type = 'edit'
            this.buttonText = "Update";
            let instance = this;
            instance.existingTrainingList.forEach(function (item, index) {
                if (trainingId === item.TrainingId) {
                    instance.trainingId = item.TrainingId
                    instance.trainingDate = item.TrainingDate
                    instance.marks = item.Mark
                }
            })
            ;

        },
        checkSubmit() {
            let updateTrainingId = this.trainingId
            let instance = this;
            instance.existingTrainingList.forEach(function (item, index) {
                if (updateTrainingId === item.TrainingId) {
                    instance.submitStatus = false
                    $("#add-edit-dept2").modal("toggle");
                    instance.errorNoti('Training Already Exist');
                }
            })

        },
        onSubmitTraining() {
            //this.$store.commit('submitButtonLoadingStatus', true);
            if (this.type === 'add') {
                this.checkSubmit();
            }
            if (this.submitStatus) {
                let submitUrl = 'jobCard/technician_training_add';
                this.axiosPost(submitUrl, {
                    technicianCode: this.technicianCode,
                    trainingId: this.trainingId,
                    trainingDate: this.trainingDate,
                    marks: this.marks

                }, (response) => {
                    this.successNoti(response.message);
                    $("#add-edit-dept2").modal("toggle");
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
