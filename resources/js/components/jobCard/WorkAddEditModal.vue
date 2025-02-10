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
                                        <ValidationProvider name="Work Name" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">

                                                <label for="tagNo">Work Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="bayName"
                                                       data-required="true"
                                                       v-model="workName" name="workName" placeholder="Work Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="Work Rate" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">

                                            <label for="tagNo">Work Rate</label>
                                            <input type="number" class="form-control"
                                                   step="any"
                                                   id="workRate"
                                                   data-required="true"
                                                   v-model="workRate" name="workRate" placeholder="Work Rate">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                            <div class="form-group">

                                                <label for="tagNo">Comments</label>
                                                <input type="text" class="form-control"
                                                       id="bayName"
                                                       data-required="true"
                                                       v-model="comments" name="Comments" placeholder="Comments">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                    </div>
                                    <div class="col-12 col-md-4">
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
            workName:'',
            workRate:0,
            comments:'',
            active:'',
            workCode:''

        }
    },
    computed: {},
    created() {},
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-jobCard-work', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('jobCard/get/work/modal/' + row.WorkCode, function (response) {
                    instance.title = 'Update Work List';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    console.log(response)
                    if(response.existingWorkInfo){
                        instance.workCode= response.existingWorkInfo.WorkCode;
                        instance.workName = response.existingWorkInfo.WorkName;
                        instance.workRate = response.existingWorkInfo.WorkRate ;
                        instance.comments = response.existingWorkInfo.Comment;
                        instance.active = response.existingWorkInfo.Active;
                    }
                }, function (error) {

                });
            } else {
                this.title = 'Add Work';
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
        bus.$off('add-edit-jobCard-work')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        onSubmit() {
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'jobCard/work-add';
                }
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'jobCard/work-update';
                }
                this.axiosPost(submitUrl, {
                    workCode: this.workCode,
                    workName: this.workName,
                    workRate: this.workRate,
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
