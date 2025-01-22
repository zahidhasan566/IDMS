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
                        <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction"
                              @submit.prevent="handleSubmit(onSubmit)"
                              @keydown.enter="$event.preventDefault()">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-3">
                                        <ValidationProvider name="Dealer" mode="eager" v-slot="{ errors }"
                                                            rules="required">
                                            <div class="form-group">
                                                <label>Dealer <span class="error">*</span></label>
                                                <select name="dealerCode" class="form-control" v-model="dealerCode"
                                                        @change="checkBayList($event)"
                                                        style="margin: 0">
                                                    <option :value="singleCustomer.CustomerCode"
                                                            v-for="(singleCustomer , index) in customers"
                                                            :key="index">{{ singleCustomer.CustomerCode }} - {{ singleCustomer.CustomerName }}
                                                    </option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="territory">Territory</label>
                                            <multiselect v-model="territory" :options="territoryList"
                                                         :multiple="false"
                                                         :close-on-select="true"
                                                         :clear-on-select="false" :preserve-search="false"
                                                         placeholder="Select Territory"
                                                         label="TTYName" track-by="TTYCode">

                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="territory">Photo</label>
                                            <input type="file" class="form-control"
                                                   @change="fileUpload($event)"
                                                   id="ydTFile"
                                                   name="ydTFile">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Network Category" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="tagNo">Network Category<span class="error">*</span></label>
                                            <select class="form-control" name="active"
                                                    v-model="networkCategory">
                                                <option value="">Select</option>
                                                <option value="a0Network">A0 Network</option>
                                                <option value="a1Network">A1 Network</option>
                                                <option value="a2Network">A2 Network</option>
                                                <option value="a3Network">A3 Network</option>
                                            </select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Dealer Category" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="tagNo">Dealer Category<span class="error">*</span></label>
                                            <select class="form-control" name="active"
                                                    v-model="dealerCategory">
                                                <option value="">Select</option>
                                                <option value="a">A</option>
                                                <option value="b">B</option>
                                                <option value="c">C</option>
                                            </select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="Staff Id">Staff Id</label>
                                            <input type="text" class="form-control"
                                                   id="staffId"
                                                   data-required="true"
                                                   v-model="staffId" name="staffId" placeholder="staff Id">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="joiningDate">Date Of Birth </label>
                                            <datepicker v-model="birthDate" :dayStr="dayStr"
                                                        placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="joiningDate">Joining Date </label>

                                            <datepicker v-model="joiningDate" :dayStr="dayStr"
                                                        placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="educationalQualification">Educational Qualification </label>
                                            <input type="text" class="form-control"
                                                   id="educationalQualification"
                                                   data-required="true"
                                                   v-model="educationalQualification" name="educationalQualification"
                                                   placeholder="Educational Qualification">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="designation">Designation</label>
                                            <select class="form-control" name="designation"
                                                    v-model="designation">
                                                <option value="">Select Designation</option>
                                                <option :value="singleDesignation.TechnicianDesignation"
                                                        v-for="(singleDesignation , index) in designationList"
                                                        :key="index">{{ singleDesignation.TechnicianDesignation }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
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
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Technician Name" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="technicianName">Technician Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="staffId"
                                                       data-required="true"
                                                       v-model="technicianName" name="staffId"
                                                       placeholder="Technician Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="contactNo">Contact No</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="contactNo"
                                                   data-required="true"
                                                   v-model="contactNo" name="contactNo" placeholder="ContactNo">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="Address">City</label>
                                            <input type="text" class="form-control"
                                                   id="Address"
                                                   data-required="true"
                                                   v-model="address" name="address" placeholder="City">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Gender" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <label for="Gender">Gender<span class="error">*</span></label>
                                            <select class="form-control" name="gender"
                                                    v-model="gender">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="experience">Total Experience</label>
                                            <input type="number" class="form-control"
                                                   id="experience"
                                                   data-required="true"
                                                   v-model="experience" name="experience" placeholder="experience">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="tagNo">Comment</label>
                                            <input type="text" class="form-control"
                                                   id="bayName"
                                                   data-required="true"
                                                   v-model="comment" name="Comments" placeholder="Comments">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="contactNo">Default Bay</label>
                                            <multiselect v-model="defaultBay" :options="allBay"
                                                         :multiple="false"
                                                         :close-on-select="true"
                                                         :clear-on-select="false" :preserve-search="false"
                                                         placeholder="Select Bay"
                                                         label="BayName" track-by="BayCode">

                                            </multiselect>
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
import DatePicker from 'vue2-datepicker';

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
            educationalQualification: '',
            designation: '',
            technicianName: '',
            contactNo: '',
            address: '',
            training: '',
            comment: '',
            defaultBay: '',
            active: '',
            allBay: [],
            technicianCode: '',
            dealerCode: '',
            customers: [],
            territory: '',
            technicianPhoto: '',
            networkCategory: '',
            dealerCategory: '',
            birthDate: '',
            gender: '',
            experience: '',
            designationList: [],
            territoryList: []


        }
    },
    computed: {},
    created() {
    },
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-jobCard-technician', (row) => {
            this.getSupportingData();
            if (row) {
                let instance = this;
                this.axiosGet('jobCard/get/technician/modal/' + row.TechnicianCode, function (response) {
                    instance.title = 'Update Technician List';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if (response.existingTechnicianInfo) {
                        instance.dealerCode = response.existingTechnicianInfo.ServiceCenterCode;
                        instance.territory = {
                            'TTYCode': response.existingTechnicianInfo.Territory,
                            'TTYName': response.existingTechnicianInfo.TTYName,
                        };
                        instance.networkCategory = response.existingTechnicianInfo.NetworkCategory;
                        instance.dealerCategory = response.existingTechnicianInfo.DealerCategory;
                        instance.birthDate =  response.existingTechnicianInfo.DateOfBirth !==null ? response.existingTechnicianInfo.DateOfBirth : '';
                        instance.technicianCode = response.existingTechnicianInfo.TechnicianCode;
                        instance.staffId = response.existingTechnicianInfo.TechnicianEmpCode;
                        instance.joiningDate = response.existingTechnicianInfo.JoiningDate !==null ? response.existingTechnicianInfo.JoiningDate : '';
                        instance.educationalQualification = response.existingTechnicianInfo.EducationalQualification;
                        instance.designation = response.existingTechnicianInfo.Designation;
                        instance.active = response.existingTechnicianInfo.Active;
                        instance.technicianName = response.existingTechnicianInfo.TechnicianName;
                        instance.contactNo = response.existingTechnicianInfo.ContactNo;
                        instance.address = response.existingTechnicianInfo.Address;
                        instance.gender = response.existingTechnicianInfo.Gender;
                        instance.experience = response.existingTechnicianInfo.Experience;
                        instance.comment = response.existingTechnicianInfo.Comment;
                        instance.defaultBay = {
                            'BayCode': response.existingTechnicianInfo.DefaultBay,
                            'BayName': response.existingTechnicianInfo.BayName,
                        };
                        instance.checkBayList(instance.dealerCode);
                    }
                }, function (error) {

                });
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
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        getSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/technician-supporting-data', function (response) {
                instance.customers = response.customers
                instance.designationList = response.designationList
                instance.territoryList = response.territoryList
            }, function (error) {
            });
        },
        checkBayList(e) {
            console.log(this.dealerCode)
            let serviceCenterCode = this.actionType === 'edit' ? this.dealerCode : e.target.value
            let instance = this;
            this.axiosGet('jobCard/technician-check-bay-data/' + serviceCenterCode, function (response) {
                instance.allBay = response.allBay;
            }, function (error) {
            });
        },
        fileUpload(e) {
            var input = e.target
            var file = input.files[0]

            if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
                this.errorNoti('Invalid file type')
            } else {
                if (file.size > 10000000) {
                    this.errorNoti('Maximum file size 10 MB')
                } else {
                    this.processImage(file)
                }
            }
        },
        processImage(file) {
            let instance = this
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                instance.technicianPhoto = reader.result
                instance.AttachmentFlag = 1
            };
            reader.onerror = function (error) {
            };
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            var returnData = $('#return').prop('checked');
            var submitUrl = '';
            if (this.actionType === 'add') {
                submitUrl = 'jobCard/technician-add';
            }
            if (!returnData && this.actionType === 'edit') {
                submitUrl = 'jobCard/technician-update';
            }
            this.axiosPost(submitUrl, {
                dealerCode: this.dealerCode,
                technicianCode: this.technicianCode,
                territory: this.territory,
                technicianPhoto: this.technicianPhoto,
                networkCategory: this.networkCategory,
                dealerCategory: this.dealerCategory,
                staffId: this.staffId,
                birthDate: this.birthDate,
                joiningDate: this.joiningDate,
                educationalQualification: this.educationalQualification,
                designation: this.designation,
                active: this.active,
                technicianName: this.technicianName,
                contactNo: this.contactNo,
                address: this.address,
                gender: this.gender,
                experience: this.experience,
                training: this.training,
                comment: this.comment,
                defaultBay: this.defaultBay,

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
