<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Print Invoice']">

            </breadcrumb>
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center align-items-center" style="padding:8px 0px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold" for="chassisNo">Chassis No</label>
                                <input type="text" class="form-control" v-model="chassisNo" placeholder="Chassis No">
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-success" @click="chassisNoPreview" :disabled="!chassisNo">Preview</button>
                            <button type="button" class="btn btn-primary" @click="editCustomer" v-if="contents.length>0">Edit Invoice</button>
                        </div>
                    </div>
                  <div class="row form-divider" v-if="contents.length>0">
                    <div class="form-divider-title">
                      <p style="width: 160px;font-weight: bolder;">Bike Details</p>
                    </div>
                  <div class="col-md-6">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                        <tbody v-for="(item, index) in contents">
                        <tr v-for="(item2, index) in headers" v-if="index <15">
                          <td style="background: #22264a;color: white;font-weight: bolder;">{{formatHeading(item2.toString())}}</td>
                          <td  style="font-weight: bolder;"> {{ item[item2] }}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                        <tbody v-for="(item, index) in contents">
                        <tr v-for="(item2, index) in headers" v-if="index >15">
                          <td style="background: #22264a;color: white;font-weight: bolder;">{{formatHeading(item2.toString())}}</td>
                          <td style="font-weight: bolder;"> {{ item[item2] }}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" data-backdrop="static" id="printInvoice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <div class="modal-title modal-title-font" id="exampleModalLabel">{{"Edit" }}
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" @click="closeModal">Close
                    </button>
                  </div>
                  <ValidationObserver v-slot="{ handleSubmit }">
                    <form class="form-horizontal" id="formProduction"  @submit.prevent="handleSubmit(onSubmit) " @keydown.enter="$event.preventDefault()">
                      <div class="card">
                        <div class="row m-2">
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="customerName" mode="eager" rules="required"
                                                v-slot="{ errors }">
                              <div class="form-group">
                                <label for="Customer">Customer Name <span class="error">*</span></label>
                                <input type="text" class="form-control" id="customerName"  data-required="true" v-model="form.customerName" name="customerName" placeholder="Customer Name">
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="customerMail" mode="eager"
                                                v-slot="{ errors }">
                              <div class="form-group">
                                <label for="customerMail">Customer Email <span class="error">*</span></label>
                                <input type="email" class="form-control" id="customerMail"  v-model="form.customerMail" name="customerMail" placeholder="example@gmail.com">
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="mobile" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group">
                                <label for="mobile">Customer Mobile No<span class="error">*</span></label>
                                <input type="tel" class="form-control" id="mobile"  data-required="true" v-model="form.mobile" name="mobile" placeholder="xxxxxxxxxxxx" pattern='(01)?[0-9]{11}'>
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>

                          <div class="col-12 col-md-4" >
                            <ValidationProvider name="fatherName" mode="eager" rules="required" v-slot="{ errors }" >
                              <div class="form-group">
                                <label for="fatherName">Father Name<span class="error">*</span></label>
                                <input type="text" class="form-control" id="fatherName" data-required="true" v-model="form.fatherName" name="fatherName" placeholder="Father Name">
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>

                          <div class="col-12 col-md-4">
                            <ValidationProvider name="motherName" mode="eager" rules="required" v-slot="{ errors }">
                              <label for="motherName">Mother Name<span class="error">*</span></label>
                              <input type="text" class="form-control" id="motherName" data-required="true" v-model="form.motherName" name="motherName" placeholder="Mother Name">
                              <span class="error-message"> {{ errors[0] }}</span>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="presentAddress" mode="eager" rules="required"
                                                v-slot="{ errors }">
                              <div class="form-group">
                                <label for="presentAddress">Present Address<span class="error">*</span></label>
                                <input type="text" class="form-control" id="presentAddress" data-required="true" v-model="form.presentAddress" name="Address" placeholder="Present Address">
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="permanentAddress" mode="eager" rules="required"
                                                v-slot="{ errors }">
                              <div class="form-group">
                                <label for="permanentAddress">Permanent Address<span class="error">*</span></label>
                                <input type="text" class="form-control" id="permanentAddress" data-required="true" v-model="form.permanentAddress" name="Address" placeholder="Permanent Address">
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-4">
                            <ValidationProvider name="NID" mode="eager" rules="required" v-slot="{ errors }">
                              <label for="NID">NID</label>
                              <input type="text" class="form-control" id="NID" data-required="true" v-model="form.NID" name="NID" placeholder="NID">
                              <span class="error-message"> {{ errors[0] }}</span>
                            </ValidationProvider>
                          </div>
                        </div>
                        <div class="col-md-12" style="text-align: end; margin-top: 10px;">
                          <div class="form-group row mb-0">
                            <div class="col-sm-12 text-right">
                              <submit-form name="Create"/>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </ValidationObserver>
                </div>
              </div>
            </div>
            <div>
              <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
            </div>
        </div>
</template>

<script>
import axios from 'axios';
import { baseurl } from "../../base_url";
import { Common } from "../../mixins/common";

export default {
    mixins: [Common],
    data() {
        return {
          chassisNo: '',
          contents: [],
          customers: [],
          headers: [],
          isLoading: false,
          form:({
              invoiceId:0,
              mobile:'',
              customerName:'',
              customerMail:'',
              fatherName:'',
              motherName:'',
              permanentAddress:'',
              presentAddress:'',
              NID:'',
          }),
          errors: [],
          buttonShow: false,
          isMenuDisabled: false,
          loaded: false,
          PreLoader: false,
          loading: false,
        }
    },
    methods: {
        chassisNoPreview() {
            axios.post(
                `${baseurl}api/invoice/get-chassis-no-info`,
                { chassisNo: this.chassisNo },
                this.config()
            ).then(response => {
              this.headers = Object.keys(response.data.data[0])
              this.contents = response.data.data
              this.customers = response.data.data


              this.form.mobile =response.data.data[0].CustomerMobile
              this.form.customerMail =response.data.data[0].CustomerEmail
              this.form.customerName =response.data.data[0].CustomerName
              this.form.fatherName =response.data.data[0].FatherName
              this.form.motherName =response.data.data[0].MotherName
              this.form.NID =response.data.data[0].NID
              this.form.permanentAddress =response.data.data[0].PermanentAddress
              this.form.presentAddress =response.data.data[0].PresentAddress
              this.form.invoiceId =response.data.data[0].InvoiceId
            }).catch(error => {
                this.errorNoti(error.response.data.message || 'Error fetching preview data');
            });
        },
        onSubmit() {
        this.PreLoader = true;
        this.$store.commit('submitButtonLoadingStatus', true);
        this.axiosPost('invoice/store-print-invoice',{
          invoiceId:this.form.invoiceId,
          mobile:this.form.mobile,
          customerMail:this.form.customerMail,
          customerName:this.form.customerName,
          fatherName:this.form.fatherName,
          motherName:this.form.motherName,
          NID:this.form.NID,
          permanentAddress:this.form.permanentAddress,
          presentAddress:this.form.presentAddress,
        }, (response) => {
          if (response.status === 'success') {
            this.PreLoader = false;
            this.successNoti(response.message);
            this.$store.commit('submitButtonLoadingStatus', false);
            this.closeModal();
            this.chassisNoPreview()
          } else {
            this.PreLoader = false;
            this.$store.commit('submitButtonLoadingStatus', false);
            this.errorNoti(response.message);
          }
        })
      },
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: { Authorization: `Bearer ${token}` }
            };
        },
      editCustomer() {
        this.editMode = false;
        this.isMenuDisabled = false;
        $('#printInvoice').modal({backdrop: 'static', keyboard: false});
        $("#printInvoice").modal("show");
        this.chassisNoPreview();
        //Focus First Element
      },
      closeModal() {
        $("#printInvoice").modal("hide");
        this.form.customers = []
        this.buttonStatus = false
        this.chassisNoPreview();
      },
      changeStatus() {
        this.loading = false
      },
      formatHeading(item) {
        let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
        let title = item.replace(rex, '$1$4 $2$3$5')
        return title.replace('_',' ')
      },
      isInt(value) {
        return !isNaN(parseInt(value * 1))
      },
    },
}
</script>

<style scoped>
.fw-bold {
    font-weight: bold;
}
.border {
    padding: 5px;
}
.error-message {
    color: red;
    font-size: 0.875rem;
}
.form-divider-title {
  position: relative;
  top: -20px;
}
.form-divider {
  padding: 6px 5px 5px 5px;
  border: 1px solid #4d87f64f;
  border-radius: 13px;
  margin: 0 auto;
}

.form-divider-title p {
  position: absolute;
  padding: 0 25px;
  background: #ffffff;
  text-transform: uppercase;
  font-weight: bold;
  color: #000000  !important;
  font-size: 12px;
}
</style>
