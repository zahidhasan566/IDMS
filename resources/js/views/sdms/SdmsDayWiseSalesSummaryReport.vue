<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Customer Wise Product Sold By Delivery Date']">
    </breadcrumb>
    <div class="payment">
      <div class="row">
        <div class="col-md-2">
          <div>
            <label>Start Date</label>
            <datepicker v-model="startDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
          </div>
        </div>
        <div class="col-md-2">
          <label>End Date</label>
          <datepicker v-model="endDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
        </div>
        <div class="col-md-2">
          <label>Customers</label>
          <select class="form-control" v-model="customer">
            <option v-for="(c,index) in customers" :value="c.CustomerCode" :key="index">{{c.CustomerName}}</option>
          </select>
        </div>
      </div>
      <div class="row" style="padding: 10px 20px;">
        <button class="btn btn-primary btn-sm" @click="filter"><i class="ti-search"></i> Filter</button>
        &nbsp;
        <button class="btn btn-info btn-sm" @click="reset"><i class="ti-reload"></i> Reset</button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="table-bordered table-responsive">
          <table class="table table-sm m-0 small">
            <thead>
            <tr>
              <th>SL</th>
              <th>Code</th>
              <th v-for="(head,i) in headers" :key="i">{{ head }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(data,i) in dataSets" :key="i" v-if="dataSets.length > 0">
              <td>{{ i+1 }}</td>
              <td>{{ data.Customer }}</td>
              <td v-for="(head,index) in headers" :key="index">{{ data[`${head}`] }}</td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center;">No Data</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import {Common} from "../../mixins/common";
import CustomerWiseProductSoldPrint from "./CustomerWiseProductSoldPrint.vue";

export default {
  components: {CustomerWiseProductSoldPrint},
  mixins: [Common],
  data() {
    return {
      startDate: '',
      endDate: '',
      customers: [],
      customer: '',
      dataSets: [],
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      headers: {}
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      this.axiosGet('product-promotion/load-customer',(response) => {
        this.customers = response.data
        this.customer = response.data[0].CustomerCode
      })
    },
    filter() {
      if (this.startDate && this.endDate && this.customer) {
        this.axiosPost('sdms-report/day-wise-sales-summary-report',{
          customerCode: this.customer,
          startDate: this.startDate,
          endDate: this.endDate
        },(response) => {
          this.dataSets = response.data
          this.masterInfo = response.masterInfo
          let columns = Object.keys(this.dataSets[0])
          columns = columns.filter((item) => {
            return item !== 'Customer'
          })
          this.headers = columns
        },function(error) {

        });
      }
    },
    reset() {
      this.startDate = ''
      this.endDate = ''
      this.dataSets = []
      this.headers = []
    }
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
#content {
  height: 300px;
}
</style>
