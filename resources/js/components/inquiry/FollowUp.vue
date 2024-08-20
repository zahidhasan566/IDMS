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
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Visit Type" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="Visit Type">Visit Type <span class="error">*</span></label>
                                            <select class="form-control" name="gender"
                                                    v-model="visitType">
                                                <option value="">Select</option>
                                                <option v-for="(visitResult,index) in visitResults" :value="visitResult.VisitResultId">{{visitResult.VisitResultName}}</option>
                                            </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Product Name" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="product">product <span class="error">*</span></label>
                                            <v-select :filterable="false"
                                                      :mutiselect="false"
                                                      v-model="product"
                                                      :options="allProduct" label="ProductName"
                                                      @search="searchProduct">

                                            </v-select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Inquiry Level" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="educationalQualification">Inquiry Level <span class="error">*</span></label>
                                            <select class="form-control" name="gender"
                                                    v-model="inquiryLevel">
                                                <option value="">Select</option>
                                                <option v-for="(levelSupportingData,index) in inquiryLevelSupportingData" :value="levelSupportingData.InquiryLevelId">{{levelSupportingData.InquiryLevelName}}</option>
                                            </select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="designation">Expected Delivery</label>
                                            <input type="date" class="form-control"
                                                   id="expectedDelivery"
                                                   data-required="true"
                                                   v-model="expectedDelivery" name="expectedDelivery">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                            <label for="tagNo">Next Visit</label>
                                            <input type="date" class="form-control"
                                                   id="NextVisit"
                                                   data-required="true"
                                                   placeholder="Next Visit"
                                                   v-model="nextVisit" name="nextVisit">
                                            <span class="error-message"> {{ errors[0] }}</span>
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
import {baseurl} from "../../base_url";

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
            staffId:'',
            joiningDate:'',
            dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            visitType:'',
            product:'',
            inquiryLevel:'',
            nextVisit:'',
            expectedDelivery:'',
            inquiryLevelSupportingData:[],
            allProduct:[],
            visitResults:[],
            inquiryId:''

        }
    },
    computed: {},
    created() {},
    mounted() {
        this.getSupportingData();
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-inquiry-follow-up', (row) => {
            this.getSupportingData();
            if (row) {
                console.log(row)
                let instance = this;
                instance.title = 'Update Follow Up Inquiry';
                instance.buttonText = "Update Follow Up";
                instance.buttonShow = true;
                instance.actionType = 'edit';
                instance.inquiryId = row.InquiryId
                instance.expectedDelivery  = moment().add(1, 'days').format('YYYY-MM-DD');
                instance.nextVisit  = moment().add(1, 'days').format('YYYY-MM-DD');
            } else {
                this.title = 'Add Technician';
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
        bus.$off('add-edit-jobCard-technician')
    },
    methods: {
        getSupportingData() {
            let instance = this;
            this.axiosGet('inquiry/supporting-data', function (response) {
                instance.inquiryOccupationSupportingData = response.inquiryOccupationSupportingData
                instance.inquiryCustomerCategory = response.inquiryCustomerCategory
                instance.inquiryMainUser = response.inquiryMainUser
                instance.inquiryMediaCategory = response.inquiryMediaCategory
                instance.inquiryLevelSupportingData = response.inquiryLevelSupportingData
                instance.inquiryDocumentCategory = response.inquiryDocumentCategory
                instance.visitResults =  response.visitResults

            }, function (error) {
            });
        },
        searchProduct(val) {
            let productCode = val;
            if (productCode.length > 2) {
                axios.get(baseurl + 'api/inquiry/search-product/'+ productCode
                    , this.config()).then((response) => {
                    this.allProduct = response.data.allProduct;
                }).catch((error) => {

                })
            }
        },
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            var returnData = $('#return').prop('checked');
            var submitUrl = '';
            submitUrl = 'inquiry/update-follow-up';
            this.axiosPost(submitUrl, {
                inquiryId: this.inquiryId,
                visitType: this.visitType,
                product: this.product,
                inquiryLevel: this.inquiryLevel,
                expectedDelivery: this.expectedDelivery,
                nextVisit: this.nextVisit,

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
