<template>
  <div class="container-fluid">
    <breadcrumb :options="['Challan List']">
      <button type="button" class="btn btn-primary btn-sm" @click="addChallan">Add Challan</button>
      <button type="button" class="btn btn-success btn-sm" @click="exportChallan">Export</button>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <div class="d-flex">
                <div class="flex-grow-1">
                  <ValidationObserver v-slot="{ handleSubmit }">
                    <form @submit.prevent="handleSubmit(getAllChallan)" @keydown.enter="$event.preventDefault()">
                      <div class="row">
                        <div class="col-md-2">
                          <ValidationProvider name="DateFrom" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Date From</label>
                              <date-picker v-model="form.DateFrom" valueType="format"></date-picker>
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-md-2">
                          <ValidationProvider name="DateTo" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Date To</label>
                              <date-picker v-model="form.DateTo" valueType="format"></date-picker>
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-md-2">
                          <ValidationProvider name="ChallanNo" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Challan No<span class="error">*</span></label>
                              <input class="form-control" type="text" value="" v-model="form.ChallanNo" placeholder="Enter ChallanNo">
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-md-2" style="margin-top: 30px">
                          <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                          <button type="button" class="btn btn-info" @click="loadInvoice"><i class="mdi mdi-reload"></i>Reload</button>
                        </div>
                      </div>
                    </form>
                  </ValidationObserver>
                </div>
                <div class="card-tools">
                  <div class="row">
                    <div class="col-md-12">
                      <!--                      <input v-model="query" type="text" class="form-control" placeholder="Search">-->
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                  <thead class="thead-dark">
                  <tr>
                    <th v-for="(item, index) in headers">
                      {{formatHeading(item.toString())}}
                    </th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in contents" >
                    <td v-for="(item2, index) in headers" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' ">
                      {{ item[item2] }}
                    </td>
                    <td>
                      <button @click="singleChallan(item)" class="btn btn-success btn-sm">Preview</button>
                      <button @click="edit(item)" class="btn btn-success btn-sm">Re Upload</button>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="data-count">
                    Show {{ form.pagination.from }} to {{ form.pagination.to }} of {{ form.pagination.total }} rows
                  </div>
                </div>
                <div class="col-8">
                  <report-pagination
                      v-if="form.pagination.last_page > 1"
                      :pagination="form.pagination"
                      :offset="5"
                      @paginate="getAllChallan()"
                  ></report-pagination>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <skeleton-loader :row="14"/>
          </div>
        </div>
      </div>
    </div>
    <data-export/>

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                     <img width="100%" :src="showImage(challanImage)"
                                @click="rotation" class="transition" v-bind:style="{transform: `rotate(${deg}deg)`}"
                                alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="challanModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title mt-0" id="myLargeModalLabel">{{ editMode ? "Edit" : "Add" }} Challan Bill</h5>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" @click="closeChallanModal">×</button>
          </div>
          <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
          <div class="modal-body">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Challan Number<span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="ChallanNumber" v-model="form2.ChallanNumber" placeholder="Enter Challan No" :class="{ 'is-invalid': form2.errors.has('ChallanNumber') }">
                    <div class="error" v-if="form2.errors.has('ChallanNumber')" v-html="form2.errors.get('ChallanNumber')" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Upload<span style="color: red">*</span></label>
                    <input @change="changeImage($event)" type="file" name="ChallanImage" class="form-control" :class="{ 'is-invalid': form2.errors.has('ChallanImage') }">
                    <div class="error" v-if="form2.errors.has('ChallanImage')" v-html="form2.errors.get('ChallanImage')" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <img v-if="form2.ChallanImage" :src="showUploadedImage(form2.ChallanImage)" alt="" height="1000" style="width: 100%">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeChallanModal">Close</button>
            <button type="submit" class="btn btn-primary" >{{ editMode ? "Update" : "Create" }}</button>
          </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import {baseurl} from '../../../base_url'
import {Common} from "../../../mixins/common";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import moment from "moment";
import {AWS_S3_COMMON_DOCUMENT_LINK} from "../../../base_url";
import axios from "axios";

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      headers: [],
      contents: [],
      query: '',
      errors: [],
      isLoading: false,
      exportShow: false,
      form: new Form({
        DateFrom: moment().subtract(1, 'month').format('yyyy-MM-DD'),
        DateTo: moment().format('yyyy-MM-DD'),
        ChallanNo: '',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
          last_page: 1,
        },
        Export :'',
      }),
      form2: new Form({
        ChallanID: '',
        ChallanNumber: '',
        ChallanImage: '',
      }),
      challanImage: '',
      editMode: false,
      deg: 0,
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Challan List | DMS';
    this.getAllChallan();
  },
  methods: {
      rotation() {
          this.deg += 90;
      },
    getAllChallan(){
      this.isLoading = true
      this.Export = '';
      this.form.post(baseurl + "api/logistics/get-all-challan-list", this.config()).then(response => {
        if (response.data.data.length > 0){
          this.headers = Object.keys(response.data.data[0])
          this.contents = response.data.data
          this.exportShow = false;
          this.isLoading = false
        }else {
          this.contents = []
          this.exportShow = true;
          this.isLoading = false
        }
        this.form.pagination.current_page = response.data.paginationData[0].current_page;
        this.form.pagination.from = response.data.paginationData[0].from;
        this.form.pagination.to = response.data.paginationData[0].to;
        this.form.pagination.total = response.data.paginationData[0].total;
        this.form.pagination.last_page = response.data.paginationData[0].last_page;
        this.isLoading = false;

      }).catch(e => {
        //
      });
    },
    singleChallan(item){
      this.challanImage = item.ChallanImage;
        console.log(this.challanImage)
      $("#imageModal").modal("show");
    },
    showImage(image){
      return baseurl + 'assets/images/challan_image/' + image;
    },
    closeModal() {
      $("#imageModal").modal("hide");
    },
    addChallan(){
      this.form2.reset();
      this.form2.clear();
      $("#challanModal").modal("show");
    },
    closeChallanModal(){
      $("#challanModal").modal("hide");
    },
    changeImage(event){
      let file = event.target.files[0];
      let reader = new FileReader();
      reader.onload = event => {
        this.form2.ChallanImage = event.target.result;
      };
      reader.readAsDataURL(file);
    },
    showUploadedImage(image){

        console.log(image)
      let img = this.form2.ChallanImage;
      if (img.length > 100){
        return this.form2.ChallanImage;
      }else{
          return baseurl + 'assets/images/challan_image/' + image
      }
    },
    store(){
      this.form2.busy = true;
      this.form2.post(baseurl+ "api/logistics/upload-challan", this.config()).then(response => {
        this.$toaster.success(response.data.message);
        $("#challanModal").modal("hide");
        this.getAllChallan();
      }).catch(e => {
        this.$toaster.error('Something Went Wrong');
        this.isLoading = false;
      });
    },
    update(){
      this.form2.busy = true;
      this.form2.post(baseurl+ "api/logistics/update-challan", this.config()).then(response => {
        this.$toaster.success(response.data.message);
        $("#challanModal").modal("hide");
        this.getAllChallan();
      }).catch(e => {
        this.$toaster.error('Something Went Wrong');
        this.isLoading = false;
      });
    },
    edit(role) {
      console.log(role)
      this.editMode = true;
      this.form2.reset();
      this.form2.clear();
      this.form2.fill(role);
      $("#challanModal").modal("show");
    },
    exportChallan(){
      this.form.Export = 'Y';
      // this.form.post(baseurl + "api/logistics/get-all-challan-list", this.config())
      //     .then((response)=>{
      //       let invoice = response.data.invoice.original;
      //       let dataSets = invoice.data;
      //       if (dataSets.length > 0) {
      //         let columns = Object.keys(dataSets[0]);
      //         columns = columns.filter((item) => item !== 'row_num');
      //         let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
      //         columns = columns.map((item) => {
      //           let title = item.replace(rex, '$1$4 $2$3$5')
      //           return {title, key: item}
      //         });
      //         bus.$emit('data-table-import', dataSets, columns, 'Challan Export')
      //       }
      //     }).catch((error)=>{
      // })
    },
    loadInvoice(){
      this.form.ChassisNo = '';
      this.getAllChallan();
    },
    formatHeading(item) {
      let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
      let title = item.replace(rex, '$1$4 $2$3$5')
      return title.replace('_',' ')
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
.transition {
    transition: transform 0.5s ease-in-out;
}
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
</style>