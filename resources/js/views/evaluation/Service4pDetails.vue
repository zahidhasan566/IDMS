<template>
    <div class="container-fluid">
        <breadcrumb :options="['Evaluation Service 4p First Part']">
            <router-link :to="{name: 'InvoiceList'}" class="btn btn-primary btn-sm">Back</router-link>
        </breadcrumb>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="datatable" v-if="!isLoading">
                        <div class="card-body" id="customer_form">
                            <ValidationObserver v-slot="{ handleSubmit }">
                                <form @submit.prevent="handleSubmit(onSubmit)">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p style="width: 160px">Dealer Details</p>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <ValidationProvider name="dealer" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Dealer <span style="color: red">*</span></label>
                                                            <multiselect v-model="form.dealer"
                                                                         :options="allDealer"
                                                                         data-index="1"
                                                                         :multiple="false"
                                                                         @input="dealerDistrict"
                                                                         :close-on-select="true"
                                                                         :clear-on-select="false"
                                                                         :preserve-search="true"
                                                                         placeholder="Dealer"
                                                                         label="CustomerName" track-by="CustomerCode">

                                                            </multiselect>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="District" mode="eager" rules=""
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>District</label>
                                                            <input type="text" class="form-control" readonly
                                                                   name="District" v-model="form.district"
                                                                   style="min-height: 35px">
                                                            <div class="error" v-if="form.errors.has('district')"
                                                                 v-html="form.errors.get('district')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="OpenDate" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Opening Date</label>
                                                            <date-picker v-model="form.openDate"
                                                                         valueType="format"></date-picker>
                                                            <div class="error" v-if="form.errors.has('openDate')"
                                                                 v-html="form.errors.get('openDate')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="Service Area Volume" mode="eager"
                                                                        rules="required" v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Service Area Volume (Sqft)<span style="color: red">*</span></label>
                                                            <input type="text" class="form-control"
                                                                   name="serviceOpeningClosingTime"
                                                                   v-model="form.serviceAreaVolume"
                                                                   style="min-height: 35px">
                                                            <div class="error"
                                                                 v-if="form.errors.has('serviceAreaVolume')"
                                                                 v-html="form.errors.get('serviceAreaVolume')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="Service Opening & Closing Time" rules="required"
                                                                        mode="eager" v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Service Opening & Closing Time</label>
                                                            <input type="text" class="form-control"
                                                                   name="serviceOpeningClosingTime"
                                                                   v-model="form.serviceOpeningClosingTime"
                                                                   style="min-height: 35px">
                                                            <div class="error" v-if="form.errors.has('district')"
                                                                 v-html="form.errors.get('serviceOpeningClosingTime')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <ValidationProvider name="Bay" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Bay <span style="color: red">*</span></label>
                                                            <multiselect v-model="form.bay"
                                                                         :options="allBay"
                                                                         data-index="1"
                                                                         :multiple="false"
                                                                         :close-on-select="true"
                                                                         :clear-on-select="false"
                                                                         :preserve-search="true"
                                                                         placeholder="Bay"
                                                                         label="bayName" track-by="bayCode">

                                                            </multiselect>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="DateOfBirth" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Evaluation Date</label>
                                                            <date-picker v-model="form.evaluationDate"
                                                                         valueType="format"></date-picker>
                                                            <div class="error" v-if="form.errors.has('evaluationDate')"
                                                                 v-html="form.errors.get('evaluationDate')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>

                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <ValidationProvider name="Evaluation By" mode="eager"
                                                                        rules="required" v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>Evaluation By <span style="color: red">*</span></label>
                                                            <input type="text" class="form-control" data-required="true"
                                                                   name="EvaluationBy" v-model="form.evaluationBy"
                                                                   style="min-height: 35px">
                                                            <div class="error" v-if="form.errors.has('evaluationBy')"
                                                                 v-html="form.errors.get('evaluationBy')"/>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p style="width: 180px">Service Manpower</p>
                                                </div>
                                                <div class="col-12 col-md-2"
                                                     v-for="(singleDesignation, index) in allEvaluationDesignation">
                                                    <ValidationProvider name="Service Manpower" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group">
                                                            <label>{{ singleDesignation.DesignationName }}<span style="color: red">*</span></label>
                                                            <select name="allEvaluationDesignation" class="form-control"
                                                                    v-model="singleDesignation.DesignationDropDown">
                                                                <option value="">Select</option>
                                                                <option :value="item.id"
                                                                        v-for="(item, index) in singleDesignation.options"
                                                                        :key="index">{{ item.id }}
                                                                </option>
                                                            </select>
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p>Service4p Details</p>
                                                </div>
                                                <br>
                                                <br>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card"
                                                             v-for="singleEvaluationHeadGroup in allEvaluationHeadGroup">
                                                            <div class="table-responsive">
                                                                <table
                                                                        class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                                                    <thead style="background: #000000 !important; color: #FFFFFF!important;">

                                                                    <tr>
                                                                        <td style="background: #000000; color: #FFFFFF"
                                                                            colspan="7" class="text-center trhead">
                                                                            {{ singleEvaluationHeadGroup.HeadName }}
                                                                        </td>
                                                                    </tr>

                                                                    </thead>
                                                                    <tbody v-for="(singleallEvaluationHead,seIndex) in singleEvaluationHeadGroup.details">
                                                                    <tr>
                                                                        <td colspan="7" class="text-center trhead"
                                                                            style="background: #a6a69c;color: #000000">
                                                                            {{ singleallEvaluationHead.SeriveSubHead }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="7" class="text-center trhead"
                                                                            style="background: #cfcfca;color: #000000">
                                                                            {{
                                                                            singleallEvaluationHead.SeriveSubSubHead
                                                                            }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="7" class="text-center">
                                                                            {{
                                                                            singleallEvaluationHead.RequirmentDescription
                                                                            }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{
                                                                            seIndex + 1 + '-' + singleallEvaluationHead.RequirmentName
                                                                            }}
                                                                        </td>
                                                                        <td>
                                                                            <select style="width: fit-content"
                                                                                    name="allEvaluationDesignation"
                                                                                    class="form-control"
                                                                                    v-model="singleallEvaluationHead.selectedItem">
                                                                                <option value="">Select</option>
                                                                                <option
                                                                                        :value="singleEvaluationMethod.EvalutionMethodId"
                                                                                        v-for="(singleEvaluationMethod, index) in allEvaluationMethod"
                                                                                        :key="index">{{
                                                                                    singleEvaluationMethod.EvalutionMethodName
                                                                                    }}
                                                                                </option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio"
                                                                                   @change="calculateQuestionMark(seIndex,singleallEvaluationHead.Q1_CheckPointWeight)"
                                                                                   :value="singleallEvaluationHead.Q1"
                                                                                   v-model="singleallEvaluationHead.selectedRadio"
                                                                                   :id="'req'+seIndex"
                                                                                   v-if="singleallEvaluationHead.Q1 !==null">
                                                                            <span style="color: red;"
                                                                                  v-if="singleallEvaluationHead.Q1">
                                                                                {{ singleallEvaluationHead.Q1 }}</span>


                                                                        </td>
                                                                        <td>
                                                                            <input type="radio"
                                                                                   @change="calculateQuestionMark(seIndex,singleallEvaluationHead.Q2_CheckPointWeight)"
                                                                                   :value="singleallEvaluationHead.Q2"
                                                                                   v-model="singleallEvaluationHead.selectedRadio"
                                                                                   :id="'req'+seIndex+1"
                                                                                   v-if="singleallEvaluationHead.Q2 !==null">
                                                                            <span style="color: #d19a04;"
                                                                                  v-if="singleallEvaluationHead.Q2 !==null">{{
                                                                                singleallEvaluationHead.Q2
                                                                                }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio"
                                                                                   :value="singleallEvaluationHead.Q3"
                                                                                   @change="calculateQuestionMark(seIndex,singleallEvaluationHead.Q3_CheckPointWeight)"
                                                                                   v-model="singleallEvaluationHead.selectedRadio"
                                                                                   :id="'req'+seIndex+2"
                                                                                   v-if="singleallEvaluationHead.Q3!==null">
                                                                            <span style="color: green;"
                                                                                  v-if="singleallEvaluationHead.Q3!==null">{{
                                                                                singleallEvaluationHead.Q3
                                                                                }}</span>

                                                                        </td>

                                                                        <td>total -
                                                                            {{
                                                                            Math.max(
                                                                                singleallEvaluationHead.Q3_CheckPointWeight !== null ? parseInt(singleallEvaluationHead.Q3_CheckPointWeight) : 0,
                                                                                singleallEvaluationHead.Q2_CheckPointWeight !== null ? parseInt(singleallEvaluationHead.Q2_CheckPointWeight) : 0,
                                                                                singleallEvaluationHead.Q3_CheckPointWeight !== null ? parseInt(singleallEvaluationHead.Q3_CheckPointWeight) : 0
                                                                            )
                                                                            }}

                                                                        </td>
                                                                        <td>
                                                                            <input type="text" readonly
                                                                                   class="form-control"
                                                                                   v-model="singleallEvaluationHead.inputValue">
                                                                        </td>
                                                                    </tr>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                                            <tbody>
                                                            <tr>
                                                                <td style="font-size: 15px;text-align: end;font-weight: bold;"   class="text-end"  colspan="7">Total</td>
                                                                <td style="font-size: 15px;text-align: end;font-weight: bold;"   class="text-end">{{totalQuestionPoint}}</td>
                                                                <td style="font-size: 15px;text-align: end;font-weight: bold;"   class="text-end">{{actualQuestionPoint}}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-primary float-right submit_on_enter">Submit
                                    </button>
                                </form>
                            </ValidationObserver>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40"
                    objectbg="#999793" opacity="80" name="circular"></loader>
        </div>
    </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: "Invoice",
    mixins: [Common],
    components: {DatePicker},
    data() {
        return {
            allDealer: [],
            allBay: [],
            allEvaluationDesignation: [],
            allEvaluationDesignationScore: [],
            headerDatas: [],
            headers: [],
            contents: [],
            totalScore: [],
            allServiceEvaluationHeadTitle: [],
            allEvaluationMethod: [],
            allEvaluationHead: [],
            allEvaluationHeadGroup: [],
            checkServiceHeadTitle: '',
            checkServiceHeadTitleArr: [],
            totalQuestionPoint:0,
            actualQuestionPoint:0,
            query: '',
            pagination: {
                current_page: 1,
                from: 1,
                to: 1,
                total: 1,
            },
            actual: [],
            grade: 0,
            observations: [],
            form: new Form({
                dealer: '',
                district: '',
                evaluationBy: '',
                serviceOpeningClosingTime: '',
                serviceAreaVolume: '',
                bay: '',
                actual: [],
                designationScore: [],
                observations: [],
                score: [],
                openDate: moment().format('yyyy-MM-DD'),
                evaluationDate: moment().format('yyyy-MM-DD'),
            }),
            errors: [],
            isLoading: false,
            buttonShow: false,
            PreLoader: false,
        }
    },
    created() {
        //
    },
    computed: {
        //
    },
    mounted() {
        document.title = 'Service 4p Details | DMS';
        this.getSupportingData()
        this.getAllEvaluation()
        this.calTotalScore()
        for (let i = 0; i < 20; i++) {
            this.allBay.push(
                {
                    "bayCode": i,
                    "bayName": i
                }
            );
        }
        for (let i = 0; i < 10; i++) {
            this.allEvaluationDesignationScore.push(i);
        }
    },
    methods: {
        calEvaluationScore(item) {
            item.score = (parseInt(item.actual) / parseInt(item.Target)) * parseInt(item.Weight)
            this.calTotalScore(item.score);
        },
        calTotalScore(val) {
            if (val) {
                this.totalScore = []
                this.contents.map((val) => {
                    this.totalScore.push(val.score)
                })
            } else {
                this.totalScore = []
            }
        },
        getAllEvaluation() {
            this.isLoading = true
            this.form.Export = '';
            this.form.post(baseurl + "api/evaluation/service-sales", this.config()).then(response => {
                if (response.data.data.length > 0) {
                    this.contents = []
                    this.headerDatas = response.data.headerData
                    this.headers = Object.keys(response.data.data[0])
                    response.data.data.forEach((item) => {
                        item.actual = 0;
                        item.observations = '';
                        this.contents.push(item)
                    })
                    this.isLoading = false
                } else {
                    this.contents = []
                    this.exportShow = true;
                    this.isLoading = false
                }
            }).catch(e => {
                //
            });
        },
        getSupportingData() {
            this.axiosGet('evaluation/get-service-4p-supporting-data', (response) => {
                try {
                    this.allDealer = response.dealer;
                    this.allEvaluationDesignation = response.evaluationDesignation
                    this.allEvaluationDesignation.map((e) => {
                        e.options = []
                        for (let i = 0; i < 10; i++) {
                            e.options.push({
                                id: i
                            })
                        }
                    })
                    this.allServiceEvaluationHeadTitle = response.serviceEvaluationHeadTitle
                    this.allEvaluationMethod = response.evaluationMethod
                    // instance.allEvaluationHead = ''
                    this.allEvaluationHead = response.evaluationHead
                    let evaluationHeads = []
                    this.allEvaluationHead.forEach((head, i) => {
                        let maxQuestionPoint = Math.max(
                            head.Q3_CheckPointWeight !== null ? parseInt(head.Q3_CheckPointWeight) : 0,
                            head.Q2_CheckPointWeight !== null ? parseInt(head.Q2_CheckPointWeight) : 0,
                            head.Q3_CheckPointWeight !== null ? parseInt(head.Q3_CheckPointWeight) : 0
                        )
                       this.totalQuestionPoint += maxQuestionPoint

                        let ev = evaluationHeads.find((e) => {
                            return e.HeadId === head.ServiceHeadID
                        })
                        if (ev) {
                            ev.details.push(head)
                        } else {
                            evaluationHeads.push({
                                HeadId: head.ServiceHeadID,
                                HeadName: head.ServiceHead,
                                details: [head]
                            })
                        }
                    })
                    this.allEvaluationHeadGroup = evaluationHeads
                } catch (e) {
                    console.log(e)
                }
            }, function (error) {
            });
        },
        dealerDistrict(val) {
            let district = val.CustomerCode;
            this.axiosPost('evaluation/region-data', {
                dealerCode: district
            }, (response) => {
                if(response.data.length>0){
                    this.form.districtName = response.data[0].RegionName
                    this.form.district = response.data[0].RegionName
                }

            })
        },
        calculateQuestionMark(questionIndex, FieldVal) {
            this.allEvaluationHead[questionIndex].inputValue = parseInt(FieldVal)
            console.log(this.allEvaluationHead[questionIndex].inputValue)
            this.calculateTotalQuestionMark()
        },
        calculateTotalQuestionMark(){
            let totalQuestionMarkTemp = 0
            this.allEvaluationHead.forEach((item)=>{
               if(item.inputValue!==null && item.inputValue){
                   totalQuestionMarkTemp  += parseInt(item.inputValue)
               }
            })
            this.actualQuestionPoint = totalQuestionMarkTemp;

        },
        onSubmit() {
             console.log(this.form.dealer, "allEvaluationHeadGroup")
            // console.log(this.allEvaluationDesignation, "allEvaluationDesignation")
            this.axiosPost('evaluation/service4p-store', {
                dealer: this.form.dealer,
                district: this.form.district,
                openDate: this.form.openDate,
                serviceAreaVolume: this.form.serviceAreaVolume,
                serviceOpeningClosingTime: this.form.serviceOpeningClosingTime,
                bay: this.form.bay,
                evaluationDate: this.form.evaluationDate,
                evaluationBy: this.form.evaluationBy,
                allEvaluationDesignation: this.allEvaluationDesignation,
                allEvaluationHeadGroup: this.allEvaluationHeadGroup,
                allData: this.form,
            }, (response) => {
                if (response.status === 'Success') {
                    this.successNoti(response.message);
                    this.$router.push({name: 'service4pDetailsPart2',params: {evaluationId:response.evaluationId}})

                } else {
                    this.errorNoti(response.message);
                }
            })
        },

        formatHeading(item) {
            let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
            let title = item.replace(rex, '$1$4 $2$3$5')
            return title.replace('_', ' ')
        },
        isInt(value) {
            return !isNaN(parseInt(value * 1))
        },
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: {Authorization: `Bearer ${token}`}
            };
        },
    }
}
</script>

<style scoped>
#customer_form .form-control {
    font-size: 10px;
    height: 29px;
}

#customer_form .form-group {
    margin-bottom: 0;
}

#customer_form label {
    font-size: 11px !important;
}

.form-divider {
    padding: 6px 5px 5px 5px;
    border: 1px solid #4d87f64f;
    border-radius: 13px;
    margin: 0 auto;
}

#invoice2 .auto-complete2 {
    position: relative;
    display: block;
}

#invoice2 .auto-complete2 ul {
    list-style: none;
    margin: 0;
    padding: 5px 0 0 0px;
    position: absolute;
    width: 100%;
    border: 1px solid #0000000d;
    background: #ffffff;
    max-height: 200px;
    overflow-y: scroll;
    z-index: 999;
}

#invoice2 .auto-complete2 ul li {
    border-bottom: 1px solid #b7b7b7;
    background: #cbc4c4;
    padding: 5px;
    cursor: pointer;
}

#invoice2 .auto-complete2 ul li a {
    color: #000000;
}

#invoice2 .auto-complete2 ul li:hover {
    background: #fff3cd;
}

#invoice2 :focus {
    background: #fff3cd;
}

.form-divider-title {
    position: relative;
    top: -20px;
}

.form-divider-title p {
    position: absolute;
    padding: 0 25px;
    background: #ffffff;
    text-transform: uppercase;
    font-weight: bold;
    color: #4d79f6b5 !important;
    font-size: 12px;
}

.tableFixHead {
    overflow-y: auto; /* make the table scrollable if height is more than 200 px  */
    height: 200px; /* gives an initial height of 200px to the table */
}

.tableFixHead thead th {
    position: sticky; /* make the table heads sticky */
    top: 0px; /* table head will be placed from the top of the table and sticks to it */
}

table {
    border-collapse: collapse; /* make the table borders collapse to each other */
    width: 100%;
}

th,
td {
    padding: 8px 16px;
    border: 1px solid #ccc;
}

th {
    background: #000000;
}

</style>