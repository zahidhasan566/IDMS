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
        <button class="btn btn-primary btn-sm" @click="printData"><i class="ti-search"></i> Filter</button>
        &nbsp;
        <button class="btn btn-info btn-sm" @click="reset"><i class="ti-reload"></i> Reset</button>
      </div>
      <div id="content">
        <CustomerWiseProductSoldPrint v-show="false"/>
      </div>
    </div>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";
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
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    }
  },
  mounted() {
    this.getData()
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    getData() {
      this.axiosGet('product-promotion/load-customer',(response) => {
        this.customers = response.data
        this.customer = response.data[0].CustomerCode
      })
    },
    printData() {
      bus.$emit('print-customer-wise-sold',this.customer,this.startDate,this.endDate)
    },
    reset() {
      this.customer = ''
      this.startDate = ''
      this.endDate = ''
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
