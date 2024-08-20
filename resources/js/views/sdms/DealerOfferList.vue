<template>
  <div class="container-fluid" id="users">
    <breadcrumb :options="['Dealer Offer List']">
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
          <label>Customers</label>
          <select class="form-control" v-model="customer">
            <option value="" v-if="me.RoleId === 'admin' || me.RoleId === 'su'">All</option>
            <option v-for="(c,index) in customers" :value="c.CustomerCode" :key="index">{{c.CustomerName}}</option>
          </select>
        </div>
      </div>
      <div class="row" style="padding: 10px 20px;">
        <button class="btn btn-primary btn-sm" @click="filterData"><i class="ti-search"></i> Filter</button>
        &nbsp;
        <button class="btn btn-info btn-sm" @click="reset"><i class="ti-reload"></i> Reset</button>
      </div>
    </div>
    <div id="dealer-content">
      <div class="datatable scrollable" style="overflow-x:auto;height: 400px;">
        <table id="datatable scrollable"
               class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small"
               style="border-collapse: collapse; border-spacing: 0; width: 100%;" v-if="data.length > 0">
          <tr class="thead-dark">
            <th v-for="(col,c) in columns" :key="c">{{ col }}</th>
          </tr>
          <tr v-for="(row,r) in data" :key="r">
            <td v-for="(col,c) in columns">{{row[col]}}</td>
          </tr>
        </table>
        <table id="scrollable"
               class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small"
               style="border-collapse: collapse; border-spacing: 0; width: 100%;" v-else>
          <tr class="thead-dark">
            <td> No Data</td>
          </tr>
        </table>
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
  name: 'DealerOfferList',
  components: {CustomerWiseProductSoldPrint},
  mixins: [Common],
  data() {
    return {
      startDate: '',
      endDate: '',
      customers: [],
      customer: '',
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      data: [],
      columns: []
    }
  },
  computed: {
    me() {
      return this.$store.state.me
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
    filterData() {
      this.axiosPost('sdms-report/dealer-offer-list',{
        startDate: this.startDate,
        endDate: this.endDate,
        customer: this.customer
      },(response) => {
        this.data = response.data
        this.columns = response.data.length > 0 ? Object.keys(this.data[0]) : []
        console.log(this.columns)
      },(error) => {

      })
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
  //height: 300px;
}
</style>
