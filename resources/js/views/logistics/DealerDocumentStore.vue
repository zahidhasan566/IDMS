<template>
  <div class="container-fluid">
    <breadcrumb :options="['Common Document']">
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(onSubmit)" ref="form_1">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 200px">Upload Files</p>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group">
                            <label>Document<span class="error">*</span></label>
                            <input type="file" @change="onFileChange($event)" accept="application/pdf" multiple>
                          </div>
                          <ul>
                            <li v-for="(file, index) in files" :key="index">{{ file.name }}</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <br>
                  <button type="submit" class="btn btn-primary float-right submit_on_enter">Submit</button>
<!--                  @click="uploadFiles"-->
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
import axios from 'axios';
import {bus} from "../../app";

export default {
  name: "Dealer Document Upload Panel",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      files: [],
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      errors: [],
      isLoading: false,
      buttonShow: false,
      PreLoader: false,
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Dealer Document | DMS';
  },
  methods: {
    onFileChange(e) {
      this.files = e.target.files;
    },
    onSubmit(){
      let formData = new FormData();
      for (let i = 0; i < this.files.length; i++) {
        formData.append('files[]', this.files[i]);
      }
      axios.post(baseurl + "api/logistics/dealer-document", formData, this.config()).then(response => {
        this.successNoti(response.data.message);
        this.$store.commit('submitButtonLoadingStatus', false);
        this.$refs.form_1.reset();
        this.files=[];
      }).catch(error => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      });
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
</style>