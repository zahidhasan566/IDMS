<template>
  <div class="container-fluid">
    <breadcrumb :options="['Free Service Follow Up List']">
      <button type="button" class="btn btn-success btn-sm" @click="exportFreeServiceFollowUp">Export</button>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(getAllFreeServiceFollowUp)" @keydown.enter="$event.preventDefault()">
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
                    <div class="col-md-2" style="margin-top: 30px">
                      <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                      <button type="button" class="btn btn-info" @click="loadService"><i class="mdi mdi-reload"></i>Reload</button>
                    </div>
                  </div>
                </form>
              </ValidationObserver>
              <div v-if="contents.length > 0">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead class="thead-dark">
                    <tr>
                      <th style="width: 9%">Action</th>
                      <th v-for="(item, index) in headers" v-if="index !== 0 && index !== 32">
                        {{ item.replace(/_/g, ' ', " $1").trim() }}
                      </th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in contents" >
                      <td>
                        <a href="javascript:" @click="addModal(item.Schedule_ID )"> <i class="ti-pencil-alt">Edit</i></a>
                      </td>
                      <td
                          v-for="(item2, index) in headers.slice(1)" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 32">
                        {{ item[item2] }}
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
                        @paginate="form.Query === '' ? getAllFreeServiceFollowUp() : getAllFreeServiceFollowUp()"
                    ></report-pagination>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <add-edit-follow-up  @changeStatus="changeStatus" v-if="loading"/>
          <div v-else>
            <skeleton-loader :row="14"/>
          </div>
        </div>
      </div>
    </div>
    <data-export/>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";
import moment from "moment";
import DatePicker from "vue2-datepicker";
export default {
  name: "Free Service Follow Up List",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      headers: [],
      contents: [],
      loading: false,
      cpLoading: false,
      tagLoading:false,
      query: '',
      errors: [],
      isLoading: false,
      exportShow: false,
      isAdmin : '',
      form: new Form({
        DateFrom: moment().add(1,'days' ).format('yyyy-MM-DD'),
        DateTo:moment().add(2,'days' ).format('yyyy-MM-DD'),
        Query :'',
        Export :'',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
          last_page: 1,
        },
      }),
    }
  },
  created() {
    //
  },
  computed:{
    //
  },

  mounted() {
    document.title = 'Free Service Follow Up List | DMS';
    this.getAllFreeServiceFollowUp();
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  destroyed() {
    bus.$off('export-data')
  },
  methods: {
    getAllFreeServiceFollowUp(){
      this.isLoading = true
      this.form.Export = '';
      this.form.post(baseurl + "api/jobCard/free-service-followup", this.config()).then(response => {
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
      }).catch(e => {
        //
      });
    },
    exportFreeServiceFollowUp(){
      this.form.Export = 'Y';
      this.exportShow = true;
      this.form.post(baseurl + "api/jobCard/free-service-followup", this.config()).then(response => {
        let dataSets = response.data.data;
        if (dataSets.length > 0) {
          let columns = Object.keys(dataSets[0]);
          columns = columns.filter((item) => item !== 'row_num');
          let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
          columns = columns.map((item) => {
            let title = item.replace(rex, '$1$4 $2$3$5')
            return {title, key: item}
          });
          //this.generateExport(dataSets, columns, 'Job Card Report')
          bus.$emit('data-table-import', dataSets, columns, 'Bike Sales Report')
          this.exportShow = false;
        }
      }).catch(e => {
        //
      });
    },
    loadService(){
      this.form.ChassisNo = '';
      this.getAllFreeServiceFollowUp();
    },
    changeStatus() {
      this.loading = false
    },
    addModal(item ) {
      this.row =item;
      this.loading = true;
      setTimeout(() => {
        bus.$emit('add-edit-follow-up', this.row);
      })
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