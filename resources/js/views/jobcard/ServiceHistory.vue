<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">
            <breadcrumb :options="['Service History']">
            </breadcrumb>
            <div class="row">
                <div class="col-md-12">
                    <ValidationObserver v-slot="{ handleSubmit }">
                        <div>
                            <form class="form-horizontal" style="padding-bottom: 30px"
                                  @submit.prevent="handleSubmit(searchServiceHistory)">
                                <div class="col-md-8">
                                    <ValidationProvider name="Chassis No" mode="eager"
                                                        rules="required"
                                                        v-slot="{ errors }">
                                        <div class="form-group row">
                                            <div class="col-md-2">
                                                <label for="chassisNo" class="col-lg-9 col-form-label">Chassis
                                                    no <span class="error">*</span></label>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="col-lg-12">
                                                    <input type="text" class="form-control"
                                                           id="chassisNo"
                                                           data-chassisNo="true"
                                                           v-model="chassisNo" name="chassisNo"
                                                           placeholder="Chassis No">
                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="col-lg-3">
                                                    <submit-form name="Search"/>
                                                </div>
                                            </div>
                                        </div>
                                    </ValidationProvider>
                                </div>

                            </form>
                        </div>
                    </ValidationObserver>
                </div>

            </div>

            <div id="serviceHistoryInfo" v-if="historyDataTag">
                <!-- Customer information -->
                <div class="row servInfo">
                    <div class="col-md-12">
                        <div class="x_panel table-responsive">
                            <div class="x_title" style="background: #d9edf7">
                                <span style="width: 65px; font-size: 20px; font-weight: bold; color:black;">Customer Information</span>
                                <div class="clearfix"></div>
                            </div>
                            <table class="table table-striped table-bordered small">
                                <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th class="text-center">Phone Number</th>
                                    <th class="text-center">Sold Dealer</th>
                                    <th class="text-center">Sold Date</th>
                                    <th class="text-center">Model</th>
                                </tr>
                                </thead>
                                <tbody id="custBody">
                                <td class="text-center">{{ customerInfo.CustomerName }}</td>
                                <td class="text-center"> {{ customerInfo.MobileNo }}</td>
                                <td class="text-center">{{ customerInfo.SoldDealer }}</td>
                                <td class="text-center">{{ moment(customerInfo.InvoiceDate).format('YYYY-MM-DD') }}
                                </td>
                                <td class="text-center">{{ customerInfo.Model }}</td>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Service information -->
                <div class="row servInfo">
                    <div class="col-md-12">
                        <div class="x_panel table-responsive">
                            <div class="x_title" style="background: #d9edf7">
                                <span style="width: 65px; font-size: 20px; font-weight: bold; color:black;">Service Information</span>
                                <div class="clearfix"></div>
                            </div>
                            <table class="table table-striped table-bordered small">
                                <thead>
                                <tr>
                                    <th>Service Dealer</th>
                                    <th class="text-center">Service Date</th>
                                    <th class="text-center">Job Card Number</th>
                                    <th class="text-center">Job Type</th>
                                    <th class="text-center">Schedule Title</th>
                                    <th class="text-center">Mileage</th>
                                    <th class="text-center">Print</th>
                                    <th class="text-center" v-if="me.RoleId==='sa'">Action</th>
                                </tr>
                                </thead>
                                <tbody id="histBody" v-for="(service, i) in serviceInfo" v-if="serviceInfo.length">
                                <td class="text-center">{{ service.CustomerName }}</td>
                                <td class="text-center"> {{ moment(service.JobDate).format('YYYY-MM-DD') }}</td>
                                <td class="text-center">{{ service.JobCardNo }}</td>
                                <td class="text-center">{{ service.JobTypeName }}</td>
                                <td class="text-center">{{ service.ScheduleTitle }}</td>
                                <td class="text-center">{{ service.Mileage }}</td>
                                <td class="text-center">
                                    <router-link class="btn btn-primary" style="font-size: 12px;width:65px;padding: 0"
                                                 :to="{path: `${baseUrl}`+'job-card-print?action_type=print&jobCardNo='+encodeConvert(service.JobCardNo)}">
                                        <i class="fa fa-print">Print</i>
                                    </router-link>
                                </td>
                                <td class="text-center" v-if="me.RoleId==='sa'">

                                    <button type="button" style="padding: 2px 5px"
                                            @click="serviceHistoryEdit(service.JobCardNo,service.Mileage,customerInfo.DelaerCode)"
                                            class="btn btn-danger btn-sm"><i class="fa fa-edit">Edit</i></button>
                                </td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal-->
                <div class="modal fade" id="edit-service-history" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-title modal-title-font" id="exampleModalLabel">Edit- <span
                                        style="color: #0f6674">{{ editJobCardNo }}</span></div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        @click="closeModal">
                                    Close
                                </button>
                            </div>
                            <div class="modal-body" v-if="editModal">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Millage</label>
                                            <div class="col-lg-9">
                                                <ValidationProvider name="editModal" mode="eager"
                                                                    rules="required" v-slot="{ errors }">
                                                    <input type="text" class="form-control"
                                                           id="serial"
                                                           data-chassisNo="true"
                                                           v-model="editMillage"
                                                           name="editMillage"
                                                           placeholder="Millage">
                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                </ValidationProvider>
                                            </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label for="mileage" class="col-lg-3 col-form-label">
                                                Problem Details </label>
                                            <div class="col-lg-9">
                                                <multiselect v-model="problemId" :options="allProblem"
                                                             :multiple="true"
                                                             :close-on-select="true"
                                                             :clear-on-select="false"
                                                             :preserve-search="false"
                                                             placeholder="Select ProblemDetails"
                                                             label="ProblemStatement" track-by="PSID">

                                                </multiselect>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label for="otherProblem" class="col-lg-3 col-form-label">Other
                                                Problem </label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control"
                                                       id="otherProblem"
                                                       v-model="otherProblem" name="brand"
                                                       placeholder="Other Problem ">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label for="reasonAndProblemDetails"
                                                   class="col-lg-3 col-form-label">Reason of Problem and
                                                Repair
                                                Details </label>
                                            <div class="col-lg-9">
                                                         <textarea id="reasonAndProblemDetails"
                                                                   style="width: 100%"
                                                                   v-model="reasonAndProblemDetails">

                                                         </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12" style="text-align: end;margin-top:5px">
                                    <button type="button"
                                            class="submit-button btn btn-gradient btn-sm w-md waves-effect waves-light"
                                            style="padding: 2px 5px" @click="submitServiceHistory">Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            chassisNo: '',
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl,
            customerInfo: {},
            serviceInfo: [],
            historyDataTag: false,
            editModal: false,
            editJobCardNo: '',
            editMillage: 0,
            updateMillage: 0,
            allProblem: [],
            problemId: '',
            otherProblem: '',
            reasonAndProblemDetails: ''
        }
    },
    mounted() {
        bus.$off('changeStatus', function () {
            this.changeStatus()
        })
        this.getSupportingData()
    },
    computed: {
        me() {
            return this.$store.state.me
        }
    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        encodeConvert(val) {
            let convertVal = btoa(val);
            return convertVal
        },
        getSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/supporting-data', function (response) {
                instance.allProblem = response.allProblems;
            }, function (error) {
            });
        },
        closeModal() {
            $("#edit-service-history").modal("toggle");
        },
        searchServiceHistory() {
            let instance = this;
            if (this.chassisNo.length > 1) {
                this.axiosGet('jobCard/check-service-history/' + instance.chassisNo, function (response) {
                    var serviceHistoryInfo = response.serviceHistory
                    if (serviceHistoryInfo.length > 0 || instance.customerInfo.length > 0) {
                        instance.serviceInfo = serviceHistoryInfo
                        instance.customerInfo = response.customerInfo
                        instance.historyDataTag = true

                    } else {
                        console.log("fdgg")
                        instance.serviceInfo = []
                        instance.customerInfo = {}
                        instance.historyDataTag = false
                        instance.errorNoti('Chassis not found');
                    }

                }, function (error) {
                });
            }
        },
        serviceHistoryEdit(jobCardNo, millage, dealerCode) {
            $("#edit-service-history").modal("toggle");
            let instance = this
            instance.editJobCardNo = jobCardNo
            instance.editModal = true
            instance.editMillage = millage
            this.axiosGet('jobCard/check-service-history-problem-details/' + jobCardNo + '/' + dealerCode, function (response) {
                let problemEditArray = []
                let existingJobCardInfo = response.existingJobCard[0]
                console.log(existingJobCardInfo.ProblemDetails)
                response.existingJobCard.forEach((item) => {
                    if (item.ProblemDetailsName && item.ProblemDetailsId) {
                        let newObj = {
                            ProblemStatement: item.ProblemDetailsName,
                            PSID: item.ProblemDetailsId
                        }
                        problemEditArray.push(newObj)
                    }

                })
                instance.problemId = problemEditArray.length > 0 ? problemEditArray : []
                instance.otherProblem = existingJobCardInfo.ProblemDetails
                instance.reasonAndProblemDetails = existingJobCardInfo.ReasonProlemRepairDetails

            }, function (error) {
            });
        },
        submitServiceHistory() {
            var submitUrl = 'jobCard/update-service-history';
            this.axiosPost(submitUrl, {
                jobCardNo: this.editJobCardNo,
                editMillage: this.editMillage,
                problemId: this.problemId,
                otherProblem: this.otherProblem,
                reasonAndProblemDetails: this.reasonAndProblemDetails,

            }, (response) => {
                $("#edit-service-history").modal("toggle");
                this.successNoti(response.message);
                location.reload();
            }, (error) => {
                this.errorNoti(error);
            })
        }
    }
}
</script>

<style scoped>

#jobCard table tr td:nth-child(12) {
    width: 145px !important;
}
</style>
