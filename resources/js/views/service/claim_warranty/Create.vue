<template>
  <div class="container-fluid">
    <breadcrumb :options="['Claim Warranty']">
      <router-link :to="{name: 'ClaimWarrantyList'}" class="btn btn-primary btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row m-b-15">
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="JobCardNo" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <input type="text" class="form-control" name="JobCardNo" v-model="form.JobCardNo" placeholder="Enter Job Card">
                              <div class="error" v-if="form.errors.has('JobCardNo')" v-html="form.errors.get('JobCardNo')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <button type="button" class="btn btn-primary float-left btn-sm" @click="jobCardWiseInfo">Find</button>
                        </div>
                        <div class="col-12 col-md-2 float-right">
                          <div class="form-group">
                            <input type="text" class="form-control" name="ChassisNo" readonly v-model="form.ChassisNo" placeholder="Chassis">
                            <div class="error" v-if="form.errors.has('ChassisNo')" v-html="form.errors.get('ChassisNo')" />
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="row m-b-15" v-if="jobCardInfoEnable">
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Invoice No</label>
                            <div class="col-lg-8">
                              <input name="InvoiceNo" type="text" class="form-control" readonly v-model="form.InvoiceNo" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Customer Name</label>
                            <div class="col-lg-8">
                              <input name="CustomerName" type="text" class="form-control" readonly v-model="form.CustomerName" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Product Name</label>
                            <div class="col-lg-8">
                              <input name="ProductName" type="text" class="form-control" readonly v-model="form.ProductName" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Chassis No</label>
                            <div class="col-lg-8">
                              <input name="ChassisNo" type="text" class="form-control" readonly v-model="form.ChassisNo" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">EngineNo</label>
                            <div class="col-lg-8">
                              <input name="EngineNo" type="text" class="form-control" readonly v-model="form.EngineNo" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Color</label>
                            <div class="col-lg-8">
                              <input name="Color" type="text" class="form-control" readonly v-model="form.Color" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Status</label>
                            <div class="col-lg-8">
                              <input name="Status" type="text" class="form-control" readonly v-model="form.Status" />
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group row" style="padding-bottom: 10px">
                            <label class="col-lg-4 col-form-label text-right">Days</label>
                            <div class="col-lg-8">
                              <input name="Days" type="text" class="form-control" readonly v-model="form.Days" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <br>
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 220px">Customer Information</p>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="OccuranceDate" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Occurrence Date.</label>
                              <date-picker v-model="form.OccuranceDate" valueType="format"></date-picker>
                              <div class="error" v-if="form.errors.has('OccuranceDate')" v-html="form.errors.get('OccuranceDate')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="SourceOfInformation" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Source of Information<span style="color: red">*</span></label>
                              <select name="SourceOfInformation" class="form-control" v-model="form.SourceOfInformation" style="margin: 0">
                                <option value="Source of Information">Select Source of Information</option>
                                <option :value="WarrSource.WarrantySourceId" v-for="(WarrSource , index) in WarrantySource" :key="index">{{ WarrSource.WarrantySourceName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('SourceOfInformation')" v-html="form.errors.get('SourceOfInformation')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Mileage" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Mileage<span style="color: red">*</span></label>
                              <input type="text" class="form-control" name="Mileage" readonly v-model="form.Mileage">
                              <div class="error" v-if="form.errors.has('Mileage')" v-html="form.errors.get('Mileage')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="ServiceSchedule" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Free Service Schedule <span style="color: red">*</span></label>
                              <select name="ServiceSchedule" class="form-control" v-model="form.ServiceSchedule" style="margin: 0">
                                <option :value="Schedule.ScheduleTitle" v-for="(Schedule , index) in ServiceSchedule" :key="index">{{ Schedule.ScheduleTitle }}</option>
                                <option value="Not Applicable">Not Applicable</option>
                              </select>
                              <div class="error" v-if="form.errors.has('ServiceSchedule')" v-html="form.errors.get('ServiceSchedule')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="TypeOfWarranty" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Type of Warranty<span style="color: red">*</span></label>
                              <select name="TypeOfWarranty" class="form-control" v-model="form.TypeOfWarranty" style="margin: 0">
                                <option :value="Type.WarrantyTypeId" v-for="(Type , index) in WarrantyType" :key="index">{{ Type.WarrantyTypeName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('TypeOfWarranty')" v-html="form.errors.get('TypeOfWarranty')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Seriousness" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Seriousness  <span style="color: red">*</span></label>
                              <select name="Seriousness" class="form-control" v-model="form.Seriousness" style="margin: 0">
                                <option :value="Seriousness.WarrantySeriousnessId" v-for="(Seriousness , index) in WarrantySeriousness" :key="index">{{ Seriousness.WarrantySeriousnessName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('Seriousness')" v-html="form.errors.get('Seriousness')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="TechnicianName" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Technician Name<span style="color: red">*</span></label>
                              <input type="text" class="form-control" name="TechnicianName" v-model="form.TechnicianName">
                              <div class="error" v-if="form.errors.has('TechnicianName')" v-html="form.errors.get('TechnicianName')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-6">
                          <ValidationProvider name="ProblemName" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Problem Name<span style="color: red">*</span></label>
                              <input type="text" class="form-control" name="ProblemName" v-model="form.ProblemName">
                              <div class="error" v-if="form.errors.has('ProblemName')" v-html="form.errors.get('ProblemName')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                      </div>
                      <br>
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 220px">Rider Information</p>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Sex" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Sex</label>
                              <select name="Sex" class="form-control" v-model="form.Sex" style="margin: 0">
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="O">Other</option>
                              </select>
                              <div class="error" v-if="form.errors.has('Sex')" v-html="form.errors.get('Sex')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Weight" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Weight</label>
                              <input type="text" class="form-control" name="Weight" v-model="form.Weight">
                              <div class="error" v-if="form.errors.has('Weight')" v-html="form.errors.get('Weight')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="RoadCondition" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Road Condition</label>
                              <select name="RoadCondition" class="form-control" v-model="form.RoadCondition" style="margin: 0">
                                <option value="Paved">Paved</option>
                                <option value="Unpaved">Unpaved</option>
                              </select>
                              <div class="error" v-if="form.errors.has('RoadCondition')" v-html="form.errors.get('RoadCondition')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Age" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Age</label>
                              <input type="text" class="form-control" name="Age" v-model="form.Age">
                              <div class="error" v-if="form.errors.has('Age')" v-html="form.errors.get('Age')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="RidingStyle" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Riding Style</label>
                              <select name="RidingStyle" class="form-control" v-model="form.RidingStyle" style="margin: 0">
                                <option value="Normal">Normal</option>
                                <option value="Aggressive">Aggressive</option>
                                <option value="Moderate">Moderate</option>
                              </select>
                              <div class="error" v-if="form.errors.has('RidingStyle')" v-html="form.errors.get('RidingStyle')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="RiderProfession" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Rider Profession</label>
                              <select name="RiderProfession" class="form-control" v-model="form.RiderProfession" style="margin: 0">
                                <option :value="occupa.OccupationId" v-for="(occupa , index) in Occupation" :key="index">{{ occupa.OccupationName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('RiderProfession')" v-html="form.errors.get('RiderProfession')" />
                            </div>
                          </ValidationProvider>
                        </div>
                      </div>
                      <br>
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 220px">Problem Details</p>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="ProblemIs" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>This Problem is</label>
                              <select name="ProblemIs" class="form-control" v-model="form.ProblemIs" style="margin: 0">
                                <option :value="ProblemIs.WarrantyProblemIsId" v-for="(ProblemIs , index) in WarrantyProblemIs" :key="index">{{ ProblemIs.WarrantyProblemName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('ProblemIs')" v-html="form.errors.get('ProblemIs')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="Remedy" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Remedy</label>
                              <select name="Remedy" class="form-control" v-model="form.Remedy" style="margin: 0">
                                <option :value="Remedy.WarrantyRemedyId" v-for="(Remedy , index) in WarrantyRemedy" :key="index">{{ Remedy.WarrantyRemedyName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('Remedy')" v-html="form.errors.get('Remedy')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="Result" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Result</label>
                              <select name="Result" class="form-control" v-model="form.Result" style="margin: 0">
                                <option :value="ProblemResult.WarrantyProblemResultId" v-for="(ProblemResult , index) in WarrantyProblemResult" :key="index">{{ ProblemResult.WarrantyProblemResultName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('Result')" v-html="form.errors.get('Result')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="CustomerComments" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Customer Comments</label>
                              <textarea class="form-control" name="CustomerComments" style="height: 50px" v-model="form.CustomerComments"></textarea>
                              <div class="error" v-if="form.errors.has('CustomerComments')" v-html="form.errors.get('CustomerComments')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="FailureAnalysis" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Diagnosis & Failure Analysis</label>
                              <textarea class="form-control" name="FailureAnalysis" style="height: 50px" v-model="form.FailureAnalysis"></textarea>
                              <div class="error" v-if="form.errors.has('FailureAnalysis')" v-html="form.errors.get('FailureAnalysis')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="RemedyResult" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Result</label>
                              <textarea class="form-control" name="RemedyResult" style="height: 50px" v-model="form.RemedyResult"></textarea>
                              <div class="error" v-if="form.errors.has('RemedyResult')" v-html="form.errors.get('RemedyResult')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="CauseOfFailure" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Suspected Cause of Failure</label>
                              <textarea class="form-control" name="CauseOfFailure" style="height: 50px" v-model="form.CauseOfFailure"></textarea>
                              <div class="error" v-if="form.errors.has('CauseOfFailure')" v-html="form.errors.get('CauseOfFailure')" />
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="AdditionalComments" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Additional Comments</label>
                              <textarea class="form-control" name="AdditionalComments" style="height: 50px" v-model="form.AdditionalComments"></textarea>
                              <div class="error" v-if="form.errors.has('CauseOfFailure')" v-html="form.errors.get('AdditionalComments')" />
                            </div>
                          </ValidationProvider>
                        </div>
                      </div>
                      <br>
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 300px">Photos of Failure Parts / Documents</p>
                        </div>
                        <div class="col-12 col-md-4">
                          <ValidationProvider name="Photo" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Photo(s)<span style="color: red">*</span></label>
                              <input @change="handleFiles($event)" type="file" name="Picture" class="form-control" multiple>
                              <div  class="selected-images">
                                <div v-for="(image, index) in form.Picture" :key="index" class="selected-image" style="display: flex">
                                  <img :src="image.url" height="40px" width="40px" alt="Selected Image">
                                  <div class="remove-button" style="margin: 6px">
                                    <button class="btn btn-danger btn-sm" @click="removeImage(index)"><i class="mdi mdi-delete"></i></button>
                                  </div>
                                </div>
                              </div>
<!--                              <img v-if="form.Photo" :src="showImage(form.Photo)" alt="" height="40px" width="40px">-->
                              <div class="error" v-if="form.errors.has('Picture')" v-html="form.errors.get('Picture')" />
                            </div>
                          </ValidationProvider>
                        </div>
                      </div>
                      <br>

                      <div class="card table-graphics" style="padding-top:0px">
                        <div class="card-body table-overlay" style="padding: 0px">
                          <div class="row" v-if="jobCardInfoEnable">
                            <div class="col-md-3">
                              <multiselect v-model="form.ProductCode" v-if="isProductSearchOpen"
                                           :options="AllParts"
                                           data-index="1"
                                           :multiple="false"
                                           @search-change="filterParts"
                                           @input="doCheckParts"
                                           :close-on-select="true"
                                           :clear-on-select="false"
                                           :preserve-search="true"
                                           placeholder="Product"
                                           label="productname" track-by="productname">
                              </multiselect>
                            </div>
                            <div class="col-md-9">
                              <button style="float: right;margin-bottom: 5px;" type="button" class="btn btn-success btn-sm small" @click="addPartsFieldRow">Add
                                Parts Row
                              </button>
                            </div>
                          </div>
                          <table class="table table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm">
                            <thead class="thead-dark">
                            <tr>
                              <th style="width:10%">Invoice Type</th>
                              <th style="width:20%">Part</th>
                              <th style="width:9%">Quantity</th>
                              <th style="width:9%">Unit Price</th>
                              <th style="width:9%">Total Price</th>
                              <th style="width:9%">Service Charge</th>
                              <th style="width:9%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(field,index) in form.fieldsData" :key="index" v-if="form.fieldsData.length">
                              <td>
                                <select name="InvoiceType" class="form-control" v-model="field.InvoiceType" style="margin: 0">
                                  <option :value="InvoiceType.WarrantyInvoiceId" v-for="(InvoiceType , index) in WarrantyInvoiceType" :key="index">{{ InvoiceType.WarrantyInvoiceName }}</option>
                                </select>
                                <div class="error" v-if="form.errors.has('InvoiceType')" v-html="form.errors.get('InvoiceType')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </td>
                              <td style="text-align: end;">
                                <input type="text" class="form-control" v-model="field.SpareParts" readonly>
                              </td>
                              <td style="text-align: end">
                                <input type="number" class="form-control" v-model="field.Quantity" @keyup="changeQuantity(index)">
                                <span class="error" v-if="errors[index] !== undefined && errors[index].Quantity !== undefined">{{errors[index].Quantity}}</span>
                              </td>
                              <td style="text-align: end">
                                <input type="number" class="form-control" readonly v-model="field.UnitPrice">
                              </td>
                              <td style="text-align: end">
                                <input type="number" class="form-control" readonly v-model="field.TotalPrice" >
                              </td>
                              <td style="text-align: end">
                                <input type="number" class="form-control" readonly v-model="field.serviceCharge" >
                              </td>
                              <td>
                                <button type="button" class="btn btn-danger btn-sm" @click="removeFieldsRow(index)">Remove</button>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="5" style="text-align:right;font-weight:bold">
                                Total Price: {{partsFieldTotalPrice}}
                              </td>
                              <td style="text-align:right;;font-weight:bold">
                                Service Charge: {{partsFieldTotalServiceCharge}}
                              </td>
                            </tr>
                            <tr>
                              <td colspan="5" style="text-align:right;font-weight:bold">
                                Grand Total: {{partsFieldGrandTotal}}
                              </td>
                              <td colspan="2" style="text-align:right;font-weight:bold"></td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <br>
                  <button type="submit" class="btn btn-primary float-right submit_on_enter">Submit</button>
                </form>
              </ValidationObserver>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../../base_url'
import {Common} from "../../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      Occupation: [],
      ServiceSchedule: [],
      WarrantyInvoiceType: [],
      WarrantyProblemIs: [],
      WarrantyProblemResult: [],
      WarrantyRemedy: [],
      WarrantySeriousness: [],
      WarrantySource: [],
      WarrantyType: [],
      AllParts: [],
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      form: new Form({
        JobCardNo : '',
        ChassisNo : '',
        EngineNo :'',
        InvoiceNo :'',
        CustomerName :'',
        ProductName :'',
        ProductCode :'',
        Color :'',
        Status :'',
        Days :'',
        OccuranceDate :'',
        SourceOfInformation : '',
        Mileage : 0,
        ServiceSchedule : '',
        TypeOfWarranty : '',
        Seriousness : '',
        TechnicianName : '',
        ProblemName : '',
        Sex : 'M',
        Weight : '',
        RoadCondition : '',
        Age : '',
        RidingStyle : '',
        RiderProfession : '',
        ProblemIs : '',
        Remedy : '',
        Result : '',
        CustomerComments : '',
        FailureAnalysis : '',
        RemedyResult : '',
        CauseOfFailure : '',
        AdditionalComments : '',
        Picture : '',
        fieldsData: [{InvoiceType : '', SpareParts:'', Quantity: 0, UnitPrice : 0, TotalPrice : 0,serviceCharge : '',ItemCode:''}],
      }),
      image : [],
      partsFieldTotalPrice: 0,
      partsFieldGrandTotal: 0,
      partsFieldTotalServiceCharge: 0,
      errors: [],
      isLoading: false,
      buttonShow: false,
      CreditFieldEnable: false,
      IsEmiEnable: false,
      IsExchangeEnable: false,
      IsExchangeMediumEnable: false,
      PreLoader: false,
      jobCardInfoEnable: false,
      isProductSearchOpen: false,
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Claim Warranty | DMS';
    this.getWarrentyFirstTime();
    document.addEventListener('click', this.handleClickOutside);
  },
  destroyed() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  methods: {
    onSubmit(){
      this.PreLoader = true;
      this.form.post(baseurl + "api/claim-warranty-store", this.config()).then(response => {
        if (response.data.status === 'success'){
          this.$toaster.success(response.data.message);
          this.$router.go(this.$router.currentRoute)
        }else {
          this.$toaster.error(response.data.message);
        }
        this.PreLoader = false;
      }).catch(e => {
        this.$toaster.error(e.data.message);
      });
    },
    jobCardWiseInfo(){
      this.PreLoader = true;
      axios.get(baseurl + "api/job-card-wise-info?JobCardNo=" + this.form.JobCardNo, this.config()).then(response => {
          if(response.data.status === 'success'){
              console.log(response.data.data)
              this.form.fill(response.data.data);
              this.calculateAllPartsInfo()
              this.jobCardInfoEnable = true;
              this.PreLoader = false;
          }
          else{
              console.log(response)
              this.$toaster.error(response.data.message);
              this.PreLoader = false;
          }

      }).catch(e => {
          this.PreLoader = false;
        this.$toaster.error(response.data.message);
      });
    },
    changeQuantity(index){
      this.form.fieldsData[index].TotalPrice = this.form.fieldsData[index].Quantity ? this.form.fieldsData[index].Quantity * parseFloat(this.form.fieldsData[index].UnitPrice) : 0;
      this.form.fieldsData[index].TotalPrice = parseFloat(this.form.fieldsData[index].TotalPrice).toFixed(2)
      this.calculateAllPartsInfo()
    },
    calculateAllPartsInfo() {
      let tempPartsFieldTotalPrice = 0;
      let tempPartsFieldTotalServiceCharge = 0;

      this.form.fieldsData.forEach(function (item, index) {
        tempPartsFieldTotalPrice += item.TotalPrice ? parseFloat(item.TotalPrice) : 0;
        tempPartsFieldTotalServiceCharge += item.serviceCharge ? parseFloat(item.serviceCharge) : 0;
      });

      this.partsFieldTotalPrice = parseFloat(tempPartsFieldTotalPrice);
      this.partsFieldTotalPrice = parseFloat(this.partsFieldTotalPrice).toFixed(2);

      this.partsFieldTotalServiceCharge = parseFloat(tempPartsFieldTotalServiceCharge);

      this.partsFieldGrandTotal = parseFloat(this.partsFieldTotalPrice) + parseFloat(this.partsFieldTotalServiceCharge);
      this.partsFieldGrandTotal = parseFloat(this.partsFieldGrandTotal).toFixed(2);
    },
    getWarrentyFirstTime(){
      axios.get(baseurl + 'api/get-warranty-first-time', this.config() ).then((response)=>{
        this.Occupation = response.data.warranty.Occupation;
        this.ServiceSchedule = response.data.warranty.ServiceSchedule;
        this.WarrantyInvoiceType = response.data.warranty.WarrantyInvoiceType;
        this.WarrantyProblemIs = response.data.warranty.WarrantyProblemIs;
        this.WarrantyProblemResult = response.data.warranty.WarrantyProblemResult;
        this.WarrantyRemedy = response.data.warranty.WarrantyRemedy;
        this.WarrantySeriousness = response.data.warranty.WarrantySeriousness;
        this.WarrantySource = response.data.warranty.WarrantySource;
        this.WarrantyType = response.data.warranty.WarrantyType;
      }).catch((error)=>{

      })
    },
    filterParts(val){
      if (val.length > 2){
        axios.get(baseurl + "api/filter-parts?query=" + val, this.config()).then(response => {
          console.log(this.AllParts)
          this.AllParts = response.data.parts
        }).catch(e => {
          this.$toaster.error(response.data.message);
        });
      }
    },
    doCheckParts(){
      this.form.fieldsData.push({
        InvoiceType: '',
        SpareParts: this.form.ProductCode.productname,
        Quantity: 1,
        UnitPrice: this.form.ProductCode.mrp,
        TotalPrice: this.form.ProductCode.mrp,
        serviceCharge: 0,
      });
      this.calculateAllPartsInfo()
      this.form.ProductCode = '';
      this.AllParts = [];
    },
    addPartsFieldRow() {
      this.isProductSearchOpen = true;
    },
    removeFieldsRow(id) {
      this.form.fieldsData.splice(id, 1)
      if (this.errors[id] !== undefined) {
        this.errors.splice(id, 1)
      }
      this.calculateAllPartsInfo()
    },
    handleFiles(event) {
      const files = event.target.files;
      this.form.Picture = [];
      Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const base64 = reader.result.split(',')[1];
          this.form.Picture.push({ url: e.target.result, file: base64 });
        };
        console.log(this.form.Picture)
      });
    },
    changeImage(event){
      this.image = [];
      const img = Array.from(event.target.files);
      for (let i = 0; i < img.length; i++) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.image.push({ url: e.target.result, file: img[i] });
        };
        reader.readAsDataURL(img[i]);
      }
      console.log(this.image)
      this.form.Picture = this.image;
    },
    showImage(){
      return this.form.Picture;
    },
    removeImage(index){
      this.form.Picture.splice(index, 1);
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
  font-size: 11px!important;
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
#invoice2 .auto-complete2 ul li{
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}
#invoice2 .auto-complete2 ul li a{
  color: #000000;
}
#invoice2 .auto-complete2 ul li:hover{
  background: #fff3cd;
}
#invoice2 :focus{
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
  color: #4d79f6b5  !important;
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

.autocomplete {
  position: relative;
}

.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  height: 120px;
  min-height: 1em;
  max-height: 6em;
  overflow: auto;
}

.autocomplete-result {
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
}

.autocomplete-result:hover {
  background-color: #4AAE9B;
  color: white;
}
</style>
