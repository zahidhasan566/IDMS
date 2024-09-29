<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Inquiry Progress Card']">
                <router-link class="btn btn-primary" :to="{name:'FollowUpIndex'}">Back</router-link>
            </breadcrumb>
            <!-- end row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <ValidationObserver v-slot="{ handleSubmit }">
                            <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction"
                                  @submit.prevent="handleSubmit(onSubmit)">
                                <div class="card-header" style="background: #204d74 !important;">Customer Profile</div>
                                <div class="card-body">
                                    <fieldset id="form-horizontal-p-0" role="tabpanel"
                                              aria-labelledby="form-horizontal-h-0" class="body current"
                                              aria-hidden="false">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <ValidationProvider name="Name" mode="eager"
                                                                    rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="jobCardNo" class="col-lg-3 col-form-label">Name
                                                            <span class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="jobCardNo"
                                                                   data-required="true"
                                                                   v-model="name" name="Name"
                                                                   placeholder="Name">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                            <div class="col-md-4">
                                                <ValidationProvider name="Contact no" mode="eager" rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="contactNo" class="col-lg-3 col-form-label">Contact
                                                            no.<span class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="contactNo"
                                                                   data-required="true"
                                                                   placeholder="Contact no"
                                                                   v-model="contactNo" name="contactNo">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                            <div class="col-md-4">
                                                <ValidationProvider name="Convenient Time to Call" mode="eager"
                                                                    rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="contactNo" class="col-lg-6 col-form-label">Convenient
                                                            Time to Call<span class="error">*</span></label>
                                                        <div class="col-lg-6">
                                                            <input type="text" class="form-control"
                                                                   id="convenientTimeToCall"
                                                                   placeholder="Convenient Time to Call"
                                                                   data-required="true"
                                                                   v-model="convenientTimeToCall"
                                                                   name="convenientTimeToCall">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <ValidationProvider name="Address" mode="eager" rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="address"
                                                               class="col-lg-3 col-form-label">Address<span
                                                                class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="Address"
                                                                   placeholder="Address"
                                                                   data-required="true"
                                                                   v-model="address" name="contactNo">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                            <div class="col-md-4">
                                                <ValidationProvider name="Age" mode="eager"
                                                                    rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="age" class="col-lg-3 col-form-label">Age <span
                                                                class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="Age"
                                                                   data-required="true"
                                                                   v-model="age" name="Age"
                                                                   placeholder="age">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                            <div class="col-md-4">
                                                <ValidationProvider name="Gender" mode="eager" rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="gender" class="col-lg-6 col-form-label">Gender<span
                                                                class="error">*</span></label>
                                                        <div class="col-lg-6">
                                                            <select class="form-control" name="gender"
                                                                    v-model="gender">
                                                                <option value="male">Male</option>
                                                                <option value="female">female</option>
                                                            </select>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <ValidationProvider name="Occupation" mode="eager" rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="occupation" class="col-lg-3 col-form-label">Occupation<span
                                                                class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control" name="gender"
                                                                    v-model="occupation">
                                                                <option value="">Select</option>
                                                                <option v-for="(occupation,index) in inquiryOccupationSupportingData" :value="occupation.OccupationId">{{occupation.OccupationName}}</option>
                                                            </select>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <label for="currentTwoWheeler" class="col-lg-3 col-form-label">Current
                                                        2 Wheeler</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control"
                                                               id="currentTwoWheeler"
                                                               data-required="true"
                                                               v-model="currentTwoWheeler" name="Age"
                                                               placeholder="currentTwoWheeler">
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <ValidationProvider name="Customer Category" mode="eager"
                                                                    rules="required"
                                                                    v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="gender" class="col-lg-6 col-form-label">Customer
                                                            Category <span class="error">*</span></label>
                                                        <div class="col-lg-6">
                                                            <select class="form-control" name="gender"
                                                                    v-model="customerCategory">
                                                                <option value="">Select</option>
                                                                <option v-for="(customerCategory,index) in inquiryCustomerCategory" :value="customerCategory.CustomerCategoryId">{{customerCategory.CustomerCategoryName}}</option>
                                                            </select>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                </ValidationProvider>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="card-header" style="background: #343a40 !important;border-color: #454d55">Follow up Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="nextVisit" class="col-lg-3 col-form-label">Next
                                                    visit</label>
                                                <div class="col-lg-9">
                                                    <input type="date" class="form-control"
                                                           id="NextVisit"
                                                           data-required="true"
                                                           placeholder="Next Visit"
                                                           v-model="nextVisit" name="nextVisit">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="expectedDelivery" class="col-lg-4 col-form-label">Expected
                                                    delivery</label>
                                                <div class="col-lg-8">
                                                    <input type="date" class="form-control"
                                                           id="expectedDelivery"
                                                           data-required="true"
                                                           v-model="expectedDelivery" name="expectedDelivery">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header"  style="background: #3f467c !important;">Main User Profile: Customer Need</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="expectedDelivery" class="col-lg-5 col-form-label">Main User
                                                    Detail</label>
                                                <div class="col-lg-7">
                                                    <select class="form-control" name="gender"
                                                            v-model="mainUserDetail">
                                                        <option value="">Select</option>
                                                        <option v-for="(mainUser,index) in inquiryMainUser" :value="mainUser.InquiryMainUserId">{{mainUser.InquiryMainUserName}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="userOccupation" class="col-lg-5 col-form-label">User
                                                    Occupation</label>
                                                <div class="col-lg-7">
                                                    <select class="form-control" name="gender"
                                                            v-model="userOccupation">
                                                        <option value="">Select</option>
                                                        <option v-for="(occupation,index) in inquiryOccupationSupportingData" :value="occupation.OccupationId">{{occupation.OccupationName}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="userCurrentTwoWheeler" class="col-lg-6 col-form-label">User
                                                    Current 2 Wheeler</label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control"
                                                           id="userCurrentTwoWheeler"
                                                           placeholder="user Current Two Wheeler"
                                                           data-required="true"
                                                           v-model="userCurrentTwoWheeler" name="userCurrentTwoWheeler">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="modelSuggested" class="col-lg-5 col-form-label">Model
                                                    Suggested</label>
                                                <div class="col-lg-7">
                                                    <input type="text" class="form-control"
                                                           id="modelSuggested"
                                                           placeholder="Model Suggested"
                                                           data-required="true"
                                                           v-model="modelSuggested" name="modelSuggested">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                    </div>
                                </div>
                                <div class="card-header" style="background: #204d74 !important;">How did the customer know about the product</div>
                                <div class="card-body row pl-5 pr-5 pb-3">
                                    <div class="col-md-2" v-for="(mediaCategory,index) in inquiryMediaCategory" :key="index">
                                        <input class="form-check-input" type="checkbox" :value="mediaCategory.InquiryMediaCategoryId" v-model="allMediaCategoryId" :id="'allMedia'+index">
                                        <label class="form-check-label" :for="'allMedia'+index+'-'">
                                            {{mediaCategory.InquiryMediaCategoryName}}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-header" style="background: #61519f !important;">Accessories</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="appralsAccessories" class="col-lg-5 col-form-label">Interested in Appeals and Accessories</label>
                                                <div class="col-lg-4">
                                                    <select class="form-control" name="Appeals Accessories"
                                                            v-model="appralsAccessories">
                                                        <option value=1>Yes</option>
                                                        <option value=0>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="purposeOfRoyal" class="col-lg-5 col-form-label">Intended purpose of Royal Enfield use</label>
                                                <div class="col-lg-4">
                                                    <select class="form-control" name="purpose Of Royal"
                                                            v-model="purposeOfRoyal">
                                                        <option value=1>Yes</option>
                                                        <option value=0>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              <div class="card-header" style="background: #343a40 !important;border-color: #454d55">Offer test ride to customer</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="expectedDelivery" class="col-lg-5 col-form-label">Test Ride
                                                    Offer</label>
                                                <div class="col-lg-7">
                                                    <select class="form-control" name="gender"
                                                            v-model="testRiderOffer">
                                                        <option value=1>Yes</option>
                                                        <option value=0>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header" style="background: #6c757d  !important">Dealer</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="expectedDelivery"
                                                       class="col-lg-3 col-form-label">Product</label>
                                                <div class="col-lg-9">
                                                        <v-select :filterable="false"
                                                                  :mutiselect="false"
                                                                  v-model="product"
                                                                  :options="allProduct" label="ProductName"
                                                                  @search="searchProduct">

                                                        </v-select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="modelYear" class="col-lg-5 col-form-label">Model
                                                    Year</label>
                                                <div class="col-lg-7">
                                                    <input type="text" class="form-control"
                                                           id="modelYear"
                                                           placeholder="Model year"
                                                           data-required="true"
                                                           v-model="modelYear" name="modelYear">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="expectedValue" class="col-lg-5 col-form-label">Expected
                                                    Value</label>
                                                <div class="col-lg-7">
                                                    <input type="text" class="form-control"
                                                           id="expectedValue"
                                                           placeholder="Expected Value"
                                                           data-required="true"
                                                           v-model="expectedValue" name="expectedValue">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="bankScheme" class="col-lg-5 col-form-label">Bank
                                                    Scheme</label>
                                                <div class="col-lg-7">
                                                    <select class="form-control" name="bankScheme"
                                                            v-model="bankScheme">
                                                        <option value="Cash">In cash</option>
                                                        <option value="Finance">Finance</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inquiryLevel" style="padding-right: 0"
                                                       class="col-lg-3 col-form-label">Inquiry Level</label>
                                                <div class="col-lg-9">
                                                    <select class="form-control" name="gender"
                                                            v-model="inquiryLevel">
                                                        <option value="">Select</option>
                                                        <option v-for="(levelSupportingData,index) in inquiryLevelSupportingData" :value="levelSupportingData.InquiryLevelId">{{levelSupportingData.InquiryLevelName}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inquiryRemark" class="col-lg-5 col-form-label">Inquiry
                                                    Remark</label>
                                                <div class="col-lg-7">
                                                    <input type="text" class="form-control"
                                                           id="inquiryRemark"
                                                           data-required="true"
                                                           placeholder="Inquiry Remark"
                                                           v-model="inquiryRemark" name="inquiryRemark">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" style="padding-right: 0">
                                            <div class="form-group row">
                                                <label for="salesConsultantName" class="col-lg-4 col-form-label">Sales
                                                    Consultant Name</label>
                                                <div class="col-lg-8" style="padding-left: 0">
                                                    <input type="text" class="form-control"
                                                           id="salesConsultantName"
                                                           data-required="true"
                                                           placeholder="Sales Consultant Name"
                                                           v-model="salesConsultantName" name="salesConsultantName">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label for="idAddressProf" class="col-lg-2 col-form-label">ID & Address
                                                    Proof</label>
                                                <div class="col-lg-7">
                                                    <div class="row">
                                                        <div class="col-md-2" v-for="(proof,index) in inquiryDocumentCategory" v-if="proof.InquiryDocumentCategory==='ID & Address Proof'" :key="index">
                                                            <input class="form-check-input" type="checkbox" :value="proof.DocumentId" v-model="idAddressProof" :id="'idAddress'+index">
                                                            <label class="form-check-label" :for="'idAddress'+index+'-'">
                                                                {{proof.InquiryDocumentSubCategory}}
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label for="residenceCum" class="col-lg-2 col-form-label">Residence
                                                    cum</label>
                                                <div class="col-lg-7">
                                                    <div class="row">
                                                        <div class="col-md-2" v-for="(residence,index) in inquiryDocumentCategory" v-if="residence.InquiryDocumentCategory==='Residence cum'" :key="index">
                                                            <input class="form-check-input" type="checkbox" :value="residence.DocumentId" v-model="residenceCum" :id="'residenceCum'+index">
                                                            <label class="form-check-label" :for="'residenceCum'+index+'-'">
                                                                {{residence.InquiryDocumentSubCategory}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
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

            <!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>

</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
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
            name:'',
            contactNo:'',
            convenientTimeToCall:'',
            address:'',
            age:'',
            gender:'',
            occupation:'',
            currentTwoWheeler:'',
            customerCategory:'',
            nextVisit:'',
            expectedDelivery:'',
            mainUserDetail:'',
            userOccupation:'',
            userCurrentTwoWheeler:'',
            modelSuggested:'',
            testRiderOffer:'',
            purposeOfRoyal:'',
            appralsAccessories:'',
            product:'',
            modelYear:'',
            expectedValue:'',
            bankScheme:'',
            inquiryLevel:'',
            inquiryRemark:'',
            salesConsultantName:'',
            inquiryOccupation:[],
            inquiryCustomerCategory:[],
            inquiryDocumentCategory:[],
            inquiryMainUser:[],
            inquiryMediaCategory:[],
            inquiryLevelSupportingData:[],
            inquiryOccupationSupportingData:[],
            allMediaCategoryId:[],
            allProduct:[],
            idAddressProof:[],
            residenceCum:[],
        }
    },
    computed: {},
    created() {
    },
    mounted() {
        this.buttonShow= true;
        this.buttonText="Submit";
        this.getSupportingData();
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-jobCard-bay', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('jobCard/get/bay/modal/' + row.BayCode, function (response) {
                    instance.title = 'Update Bay List';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if (response.existingBayInfo) {
                        instance.bayCode = response.existingBayInfo.BayCode;
                        instance.bayName = response.existingBayInfo.BayName;
                        instance.comments = response.existingBayInfo.Comment;
                        instance.active = response.existingBayInfo.Active;
                    }

                    // instance.getItemByCategory();
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
        getSupportingData() {
            let instance = this;
            this.axiosGet('inquiry/supporting-data', function (response) {
                instance.inquiryOccupationSupportingData = response.inquiryOccupationSupportingData
                instance.inquiryCustomerCategory = response.inquiryCustomerCategory
                instance.inquiryMainUser = response.inquiryMainUser
                instance.inquiryMediaCategory = response.inquiryMediaCategory
                instance.inquiryLevelSupportingData = response.inquiryLevelSupportingData
                instance.inquiryDocumentCategory = response.inquiryDocumentCategory

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
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            var submitUrl = 'inquiry/progress-card-add';
            this.axiosPost(submitUrl, {
                name: this.name,
                contactNo: this.contactNo,
                convenientTimeToCall: this.convenientTimeToCall,
                address: this.address,
                age: this.age,
                gender: this.gender,
                occupation: this.occupation,
                currentTwoWheeler: this.currentTwoWheeler,
                customerCategory: this.customerCategory,
                nextVisit: this.nextVisit,
                expectedDelivery: this.expectedDelivery,
                mainUserDetail: this.mainUserDetail,
                userOccupation: this.userOccupation,
                userCurrentTwoWheeler: this.userCurrentTwoWheeler,
                modelSuggested: this.modelSuggested,
                allMediaCategoryId: this.allMediaCategoryId,
                testRiderOffer: this.testRiderOffer,
                purposeOfRoyal:this.purposeOfRoyal,
                appralsAccessories:this.appralsAccessories,
                product: this.product,
                modelYear: this.modelYear,
                expectedValue: this.expectedValue,
                bankScheme: this.bankScheme,
                inquiryLevel: this.inquiryLevel,
                inquiryRemark: this.inquiryRemark,
                salesConsultantName: this.salesConsultantName,
                idAddressProof: this.idAddressProof,
                residenceCum: this.residenceCum
            }, (response) => {
                this.successNoti(response.message);
                $("#add-edit-dept").modal("toggle");
                bus.$emit('refresh-datatable');
                this.$store.commit('submitButtonLoadingStatus', false);
                this.$router.push({name: 'FollowUpIndex'})
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
<style scoped>
.card-header{
    font-size: 14px !important;
}
.card-body{
    padding-bottom: 5px;
}
.datepicker .vue-input, .date-range-picker .vue-input, .timepicker .vue-input, .datetime-picker .vue-input {
    padding: 7px !important;
}
</style>
