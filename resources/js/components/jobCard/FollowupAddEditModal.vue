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
                                    <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="scheduleId">Schedule ID</label>
                                                <input type="text" class="form-control"
                                                       id="scheduleId"
                                                       data-required="true"
                                                       v-model="scheduleId" name="scheduleId" placeholder="schedule Id" readonly>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                    </div>
                                  <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <input type="text" class="form-control"
                                                       id="remarks"
                                                       data-required="true"
                                                       v-model="remarks" name="remarks" placeholder="remarks" required>
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
            actionType: '',
            buttonShow: false,
            errors: [],
            remarks:'',
            scheduleId:''

        }
    },
    computed: {},
    created() {},
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-follow-up', (row) => {
          console.log(row)
            if (row) {
                let instance = this;
                    this.scheduleId = row;
                    instance.title = 'Update Follow Up';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
            }
            $("#add-edit-dept").modal("toggle");
        })
    },
    destroyed() {
        bus.$off('add-edit-follow-up')
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
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'jobCard/free-service-followup/update';
                }
                this.axiosPost(submitUrl, {
                    scheduleId: this.scheduleId,
                    remarks: this.remarks,
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
