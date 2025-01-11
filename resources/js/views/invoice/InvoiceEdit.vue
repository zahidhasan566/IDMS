<template>
    <div class="container-fluid">
        <breadcrumb :options="['Invoice Create']">
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
                                            <div class="form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p style="width: 160px">Bike Details</p>
                                                </div>
                                              <div class="row">
                                                <div class="col-md-5">
                                                  <ValidationProvider name="ChassisNo" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">ChassisNo<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="CustomerName" type="text" class="form-control" readonly v-model="form.ChassisNo"/>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="EngineNo" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">EngineNo<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="EngineNo" type="text" class="form-control" readonly v-model="form.EngineNo"/>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="Color" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">Color<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="Color" type="text" class="form-control" readonly v-model="form.Color"/>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="FuelUsed" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">FuelUsed<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="FuelUsed" type="text" class="form-control" v-model="form.FuelUsed"/>
                                                        <div class="error" v-if="form.errors.has('FuelUsed')" v-html="form.errors.get('FuelUsed')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="HorsePower" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">HorsePower<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="HorsePower" type="text" class="form-control" v-model="form.HorsePower"/>
                                                        <div class="error" v-if="form.errors.has('HorsePower')" v-html="form.errors.get('HorsePower')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="RPM" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">RPM<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="RPM" type="text" class="form-control" v-model="form.RPM"/>
                                                        <div class="error" v-if="form.errors.has('RPM')" v-html="form.errors.get('RPM')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="CubicCapacity" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">CubicCapacity<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="CubicCapacity" type="text" class="form-control" v-model="form.CubicCapacity"/>
                                                        <div class="error" v-if="form.errors.has('CubicCapacity')" v-html="form.errors.get('CubicCapacity')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="WheelBase" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">WheelBase<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="WheelBase" type="text" class="form-control" v-model="form.WheelBase"/>
                                                        <div class="error" v-if="form.errors.has('CubicCapacity')" v-html="form.errors.get('WheelBase')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="Weight" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">Weight<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="Weight" type="text" class="form-control" v-model="form.Weight"/>
                                                        <div class="error" v-if="form.errors.has('Weight')" v-html="form.errors.get('Weight')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="WeightMax" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">WeightMax<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="WeightMax" type="text" class="form-control" v-model="form.WeightMax"/>
                                                        <div class="error" v-if="form.errors.has('WeightMax')" v-html="form.errors.get('WeightMax')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="Standee" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-4 col-form-label text-right">Standee<span style="color: red">*</span></label>
                                                      <div class="col-lg-8">
                                                        <input name="Standee" type="text" class="form-control" v-model="form.Standee"/>
                                                        <div class="error" v-if="form.errors.has('Standee')" v-html="form.errors.get('Standee')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                </div>

                                                <div class="col-md-7">
                                                  <ValidationProvider name="TireSizeFront" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">TireSizeFront<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="TireSizeFront" type="text" class="form-control" v-model="form.TireSizeFront"/>
                                                        <div class="error" v-if="form.errors.has('TireSizeFront')" v-html="form.errors.get('TireSizeFront')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="TireSizeRear" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">TireSizeRear<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="TireSizeRear" type="text" class="form-control" v-model="form.TireSizeRear"/>
                                                        <div class="error" v-if="form.errors.has('TireSizeFront')" v-html="form.errors.get('TireSizeRear')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="Seats" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">Seats<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="Seats" type="text" class="form-control" v-model="form.Seats"/>
                                                        <div class="error" v-if="form.errors.has('Seats')" v-html="form.errors.get('Seats')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="NoOfTyre" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">NoOfTyre<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="NoOfTyre" type="text" class="form-control" v-model="form.NoOfTyre"/>
                                                        <div class="error" v-if="form.errors.has('NoOfTyre')" v-html="form.errors.get('NoOfTyre')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="NoOfAxel" mode="eager" rules="" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">NoOfAxel</label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="NoOfAxel" type="text" class="form-control" v-model="form.NoOfAxel"/>
                                                        <div class="error" v-if="form.errors.has('NoOfAxel')" v-html="form.errors.get('NoOfAxel')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="ClassOfVehicle" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">ClassOfVehicle<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="ClassOfVehicle" type="text" class="form-control" v-model="form.ClassOfVehicle"/>
                                                        <div class="error" v-if="form.errors.has('ClassOfVehicle')" v-html="form.errors.get('ClassOfVehicle')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="MakerName" mode="eager" rules="" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">MakerName</label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="MakerName" type="text" class="form-control" v-model="form.MakerName"/>
                                                        <div class="error" v-if="form.errors.has('MakerName')" v-html="form.errors.get('MakerName')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="MakerCountry" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">MakerCountry<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="MakerCountry" type="text" class="form-control" v-model="form.MakerCountry"/>
                                                        <div class="error" v-if="form.errors.has('MakerCountry')" v-html="form.errors.get('MakerCountry')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="EngineType" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">EngineType<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="EngineType" type="text" class="form-control" v-model="form.EngineType"/>
                                                        <div class="error" v-if="form.errors.has('MakerCountry')" v-html="form.errors.get('EngineType')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="NumberOfCylinders" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">NumberOfCylinders<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="NumberOfCylinders" type="text" class="form-control" v-model="form.NumberOfCylinders"/>
                                                        <div class="error" v-if="form.errors.has('NumberOfCylinders')" v-html="form.errors.get('NumberOfCylinders')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                  <ValidationProvider name="ImportYear" mode="eager" rules="required" v-slot="{ errors }">
                                                    <div class="form-group row" style="padding-bottom: 10px">
                                                      <label class="col-lg-7 col-form-label text-right">ImportYear<span style="color: red">*</span></label>
                                                      <div class="col-lg-5 text-left">
                                                        <input name="ImportYear" type="text" class="form-control" v-model="form.ImportYear"/>
                                                        <div class="error" v-if="form.errors.has('ImportYear')" v-html="form.errors.get('ImportYear')"/>
                                                        <span class="error-message"> {{ errors[0] }}</span>
                                                      </div>
                                                    </div>
                                                  </ValidationProvider>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-divider m-b-15">
                                                <div class="form-divider-title">
                                                    <p style="width: 200px">Customer Details</p>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <ValidationProvider name="CustomerName" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Customer Name<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <input name="CustomerName" type="text" class="form-control" v-model="form.CustomerName"/>
                                                                    <div class="error" v-if="form.errors.has('CustomerName')" v-html="form.errors.get('CustomerName')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="Gender" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Gender<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <select name="Gender" class="form-control" v-model="form.Gender" style="margin: 0">
                                                                        <option value="Male">Male</option>
                                                                        <option value="Female">Female</option>
                                                                        <option value="Other">Other</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="FatherName" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Father/Husband Name<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <input name="FatherName" type="text" class="form-control" v-model="form.FatherName"/>
                                                                    <div class="error" v-if="form.errors.has('FatherName')" v-html="form.errors.get('FatherName')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="PreAddress" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Present Address<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <input name="PreAddress" type="text" class="form-control" v-model="form.PreAddress"/>
                                                                    <div class="error" v-if="form.errors.has('PreAddress')" v-html="form.errors.get('PreAddress')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <div class="form-group row" style="padding-bottom: 10px">
                                                            <label class="col-lg-4 col-form-label text-right">Permanent Address</label>
                                                            <div class="col-lg-8">
                                                                <input name="PerAddress" type="text" class="form-control" v-model="form.PerAddress"/>
                                                                <div class="error" v-if="form.errors.has('PerAddress')" v-html="form.errors.get('PerAddress')"/>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                        <ValidationProvider name="EMail" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Email<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <input name="EMail" type="text" class="form-control" v-model="form.EMail"/>
                                                                    <div class="error" v-if="form.errors.has('EMail')" v-html="form.errors.get('EMail')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="DateOfBirth" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-4 col-form-label text-right">Date of Birth (yyyy-mm-dd)<span style="color: red">*</span></label>
                                                                <div class="col-lg-8">
                                                                    <date-picker v-model="form.DateOfBirth" valueType="format"></date-picker>
                                                                    <div class="error" v-if="form.errors.has('DateOfBirth')" v-html="form.errors.get('DateOfBirth')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                    </div>

                                                    <div class="col-md-7">
                                                        <ValidationProvider name="OwnerType" mode="eager" rules="" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-7 col-form-label text-right">Owner Type</label>
                                                                <div class="col-lg-5 text-left">
                                                                  <select class="form-control" v-model="form.OwnerType">
                                                                    <option value="Private">Private</option>
                                                                    <option value="Organization">Organization</option>
                                                                  </select>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="MotherName" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-7 col-form-label text-right">Mother Name<span style="color: red">*</span></label>
                                                                <div class="col-lg-5 text-left">
                                                                    <input name="MotherName" type="text" class="form-control" v-model="form.MotherName"/>
                                                                    <div class="error" v-if="form.errors.has('MotherName')" v-html="form.errors.get('MotherName')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <ValidationProvider name="MobileNo" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-7 col-form-label text-right">MobileNo<span style="color: red">*</span></label>
                                                                <div class="col-lg-5" style="display: flex;align-items: center;overflow: hidden">
<!--                                                                    <span style=" background-color: #f7f7f7;font-size: 14px;padding: 0 8px;">+88</span>-->
                                                                    <input name="MobileNo" type="text" class="form-control" v-model="form.MobileNo"
                                                                            placeholder="Enter your mobile number"/>
                                                                </div>
                                                                <div class="error" v-if="form.errors.has('MobileNo')" v-html="form.errors.get('MobileNo')"/>
                                                                <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </ValidationProvider>
                                                        <div class="form-group row" style="padding-bottom: 10px">
                                                            <label class="col-lg-7 col-form-label text-right">Emergency Mobile</label>
                                                            <div class="col-lg-5" style="display: flex;align-items: center;overflow: hidden">
<!--                                                                <span style=" background-color: #f7f7f7;font-size: 14px;padding: 0 8px;">+88</span>-->
                                                                <input name="EmergencyMobile" type="text" class="form-control" v-model="form.EmergencyMobile"
                                                                        placeholder="Enter your mobile number"
                                                                />
                                                              <div class="error" v-if="form.errors.has('EmergencyMobile')" v-html="form.errors.get('EmergencyMobile')"/>
                                                              <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                        <ValidationProvider name="NID" mode="eager" rules="required" v-slot="{ errors }">
                                                            <div class="form-group row" style="padding-bottom: 10px">
                                                                <label class="col-lg-7 col-form-label text-right">N.I.D<span style="color: red">*</span></label>
                                                                <div class="col-lg-5 text-left">
                                                                    <input name="NID" type="text" class="form-control" v-model="form.NID"/>
                                                                    <div class="error" v-if="form.errors.has('NID')" v-html="form.errors.get('NID')"/>
                                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                                </div>
                                                            </div>
                                                        </ValidationProvider>
                                                        <div class="form-group row" style="padding-bottom: 10px">
                                                            <label class="col-lg-7 col-form-label text-right">Blood Group</label>
                                                            <div class="col-lg-5 text-left">
                                                                <select id="blood-group" class="form-control" v-model="form.BloodGroup">
                                                                    <option disabled value="">-- Select Blood Group --</option>
                                                                    <option v-for="group in bloodGroups" :key="group" :value="group">
                                                                        {{ group }}
                                                                    </option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="form-group row" style="padding-bottom: 10px">
                                                            <label class="col-lg-7 col-form-label text-right">Nationality</label>
                                                            <div class="col-lg-5 text-left">
                                                              <input name="Nationality" type="text" class="form-control" v-model="form.Nationality"/>
                                                              <div class="error" v-if="form.errors.has('Nationality')" v-html="form.errors.get('Nationality')"/>
                                                              <span class="error-message"> {{ errors[0] }}</span>
                                                            </div>
                                                        </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <button type="submit" class="btn btn-primary float-right submit_on_enter">Update</button>
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
          bloodGroups: [
            'A+',
            'A-',
            'B+',
            'B-',
            'O+',
            'O-',
            'AB+',
            'AB-'
          ], // List of blood groups
            form: new Form({
              Id: '',
              InvoiceNo: '',
              FlagshipCode: '',
              MasterCode: '',
              CustomerCode: '',
              CustomerName: '',
              FatherName: '',
              MotherName: '',
              PreAddress: '',
              PerAddress: '',
              MobileNo: '',
              Nationality: '',
              EmergencyMobile: '',
              BloodGroup: '',
              EMail: '',
              NID: '',
              DateOfBirth: '',
              Gender: '',
              OwnerType: '',
              ChassisNo: '',
              EngineNo: '',
              Color: '',
              FuelUsed: '',
              HorsePower: '',
              RPM: '',
              CubicCapacity: '',
              WheelBase: '',
              Weight: '',
              WeightMax: '',
              Standee: '',
              TireSizeFront: '',
              TireSizeRear: '',
              Seats: '',
              NoOfTyre: '',
              NoOfAxel: '',
              ClassOfVehicle: '',
              MakerName: ' IFAD MOTORS LIMITED',
              MakerCountry: '',
              EngineType: '',
              NumberOfCylinders: '',
              ImportYear: '',
            }),
            errors: [],
            isLoading: false,
            buttonShow: false,
            PreLoader: false,
        }
    },
    created() {
      axios.get(baseurl + `api/invoice-edit/${this.$route.params.InvoiceNo}`, this.config()).then((response)=>{
        this.form.fill(response.data.flagshipInvoiceBRTA);
      });
    },
    computed: {
        //
    },
    mounted() {
        document.title = 'Invoice Edit | DMS';
    },
    methods: {
        onSubmit() {
            this.form.post(baseurl + "api/invoice-update", this.config()).then(response => {
                if (response.data.status === 'success') {
                    this.$toaster.success(response.data.message);
                    this.$router.go(this.$router.currentRoute)
                } else {
                    this.$toaster.error(response.data.message);
                }
                this.PreLoader = false;
            }).catch(e => {
                this.PreLoader = false;
                this.$toaster.error(e.data.message);
            });
        },
        validateEmergencyMobile(type) {
            const pattern = /^(01)?[0-9]{11}$/; // Regex for validation
            if(type.length>=11){
                let number =''
                if (type==='EmergencyMobile') {number= this.form.EmergencyMobile}
                else{number= this.form.Mobile }

              if (number && !pattern.test(this.form.number)) {
                  this.$toaster.error('Invalid mobile number. Please enter a valid number.');
                  if (type==='EmergencyMobile') {this.form.EmergencyMobile =''}
                  else{this.form.Mobile='' }
              } else {
                  this.emergencyMobileError = null; // Clear the error if valid
              }
          }
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