<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="{title}">
                <router-link class="btn btn-primary" :to="{name:'JobCardIndex'}">Back</router-link>
            </breadcrumb>
            <!-- end row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <ValidationObserver v-slot="{ handleSubmit }">
                                <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction"
                                      @submit.prevent="handleSubmit(onSubmit)">
                                    <div class="content clearfix"
                                         style="margin-top: 5px;margin-bottom:0px !important;padding-bottom: 0px !important;">
                                        <h3 id="form-horizontal-h-3" tabindex="-1" class="title">Job Card
                                            information</h3>

                                        <fieldset id="form-horizontal-p-0" role="tabpanel"
                                                  aria-labelledby="form-horizontal-h-0" class="body current"
                                                  aria-hidden="false">

                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"></div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ValidationProvider name="job Card No" mode="eager"
                                                                        rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="jobCardNo" class="col-lg-3 col-form-label">Job
                                                                Card
                                                                No <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <input type="text" class="form-control"
                                                                       id="jobCardNo"
                                                                       readonly
                                                                       data-required="true"
                                                                       v-model="jobCardNo" name="jobCardNo"
                                                                       placeholder="Job Card No">
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="job Date" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="jobDate" class="col-lg-3 col-form-label">Job
                                                                Date <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <input type="date" class="form-control"
                                                                       id="jobDate"
                                                                       readonly
                                                                       data-required="true"
                                                                       v-model="jobDate" name="jobDate">
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Chassis No" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="chassisNo" class="col-lg-3 col-form-label">Chassis
                                                                no <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="chassisNo"
                                                                             :options="allchassisNo"
                                                                             :multiple="false"
                                                                             :disabled="actionType==='edit'?true:false"
                                                                             @search-change="checkChassisNo($event,'bi')"
                                                                             @input="setBikeInfo($event)"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="true"
                                                                             placeholder="Chassis No"
                                                                             label="chassisno" track-by="chassisno">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="engineNo" class="col-lg-3 col-form-label">Engine
                                                            no</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="engineNo"
                                                                   readonly
                                                                   data-chassisNo="true"
                                                                   v-model="engineNo" name="engineNo"
                                                                   placeholder="Engine no">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Customer Name" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                    <div class="form-group row">
                                                        <label for="customerName" class="col-lg-3 col-form-label">Customer
                                                            Name <span class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="customerName"
                                                                   readonly
                                                                   data-chassisNo="true"
                                                                   v-model="customerName" name="customerName"
                                                                   placeholder="Customer Name">
                                                            <span class="error-message"> {{ errors[0] }}</span>
                                                        </div>
                                                    </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Mobile No" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="mobileNo" class="col-lg-3 col-form-label">Mobile
                                                                no <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <input type="tel" pattern='(01)?[0-9]{11}'
                                                                       class="form-control"
                                                                       data-index="2"
                                                                       id="mobileNo"
                                                                       data-required="true"
                                                                       v-model="mobileNo" name="mobileNo"
                                                                       placeholder="Mobile no">
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>

                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="brand" class="col-lg-3 col-form-label">Brand</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="brand"
                                                                   readonly
                                                                   data-chassisNo="true"
                                                                   v-model="brand" name="brand" placeholder="Brand">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="model" class="col-lg-3 col-form-label">Model</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="model"
                                                                   readonly
                                                                   data-chassisNo="true"
                                                                   v-model="model" name="brand" placeholder="Model">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="purchaseDate" class="col-lg-3 col-form-label">Purchase
                                                            Date</label>
                                                        <div class="col-lg-9">
                                                            <input type="date" class="form-control"
                                                                   readonly
                                                                   id="purchaseDate"
                                                                   data-chassisNo="true"
                                                                   v-model="purchaseDate" name="brand"
                                                                   placeholder="Purchase Date">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Under
                                                            Warrenty</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control" name="underWarrenty"
                                                                    readonly
                                                                    v-model="underWarrenty">
                                                                <option value="Y">Yes</option>
                                                                <option value="N">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label for="registrationNo" class="col-lg-3 col-form-label">Registration
                                                            No</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   data-index="3"
                                                                   id="registrationNo"
                                                                   data-chassisNo="true"
                                                                   v-model="registrationNo" name="brand"
                                                                   placeholder="Registration No">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Serial No <span
                                                                class="error">*</span></label>

                                                        <div class="col-lg-3">
                                                            <ValidationProvider name="serial" mode="eager"
                                                                                rules="required" v-slot="{ errors }">
                                                                <input type="text" class="form-control"
                                                                       data-index="5"
                                                                       id="serial"
                                                                       data-chassisNo="true"
                                                                       v-model="serial" name="serial"
                                                                       placeholder="Serial">
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </ValidationProvider>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Mileage" mode="eager"
                                                                        :rules="`required|min_value:1`"
                                                                        v-slot="{ errors }">

                                                        <div class="form-group row">
                                                            <label for="mileage" class="col-lg-3 col-form-label">Mileage
                                                                <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <input type="number" class="form-control"
                                                                       data-index="6"
                                                                       @change="checkMillage($event)"
                                                                       id="mileage"
                                                                       data-chassisNo="true"
                                                                       v-model="mileage" name="mileage"
                                                                       placeholder="Mileage">
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Technician Name" mode="eager"
                                                                        rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="technicianName" class="col-lg-3 col-form-label">Technician
                                                                Name <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="technicianCode"
                                                                             data-index="7"
                                                                             :options="allTechnician"
                                                                             @input="setBay($event)"
                                                                             :multiple="false"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="false"
                                                                             placeholder="Select Technician"
                                                                             label="Details" track-by="TechnicianCode">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Bay" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="bay" class="col-lg-3 col-form-label">Bay <span
                                                                    class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="bayCode" :options="allBay"
                                                                             :multiple="false"
                                                                             data-index="8"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="false"
                                                                             placeholder="Select Bay"
                                                                             label="Details" track-by="BayCode">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Job Status " mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="jobStatus" class="col-lg-3 col-form-label">Job
                                                                Status <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="jobStatus" :options="allJobStatus"
                                                                             :multiple="false"
                                                                             data-index="9"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="false"
                                                                             placeholder="Select Job Status"
                                                                             label="StatusName" track-by="StatusCode">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Job Type" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="jobType" class="col-lg-3 col-form-label">Job
                                                                Type
                                                                <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="jobType" :options="parentJobType"
                                                                             :multiple="false"
                                                                             :disabled="actionType==='edit'"
                                                                             @input="setServiceNo($event)"
                                                                             data-index="10"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="false"
                                                                             placeholder="Job Type"
                                                                             label="JobTypeName" track-by="Id">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Job Status " mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label class="col-lg-3 col-form-label">Service No <span
                                                                    class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <multiselect v-model="ServiceNo" :options="childJobType"
                                                                             v-if="showElements"
                                                                             :multiple="false"
                                                                             data-index="11"
                                                                             :disabled="childJobTypeStatusReadOnly ||actionType==='edit'"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="true"
                                                                             :preserve-search="true"
                                                                             placeholder="Job Type"
                                                                             label="JobTypeName" track-by="Id">

                                                                </multiselect>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ValidationProvider name="Diagnose status" mode="eager" rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="ytdStatus" class="col-lg-3 col-form-label">Diagnose
                                                                status <span class="error">*</span></label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="ytdStatus"
                                                                        data-index="12"
                                                                        @change="setYdtNoReason($event)"
                                                                        v-model="ytdStatus">
                                                                    <option value="Y">Yes</option>
                                                                    <option value="N">No</option>
                                                                </select>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-4" v-if="!ytdNoTag">
                                                    <div class="form-group row">
                                                        <label for="ydTFile" class="col-lg-3 col-form-label">Diagnose
                                                            File</label>
                                                        <div class="col-lg-9">
                                                            <input type="file" class="form-control"
                                                                   @change="fileUpload($event)"
                                                                   id="ydTFile"
                                                                   name="ydTFile">
                                                        </div>
                                                    </div>
                                                </div>
<<<<<<< HEAD
                                                <div class="col-md-4" v-if="ytdNoTag">
                                                    <div class="form-group row">
                                                        <label for="failureAnalysis" class="col-lg-3 col-form-label">
                                                            Reason of Diagnose <span class="error">*</span> </label>
                                                        <div class="col-lg-9">
                                                            <multiselect v-model="reasonOfYDT" :options="ytdNoReason"
                                                                         :multiple="false"
                                                                         :close-on-select="true"
                                                                         :clear-on-select="false"
                                                                         :preserve-search="false"
                                                                         placeholder="Reason Of Ytd"
                                                                         label="Ytd_Stauts_Reason" track-by="Id">

                                                            </multiselect>
                                                        </div>
                                                    </div>
                                                </div>
=======
>>>>>>> 4ddd6a18cd87f9ba645e2cb44db0c2664c5d3344
<!--                                                <div class="col-md-4">-->
<!--                                                    <ValidationProvider name="FI Status" mode="eager" rules="required"-->
<!--                                                                        v-slot="{ errors }">-->
<!--                                                        <div class="form-group row">-->
<!--                                                            <label for="fiStatus" class="col-lg-3 col-form-label">FI-->
<!--                                                                Status-->
<!--                                                                <span class="error">*</span></label>-->
<!--                                                            <div class="col-lg-9">-->
<!--                                                                <select class="form-control" name="ytdStatus"-->
<!--                                                                        @change="setFiNoReason($event)"-->
<!--                                                                        v-model="fiStatus">-->
<!--                                                                    <option value="Y">Yes</option>-->
<!--                                                                    <option value="N">No</option>-->
<!--                                                                </select>-->
<!--                                                                <span class="error-message"> {{ errors[0] }}</span>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </ValidationProvider>-->
<!--                                                </div>-->
                                            </div>
                                            <div class="row" v-if="fiNoTag">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="failureAnalysis" class="col-lg-3 col-form-label">FAILURE
                                                            Reason of FI <span class="error">*</span></label>
                                                        <div class="col-lg-9">
                                                            <multiselect v-model="reasonOfFI" :options="fiNoReason"
                                                                         :multiple="false"
                                                                         :close-on-select="true"
                                                                         :clear-on-select="false"
                                                                         :preserve-search="false"
                                                                         placeholder="Reason Of FI"
                                                                         label="Ytd_Stauts_Reason" track-by="Id">

                                                            </multiselect>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="Address"
                                                               class="col-lg-3 col-form-label">Address</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="ydTFile"
                                                                   v-model="address"
                                                                   name="address">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <label for="failureAnalysis" class="col-lg-3 col-form-label">FAILURE
                                                            ANALYSIS & DIAGNOSIS</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control"
                                                                   id="failureAnalysis"
                                                                   v-model="failureAnalysis"
                                                                   name="failureAnalysis">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
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
                                                <div class="col-md-6">
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
                                                <div class="col-md-6">
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
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <ValidationProvider name="Time Required (Minute)" mode="eager"
                                                                        rules="required"
                                                                        v-slot="{ errors }">
                                                        <div class="form-group row">
                                                            <label for="timeReqMin" class="col-lg-6 col-form-label">Time
                                                                Required (Minute)<span class="error">*</span></label>
                                                            <div class="col-lg-6">
                                                                <input type="number" class="form-control"
                                                                       id="timeReqMin"
                                                                       v-model="timeReqMin"
                                                                       name="timeReqMin">
                                                            </div>
                                                        </div>
                                                    </ValidationProvider>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group row">
                                                        <label for="timeTaken" class="col-lg-6 col-form-label">Time
                                                            Taken</label>
                                                        <div class="col-lg-6">
                                                            <input type="number" class="form-control"
                                                                   readonly
                                                                   id="timeTaken"
                                                                   v-model="timeTaken"
                                                                   name="timeTaken">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group row">
                                                        <label for="startTime" class="col-lg-6 col-form-label">Start
                                                            Time</label>
                                                        <div class="col-lg-6">
                                                            <input type="number" class="form-control"
                                                                   id="startTime"
                                                                   readonly
                                                                   v-model="startTime"
                                                                   name="startTime">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group row">
                                                        <label for="endTime" class="col-lg-6 col-form-label"> End
                                                            Time</label>
                                                        <div class="col-lg-6">
                                                            <input type="number" class="form-control"
                                                                   id="endTime"
                                                                   readonly
                                                                   v-model="endTime"
                                                                   name="endTime">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h3 id="form-horizontal-h-3" class="title">Parts information</h3>
                                        <div class="card table-graphics" style="padding-top:0px">
                                            <div class="card-body table-overlay" style="padding: 0px">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button style="float: right;margin-bottom: 5px;font-size: 13px;
                                                     padding: 10px;" id="add-row"
                                                                type="button"
                                                                class="btn btn-success btn-sm"
                                                                @click="addPartsFieldRow">Add
                                                            Parts Row
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                                                        <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:25%">Part</th>
                                                            <th style="width:6%">Current Stock</th>
                                                            <th style="width:9%">Quantity</th>
                                                            <th style="width:9%"> Unit Price</th>
                                                            <th style="width:9%"> Total Price</th>
                                                            <th style="width:9%">Service Charge</th>
                                                            <th style="width:9%">Discount(%)</th>
                                                            <th style="width:9%">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(partsField,partsIndex) in partsFields"
                                                            :key="partsIndex">
                                                            <td>
                                                                <multiselect v-model="partsField.partsCode"
                                                                             :options="parts"
                                                                             :multiple="false"
                                                                             @search-change="checkSpareParts($event,partsIndex)"
                                                                             @input="setPartsInfo($event,partsIndex)"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="true"
                                                                             placeholder="Select Parts"
                                                                             label="FullProduct" track-by="ProductCode">
                                                                </multiselect>
                                                            </td>
                                                            <td style="text-align: end;">
                                                            <span style="background: rgb(2 164 153);
                                                            font-weight: 700;padding: 6px 15px;
                                                            color: #ffffff;
                                                            border-radius: 5px;">
                                                             {{ partsField.currentStock }}
                                                            </span>

                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="partsField.quantity"
                                                                       @input="setTotalPartsPrice(partsIndex)"
                                                                       v-model="partsField.quantity"
                                                                       name="partsField.quantity">

                                                                <span class="error"
                                                                      v-if="errors[partsIndex] !== undefined && errors[partsIndex].quantity !== undefined">{{
                                                                    errors[partsIndex].quantity
                                                                    }}</span>
                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="partsField.unitPrice"
                                                                       readonly
                                                                       v-model="partsField.unitPrice"
                                                                       name="partsField.unitPrice">
                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="partsField.totalPrice"
                                                                       readonly
                                                                       v-model="partsField.totalPrice"
                                                                       name="partsField.totalPrice">
                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="partsField.serviceCharge"
                                                                       @input="setTotalPartsPrice(partsIndex)"
                                                                       v-model="partsField.serviceCharge"
                                                                       name="partsField.serviceCharge">
                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="partsField.discount"
                                                                       @input="setTotalPartsPrice(partsIndex)"
                                                                       v-model="partsField.discount"
                                                                       name="partsField.discount">
                                                                <span class="error"
                                                                      v-if="errors[partsIndex] !== undefined && errors[partsIndex].discount !== undefined">{{
                                                                    errors[partsIndex].discount
                                                                    }}</span>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                        @click="removeFieldsRow(partsIndex)">Remove
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="text-align:right;font-weight:bold">
                                                                Total Price: {{ partsFieldTotalPrice }}
                                                            </td>
                                                            <td style="text-align:right;;font-weight:bold">
                                                                Service Charge: {{ partsFieldTotalServiceCharge }}
                                                            </td>
                                                            <td style="text-align:right;;font-weight:bold">
                                                                Discount Price: {{ partsFieldTotalDiscount }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="text-align:right;font-weight:bold">
                                                                Grand total
                                                            </td>
                                                            <td colspan="2" style="text-align:right;font-weight:bold">
                                                                {{ partsFieldGrandTotal }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3 id="form-horizontal-h-3" class="title">Service Charge</h3>
                                        <div class="card table-graphics" id="serviceChargeJobCard"
                                             style="padding-top:0px">
                                            <div class="card-body table-overlay" style="padding: 0px">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button style="float: right;margin-bottom: 5px;
                                                    border:#626ed4;
                                                    font-size: 13px;
                                                    padding: 10px;
                                                    background: #626ed4" id="add-row"
                                                                type="button"
                                                                class="btn btn-success btn-sm"
                                                                @click="addServiceFieldRow">
                                                            Add Service Row
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive scrollable">
                                                    <table
                                                            class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                                                        <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:20%">Work</th>
                                                            <th style="width:9%"> Unit Price</th>
                                                            <th style="width:9%"> Discount(%)</th>
                                                            <th style="width:9%">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(serviceField,serviceIndex) in serviceFields"
                                                            :key="serviceIndex">
                                                            <td style="min-height: 100px !important;">
                                                                <multiselect v-model="serviceField.workCode"
                                                                             :options="works"
                                                                             :multiple="false"
                                                                             @search-change="checkServices($event,serviceIndex)"
                                                                             @input="setServiceInfo($event,serviceIndex)"
                                                                             :close-on-select="true"
                                                                             :clear-on-select="false"
                                                                             :preserve-search="true"
                                                                             placeholder="Select Work"
                                                                             label="FullProduct" track-by="WorkCode">

                                                                </multiselect>

                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       readonly
                                                                       id="partsField.unitPrice"
                                                                       v-model="serviceField.unitPrice"
                                                                       name="partsField.unitPrice">
                                                            </td>
                                                            <td style="text-align: end">
                                                                <input type="number" class="form-control"
                                                                       id="serviceField.discount"
                                                                       @input="calculateAllServiceInfo"
                                                                       v-model="serviceField.serviceDiscount"
                                                                       name="serviceField.discount">
                                                                <span class="error"
                                                                      v-if="errors[serviceIndex] !== undefined && errors[serviceIndex].serviceDiscount !== undefined">{{
                                                                    errors[serviceIndex].serviceDiscount
                                                                    }}</span>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                        @click="removeServiceRow(serviceIndex)">Remove
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="text-align:right;;font-weight:bold">
                                                                Discount Price
                                                            </td>
                                                            <td colspan="1" style="font-weight:bold">
                                                                {{ serviceFieldTotalDiscount }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="text-align:right;font-weight:bold">
                                                                Grand total
                                                            </td>
                                                            <td colspan="1" style="font-weight:bold">
                                                                {{ serviceFieldGrandTotal }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
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

            <!-- end row -->

            <!-- Last Service History Modal-->
            <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="modal-title modal-title-font" id="exampleModalLabel">Last Service History</div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
                                Close
                            </button>
                        </div>


                        <div class="modal-body">
                            <div class="table-responsive scrollable">
                                <table
                                        class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                                    <thead class="thead-dark">
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th style="width: 50%;color: #0b0b0b">Dealer Name</th>
                                        <td>{{ serviceHistoryDealerName }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%;color: #0b0b0b">Last Service Type</th>
                                        <td>{{ serviceHistoryLastServiceType }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%;color: #0b0b0b">Last Schedule Title</th>
                                        <td>{{ serviceHistoryLastScheduleTitle }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%;color: #0b0b0b">Last Service Date</th>
                                        <td>{{ serviceHistoryLastServiceDate }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%;color: #0b0b0b">Last Feedback Rating</th>
                                        <td>{{ serviceHistoryLastFeedbackRating }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div>
                <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2"
                        bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
            </div>
        </div>
        <!-- container-fluid -->
    </div>

</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";
import {value} from "lodash/seq";

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
            duplicateErrors: [],
            dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            jobDate: moment().format('YYYY-MM-DD'),
            jobCardNo: '',
            chassisNo: '',
            serviceCenterCode: '',
            allchassisNo: [],
            freeServices: [],
            engineNo: '',
            customerName: '',
            mobileNo: '',
            brand: '',
            model: '',
            purchaseDate: '',
            underWarrenty: '',
            registrationNo: '',
            bookingNo: '',
            serial: '',
            mileage: 0,
            technicianCode: '',
            bayCode: '',
            jobStatus: '',
            allJobType: [],
            parentJobType: [],
            childJobType: [],
            childJobTypeStatusReadOnly: false,
            jobType: '',
            ServiceNo: '',
            ytdStatus: '',
            ydTFile: '',
            AttachmentFlag: 0,
            fiStatus: '',
            address: '',
            failureAnalysis: '',
            problemId: '',
            otherProblem: '',
            reasonAndProblemDetails: '',
            timeReqMin: 0,
            timeTaken: 0,
            startTime: 0,
            endTime: 0,
            discountType: '',
            discount: 0,
            staffId: '',
            ytdNoReason: [],
            fiNoReason: [],
            ytdNoTag: false,
            fiNoTag: false,
            reasonOfYDT: '',
            reasonOfFI: '',
            PreLoader: false,


            read: true,
            allTechnician: [],
            allBay: [],
            allProblem: [],

            partsFields: [],
            partsFieldTotalPrice: 0,
            partsFieldTotalServiceCharge: 0,
            partsFieldTotalDiscount: 0,
            partsFieldGrandTotal: 0,

            serviceFields: [],
            serviceFieldTotalDiscount: 0,
            serviceFieldGrandTotal: 0,
            reference: '',

            serviceHistoryDealerName: '',
            serviceHistoryLastServiceType: '',
            serviceHistoryLastScheduleTitle: '',
            serviceHistoryLastServiceDate: '',
            serviceHistoryLastFeedbackRating: '',
            serviceHistoryLastMillage: '',
            parts: [],
            works: [],
            allJobStatus: [],
            workName: '',
            workRate: 0,
            comments: '',
            active: '',
            workCode: '',
            allReference: [],
            showElements: true
        }
    },
    computed: {},
    created() {
    },
    mounted() {
        $(`[data-index="${1}"]`).focus()
        this.addPartsFieldRow();
        this.addServiceFieldRow();
        this.getSupportingData();
        if (this.$route.query.action_type === 'edit') {
            let instance = this;
            let JobCardNo = this.decodeConvert(this.$route.query.jobCardNo)
            this.axiosGet('jobCard/get/jobCard/modal/' + JobCardNo, function (response) {
                console.log(response)
                instance.title = 'Update Job Card';
                instance.buttonText = "Update";
                instance.buttonShow = true;
                instance.actionType = 'edit';
                if (response.existingJobCard[0]) {

                    //Job Card
                    let existingJobCardInfo = response.existingJobCard[0]
                    instance.jobCardNo = existingJobCardInfo.JobCardNo
                    instance.jobDate = moment(existingJobCardInfo.JobDate).format('YYYY-MM-DD')
                    instance.chassisNo = {
                        chassisno: existingJobCardInfo.ChassisNo,
                    }
                    instance.serviceCenterCode = existingJobCardInfo.ServiceCenterCode
                    instance.engineNo = existingJobCardInfo.EngineNo
                    instance.customerName = existingJobCardInfo.CustomerName
                    instance.mobileNo = existingJobCardInfo.MobileNo
                    instance.brand = existingJobCardInfo.Brand
                    instance.model = existingJobCardInfo.Model
                    instance.purchaseDate = moment(existingJobCardInfo.PurchaseDate).format('YYYY-MM-DD')
                    instance.underWarrenty = existingJobCardInfo.UnderWarrenty

                    instance.registrationNo = existingJobCardInfo.RegistrationNo
                    // instance.bookingNo=existingJobCardInfo.
                    instance.serial = existingJobCardInfo.SerialNo
                    instance.mileage = existingJobCardInfo.Mileage
                    instance.failureAnalysis = existingJobCardInfo.MotorcycleOuterCondition

                    instance.technicianCode = {
                        Details: existingJobCardInfo.TechnicianCode + '-' + existingJobCardInfo.TechnicianName,
                        TechnicianCode: existingJobCardInfo.TechnicianCode
                    }
                    instance.bayCode = {
                        Details: existingJobCardInfo.BayCode + '-' + existingJobCardInfo.BayName,
                        BayCode: existingJobCardInfo.BayCode
                    }
                    if (existingJobCardInfo.JobStatus === 'Ongoing') {
                        instance.allJobStatus = []
                        instance.allJobStatus = [
                            {
                                StatusName: 'Ongoing',
                                StatusCode: 'Ongoing'
                            },
                            {
                                StatusName: 'Pause',
                                StatusCode: 'Pause'
                            },
                            {
                                StatusName: 'Close',
                                StatusCode: 'Close'
                            }
                        ]
                    }
                    if (existingJobCardInfo.JobStatus === 'Waiting') {
                        instance.allJobStatus = []
                        instance.allJobStatus = [
                            {
                                StatusName: 'Ongoing',
                                StatusCode: 'Ongoing'
                            },
                            {
                                StatusName: 'Waiting',
                                StatusCode: 'Waiting'
                            }
                        ]
                    }
                    if (existingJobCardInfo.JobStatus === 'Pause') {
                        instance.allJobStatus = []
                        instance.allJobStatus = [
                            {
                                StatusName: 'Ongoing',
                                StatusCode: 'Ongoing'
                            },
                            {
                                StatusName: 'Close',
                                StatusCode: 'Close'
                            }
                        ]
                    }

                    instance.jobStatus = {
                        StatusName: existingJobCardInfo.JobStatus,
                        StatusCode: existingJobCardInfo.JobStatus
                    }
                    // setTimeout(() => {
                    //     instance.jobType = {
                    //         JobTypeName: existingJobCardInfo.JobTypeName,
                    //         Id: existingJobCardInfo.JobTypeId
                    //     }
                    // }, 500)

                    instance.jobType = {
                        JobTypeName: existingJobCardInfo.JobTypeName,
                        Id: existingJobCardInfo.JobTypeId
                    }


                    let updateJobCardServiceName = ''
                    let updateJobCardServiceId = ''
                    if (existingJobCardInfo.ScheduleTitle != null && parseInt(existingJobCardInfo.FreeSScheduleID) !== 0) {
                        updateJobCardServiceName = existingJobCardInfo.ScheduleTitle
                        updateJobCardServiceId = existingJobCardInfo.FreeSScheduleID
                        //instance.checkLastServiceHistory(existingJobCardInfo.ChassisNo);
                        instance.childJobTypeStatusReadOnly = false
                    } else {
                        updateJobCardServiceName = existingJobCardInfo.JobTypeName
                        updateJobCardServiceId = existingJobCardInfo.JobTypeId
                        instance.childJobTypeStatusReadOnly = true
                    }
                    // setTimeout(() => {
                    //     instance.ServiceNo = {
                    //         JobTypeName: updateJobCardServiceName,
                    //         Id: updateJobCardServiceId
                    //     }
                    // }, 500)


                    instance.ServiceNo = {
                        JobTypeName: updateJobCardServiceName,
                        Id: updateJobCardServiceId
                    }

                    instance.ytdStatus = existingJobCardInfo.YTD_status
                    if (existingJobCardInfo.YTD_status === 'N') {
                        instance.ytdNoTag = true
                        instance.reasonOfYDT = {
                            Ytd_Stauts_Reason: existingJobCardInfo.Ytd_Stauts_Reason,
                            Id: existingJobCardInfo.YTD_status_no_reason
                        }
                    }
                    if (existingJobCardInfo.FI_Status === 'N') {
                        instance.fiNoTag = true
                        instance.reasonOfFI = {
                            Ytd_Stauts_Reason: existingJobCardInfo.fi_no_reason,
                            Id: existingJobCardInfo.FI_status_no_reason
                        }
                    }
                    // instance.ydTFile=existingJobCardInfo.
                    instance.fiStatus = existingJobCardInfo.FI_Status
                    instance.address = existingJobCardInfo.Address
                    // instance.failureAnalysis=existingJobCardInfo.
                    let problemEditArray = []
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
                    instance.timeReqMin = existingJobCardInfo.TimeRequired
                    instance.timeTaken = existingJobCardInfo.TimeTaken
                    instance.startTime = (existingJobCardInfo.JobStartTimeparseInt) > 2000 ? existingJobCardInfo.JobStartTimeparseInt : 0
                    instance.endTime = parseInt(existingJobCardInfo.JobEndTime) > 2000 ? existingJobCardInfo.JobEndTime : 0
                    instance.discountType = existingJobCardInfo.DiscountType
                    instance.discount = existingJobCardInfo.DiscountPercent
                    instance.staffId = existingJobCardInfo.ACIEmployeeId
                    if (existingJobCardInfo.LocalMechanicsCode) {
                        instance.reference = {
                            MechanicsDetails: existingJobCardInfo.LocalMechanicsCode + '-' + existingJobCardInfo.LocalMechanicsName,
                            MechanicsCode: existingJobCardInfo.LocalMechanicsCode
                        }
                    }

                    //Spare Parts
                    let spare_parts = response.existingJobCardPartsInfo;
                    if (spare_parts.length > 0) {
                        instance.partsFields.splice(0, 1)
                        spare_parts.forEach((item) => {
                            if (item.ItemCode) {
                                instance.partsFields.push({
                                    partsCode: {
                                        FullProduct: item.ItemCode + '-' + item.ProductName,
                                        ProductCode: item.ItemCode
                                    },
                                    currentStock: item.CurrentStock ? item.CurrentStock : 0,
                                    quantity: item.Quantity ? item.Quantity : 0,
                                    unitPrice: item.UnitPrice ? item.UnitPrice : 0,
                                    totalPrice: item.Quantity * item.UnitPrice,
                                    serviceCharge: item.ServiceCharge,
                                    discount: item.Discount,
                                })
                            }

                        })
                    }
                    instance.calculateAllPartsInfo()
                    let editService = response.existingJobCardServiceInfo;
                    if (editService.length > 0) {
                        instance.serviceFields.splice(0, 1)
                        editService.forEach((serviceItem) => {
                            if (serviceItem.ItemCode) {
                                instance.serviceFields.push({
                                    workCode: {
                                        FullProduct: serviceItem.ItemCode + '-' + serviceItem.WorkName,
                                        WorkCode: serviceItem.ItemCode
                                    },
                                    unitPrice: serviceItem.UnitPrice,
                                    serviceDiscount: serviceItem.Discount
                                })
                            }

                        })
                    }
                    instance.calculateAllServiceInfo()
                }
            }, function (error) {

            });
        } else {
            this.title = 'Add Job Card' +
                '';
            this.buttonText = "Submit";
            this.transferNo = '';

            this.status = '';
            this.buttonShow = true;
            this.actionType = 'add'
        }

    },
    destroyed() {
        bus.$off('add-edit-jobCard')
    },
    methods: {
        encodeConvert(val) {
            let convertVal = btoa(val);
            return convertVal
        },
        decodeConvert(val) {
            let convertVal = atob(val);
            return convertVal
        },
        getSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/supporting-data', function (response) {
                if (instance.actionType === 'add') {
                    instance.jobCardNo = response.jobCardNo;
                }
                instance.allBay = response.allBay
                instance.allTechnician = response.allTechnician
                instance.allReference = response.localMechanics;
                instance.parentJobType = response.parentJobType;
                instance.allJobType = response.allJobType;
                instance.allProblem = response.allProblems;
                //instance.allJobStatus = response.tblJobStatus;

                if (instance.actionType === 'add') {
                    instance.allJobStatus = [
                        {
                            StatusName: 'Ongoing',
                            StatusCode: 'Ongoing'
                        },
                        {
                            StatusName: 'Waiting',
                            StatusCode: 'Waiting'
                        }

                    ]
                }
                instance.ytdNoReason = response.ytdNoReason;
                instance.fiNoReason = response.fiNoReason;
            }, function (error) {
            });
        },
        addPartsFieldRow() {
            this.newProduct = 'add';
            this.partsFields.push({
                partsCode: '',
                currentStock: 0,
                quantity: 0,
                unitPrice: 0,
                totalPrice: 0,
                serviceCharge: 0,
                discount: 0,
                grossTotal: 0,
            });
        },
        addServiceFieldRow() {
            this.newProduct = 'add';
            this.serviceFields.push({
                workCode: '',
                unitPrice: 0,
                serviceDiscount: 0,
                grossTotal: 0,
                works: [],
            });
        },
        removeFieldsRow(id) {
            this.partsFields.splice(id, 1)
            if (this.errors[id] !== undefined) {
                this.errors.splice(id, 1)
            }
            this.calculateAllPartsInfo();
        },
        removeServiceRow(id) {
            this.serviceFields.splice(id, 1)
            if (this.errors[id] !== undefined) {
                this.errors.splice(id, 1)
            }
            this.calculateAllServiceInfo();
        },
        closeModal() {
            $("#add-edit-dept").modal("toggle");
            $(`[data-index="${2}"]`).focus()
        },
        setBay(e) {
            this.bayCode = {
                Details: e.DefaultBay + '-' + e.BayName,
                BayCode: e.DefaultBay
            }
        },
        setYdtNoReason(e) {
            if (e.target.value === 'N') {
                this.ytdNoTag = true
            } else {
                this.ytdNoTag = false
            }
        },
        setFiNoReason(e) {
            if (e.target.value === 'N') {
                this.fiNoTag = true
            } else {
                this.fiNoTag = false
            }
        },
        checkOnlineBooking(e) {
            let onlineBookingNo = this.bookingNo
            let instance = this

            if (instance.bookingNo === '') {
                instance.errorNoti('Please Enter Booking No')
                return false
            } else {
                this.axiosGet('jobCard/check-online-booking-no/' + onlineBookingNo, function (response) {
                    if (response.onlineBooking !== null) {
                        instance.checkChassisNo(response.onlineBooking.ChassisNo)
                    } else {
                        instance.errorNoti('Online Booking Not Found');
                        instance.chassisNo = ''
                        instance.engineNo ='';
                        instance.customerName = '';
                        instance.brand = '';
                        instance.model = '';
                        instance.purchaseDate = '';
                        instance.underWarrenty = '';
                        instance.address = '';

                    }

                }, function (error) {
                });
            }


        },
        checkChassisNo(val,bi='') {
            let instance = this;
            instance.chassisNo = val;
            this.allchassisNo = []
            if (val.length > 0) {
                this.axiosGet('jobCard/check-chassis-no/' + instance.chassisNo, function (response) {
                    let onGoingStatusCheck = (response.onGoingStatusCheck === null) ? '' : response.onGoingStatusCheck.JobStatus
                    console.log(onGoingStatusCheck, 'onGoingStatusCheck')

                    if (onGoingStatusCheck === 'Ongoing' || onGoingStatusCheck === 'Waiting' || onGoingStatusCheck === 'Pause') {
                        let dealerName = response.onGoingStatusCheck.CustomerName
                        instance.errorNoti('Already Job Card Running At ' + dealerName);
                        instance.errors.push('Ongoing Status')
                    } else {
                        if (response) {
                            instance.allchassisNo = response.bikeList;
                            if (bi !== 'bi') {
                                instance.chassisNo = {
                                    chassisno:  instance.chassisNo
                                }
                              instance.setBikeInfo(response.bikeList[0])
                            }
                        } else {
                            instance.allchassisNo = []
                        }
                    }

                }, function (error) {
                });
            }
        },
        setServiceNo(val) {
            let childJobTypes = this.allJobType.filter(item => {
                return item.ParentId === val.Id;

            })
            if (val.Id === '2') {
                console.log(this.freeServices)
                this.ServiceNo = ''
                this.childJobType = this.freeServices;
                this.childJobTypeStatusReadOnly = false
                if (this.freeServices.length <= 0) {
                    this.errorNoti('Free Service Not Available');
                }

            } else {
                this.childJobType = []
                this.ServiceNo = {
                    JobTypeName: val.JobTypeName,
                    Id: val.Id
                }
                this.childJobTypeStatusReadOnly = true
            }

        },
        checkSpareParts(e, index) {
            let instance = this;
            if (e.length > 2) {
                this.axiosGet('jobCard/check-spare-parts/' + e, function (response) {
                    if (response.data) {
                        instance.parts = response.data;
                    } else {
                        instance.parts = []
                    }

                }, function (error) {
                });
            }

        },
        checkServices(e, index) {
            let instance = this;
            if (e.length > 1) {
                this.axiosGet('jobCard/check-services/' + e, function (response) {
                    if (response.data) {
                        instance.works = response.data;
                    } else {
                        instance.works = []
                    }

                }, function (error) {
                });
            }
        },
        setBikeEvent() {

        },
        setBikeInfo(val) {
            let instance = this;
            let bikeInfo = val;
            if (bikeInfo) {
                instance.engineNo = bikeInfo.engineno;
                instance.customerName = bikeInfo.customername;
                instance.brand = bikeInfo.brandname;
                instance.model = bikeInfo.productname;

                var dateObj = new Date(bikeInfo.invoicedate);
                var momentObj = moment(dateObj);
                var momentString = momentObj.format('YYYY-MM-DD');
                instance.purchaseDate = momentString;
                instance.underWarrenty = bikeInfo.underwarrenty;
                instance.address = bikeInfo.Address;

                //Check las Service History
                this.checkLastServiceHistory(bikeInfo.chassisno)
               //  if (instance.actionType === 'add') {
               //      // $("#add-edit-dept").modal("toggle");
               //      $(`[data-index="${2}"]`).focus()
               //  }

            }
        },
        checkLastServiceHistory(chassisNo) {
            console.log(chassisNo)

            let instance = this;
            instance.freeServices = [];
            this.childJobType = []
            this.showElements = false;
            setTimeout(() => {
                this.showElements = true;
            }, 2000);
            this.axiosGet('jobCard/check-last-service-history/' + chassisNo, function (response) {
                let serviceHistoryInfo = response.data
                if (response.data.lastService.length > 0) {
                    instance.serviceHistoryDealerName = serviceHistoryInfo.lastService[0].CustomerName,
                        instance.serviceHistoryLastServiceType = serviceHistoryInfo.lastService[0].JobTypeName,
                        instance.serviceHistoryLastScheduleTitle = serviceHistoryInfo.lastService[0].ScheduleTitle,
                        instance.serviceHistoryLastServiceDate = serviceHistoryInfo.lastService[0].JobDate,
                        instance.serviceHistoryLastFeedbackRating = serviceHistoryInfo.lastService[0].FeedBack,
                        instance.serviceHistoryLastMillage = serviceHistoryInfo.maxMileage[0].max
                    instance.jobType = ''
                    instance.ServiceNo = ''
                }
                instance.freeServices = serviceHistoryInfo.freeServices
                if (instance.actionType === 'add') {
                    $("#add-edit-dept").modal("toggle");
                    $(`[data-index="${2}"]`).focus()
                }

            }, function (error) {
            });
        },
        checkMillage(e) {
            if (this.serviceHistoryLastMillage.length > 0) {
                let checkMillage = e.target.value
                if (parseInt(checkMillage) < parseInt(this.serviceHistoryLastMillage)) {
                    this.errorNoti('Current Millage Lower Than Last Millage')
                    this.mileage = 0
                    $(`[data-index="${6}"]`).focus()
                    this.errors.push('Millage Error')
                } else {
                    this.errors = []
                    $(`[data-index="${7}"]`).focus()
                }
            }

        },
        setPartsInfo(val, index) {
            let instance = this;
            let partsInfo = val;
            instance.partsFields[index].unitPrice = parseFloat(partsInfo.MRP).toFixed(2);
            if (!partsInfo.CurrentStock) {
                instance.partsFields[index].currentStock = 0
            } else {
                instance.partsFields[index].currentStock = partsInfo.CurrentStock;
            }

            instance.partsFields[index].totalPrice = parseFloat(instance.partsFields[index].quantity).toFixed(2) * parseFloat(partsInfo.UnitPrice).toFixed(2);
        },
        setTotalPartsPrice(index) {
            this.checkPartsFieldValue();
            this.partsFields[index].totalPrice = this.partsFields[index].quantity ? this.partsFields[index].quantity * parseFloat(this.partsFields[index].unitPrice) : 0;
            this.partsFields[index].totalPrice = parseFloat(this.partsFields[index].totalPrice).toFixed(2)
            this.calculateAllPartsInfo();

        },
        setServiceInfo(val, index) {
            let serviceInfo = val;
            this.serviceFields[index].unitPrice = serviceInfo.WorkRate;
            this.calculateAllServiceInfo();

        },
        calculateAllPartsInfo() {
            let tempPartsFieldTotalPrice = 0;
            let tempPartsFieldTotalServiceCharge = 0;
            let tempDiscount = 0;
            this.partsFieldTotalPrice = 0;
            this.partsFieldTotalServiceCharge = 0;
            this.partsFieldGrandTotal = 0;
            this.partsFieldTotalDiscount = 0;

            this.partsFields.forEach(function (item, index) {
                tempPartsFieldTotalPrice += item.totalPrice ? parseFloat(item.totalPrice) : 0;
                tempPartsFieldTotalServiceCharge += item.serviceCharge ? parseFloat(item.serviceCharge) : 0;
                tempDiscount += item.discount ? item.discount <= 100 ? (parseFloat(item.totalPrice) * parseFloat(item.discount)) / 100 : 0 : 0;
            });

            this.partsFieldTotalPrice = parseFloat(this.partsFieldTotalPrice + tempPartsFieldTotalPrice).toFixed(2);
            this.partsFieldTotalServiceCharge = parseFloat(this.partsFieldTotalServiceCharge + tempPartsFieldTotalServiceCharge).toFixed(2);
            this.partsFieldTotalDiscount = parseFloat(this.partsFieldTotalDiscount + tempDiscount).toFixed(2)
            this.partsFieldGrandTotal = parseFloat((parseFloat(this.partsFieldTotalPrice) + parseFloat(this.partsFieldTotalServiceCharge)) - parseFloat(this.partsFieldTotalDiscount)).toFixed(2)
        },
        calculateAllServiceInfo() {
            this.checkServiceFieldValue();
            let tempServiceFieldTotalDiscount = 0;
            let tempServiceFieldGrandTotal = 0;
            this.serviceFieldTotalDiscount = 0;
            this.serviceFieldGrandTotal = 0;

            this.serviceFields.forEach(function (item, index) {
                tempServiceFieldGrandTotal += item.unitPrice ? parseFloat(item.unitPrice) : 0;
                tempServiceFieldTotalDiscount += item.serviceDiscount ? item.serviceDiscount <= 100 ? (parseFloat(item.unitPrice) * parseFloat(item.serviceDiscount)) / 100 : 0 : 0;
            });
            this.serviceFieldTotalDiscount = parseFloat(tempServiceFieldTotalDiscount).toFixed(2);
            this.serviceFieldGrandTotal = parseFloat(parseFloat(tempServiceFieldGrandTotal) - parseFloat(this.serviceFieldTotalDiscount)).toFixed(2);
            this.checkDuplicateService()

        },
        fileUpload(e) {
            var input = e.target
            var file = input.files[0]
            if (file.type !== 'application/pdf') {
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
                instance.ydTFile = reader.result
                instance.AttachmentFlag = 1
            };
            reader.onerror = function (error) {
            };
        },
        checkPartsFieldValue() {
            let instance = this;
            //Parts Field
            this.errors = []
            this.partsFields.forEach(function (item, index) {
                    if (item.partsCode.ProductCode) {
                        let qntError = ''
                        if (parseFloat(item.quantity) <= 0) {
                            qntError = 'Need Min Quantity'
                        } else if (parseFloat(item.quantity) > parseFloat(item.currentStock)) {
                            qntError = 'Not Enough Stock'
                        } else {
                            qntError = ''
                        }
                        let disError = parseFloat(item.discount) > 100 ? 'Discount max 100%' : ''
                        if (qntError.length > 0 || disError.length > 0) {
                            instance.errors[index] = {
                                quantity: qntError,
                                discount: disError
                            };
                        }
                    }
                }
            );
            this.checkDuplicateParts();
        },
        checkServiceFieldValue() {
            let instance = this;
            //Service Field
            this.serviceFields.forEach(function (item, index) {
                if (item.workCode.ServiceCenterCode) {
                    let disSerError = parseFloat(item.serviceDiscount) > 100 ? 'Discount max 100%' : ''
                    if (disSerError.length > 0) {
                        instance.errors[index] = {
                            serviceDiscount: disSerError
                        };
                    }
                }
            });
        },
        checkDuplicateParts() {
            let errorCount = 0
            this.partsFields.forEach((item, index) => {
                var filterProduct = this.partsFields.filter((row) => {
                    return row.partsCode.ProductCode === item.partsCode.ProductCode
                })
                if (filterProduct.length > 1) {
                    errorCount += 1
                } else {
                    errorCount = 0
                }
            });
            if (errorCount > 0) {
                this.duplicateErrors.push(errorCount)
                this.errorNoti('Duplicate Parts');
            } else {
                this.duplicateErrors = []
            }
        },
        checkDuplicateService() {
            let errorCount = 0
            this.serviceFields.forEach((item, index) => {
                var filterProductService = this.serviceFields.filter((row) => {
                    return row.workCode.WorkCode === item.workCode.WorkCode
                })
                if (filterProductService.length > 1) {
                    errorCount += 1
                } else {
                    errorCount = 0
                }
            });
            if (errorCount > 0) {
                this.duplicateErrors.push(errorCount)
                this.errorNoti('Duplicate Services');
            } else {
                this.duplicateErrors = []
            }
        },
        onSubmit() {
            this.PreLoader = true;
            this.$store.commit('submitButtonLoadingStatus', true);
            this.buttonShow = false;

            this.checkPartsFieldValue()
            this.checkServiceFieldValue()

            // console.log('finalError', this.errors.length)
            // console.log('finalError2', this.duplicateErrors.length)


            if (this.duplicateErrors.length === 0 && this.errors.length === 0) {
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'jobCard/job-add';
                }
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'jobCard/job-update';
                }
                this.axiosPost(submitUrl, {
                    jobCardNo: this.jobCardNo,
                    jobDate: this.jobDate,
                    chassisNo: this.chassisNo,
                    engineNo: this.engineNo,
                    customerName: this.customerName,
                    mobileNo: this.mobileNo,
                    brand: this.brand,
                    model: this.model,
                    purchaseDate: this.purchaseDate,
                    underWarrenty: this.underWarrenty,
                    registrationNo: this.registrationNo,
                    bookingNo: this.bookingNo,
                    serial: this.serial,
                    mileage: this.mileage,
                    technicianCode: this.technicianCode,
                    bayCode: this.bayCode,
                    jobStatus: this.jobStatus,
                    jobType: this.jobType,
                    ServiceNo: this.ServiceNo,
                    ytdStatus: this.ytdStatus,
                    reasonOfYDT: this.reasonOfYDT,
                    reasonOfFI: this.reasonOfFI,
                    ydTFile: this.ydTFile,
                    fiStatus: this.fiStatus,
                    address: this.address,
                    failureAnalysis: this.failureAnalysis,
                    problemId: this.problemId,
                    otherProblem: this.otherProblem,
                    reasonAndProblemDetails: this.reasonAndProblemDetails,
                    timeReqMin: this.timeReqMin,
                    timeTaken: this.timeTaken,
                    startTime: this.startTime,
                    endTime: this.endTime,
                    discountType: this.discountType,
                    discount: this.discount,
                    staffId: this.staffId,
                    partsFields: this.partsFields,
                    serviceFields: this.serviceFields,
                    reference: this.reference
                }, (response) => {
                    this.PreLoader = false;
                    this.successNoti(response.message);
                    this.buttonShow = true;
                    bus.$emit('refresh-datatable');
                    this.$store.commit('submitButtonLoadingStatus', false);
                    if (this.actionType === 'edit') {
                        this.$router.push({path: 'job-card-print?action_type=print&jobCardNo=' + this.encodeConvert(this.jobCardNo)})
                    }
                    if (response.jobStatus === 'Close') {
                        this.$router.push({name: 'JobCardIndex'})
                    } else {
                        location.reload();
                    }
                }, (error) => {
                    this.PreLoader = false;
                    this.errorNoti(error);
                    this.$store.commit('submitButtonLoadingStatus', false);
                })
            } else {
                this.PreLoader = false;
                location.reload();
                this.$store.commit('submitButtonLoadingStatus', false);
                this.errorNoti('Check Stock And Mandatory Field');
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
<style scoped>
#serviceChargeJobCard .table .thead-dark th {
    background: #204d74 !important;
    /*border: #626ed4 !important;*/
}

</style>
