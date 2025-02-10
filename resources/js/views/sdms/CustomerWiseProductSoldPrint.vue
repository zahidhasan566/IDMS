<template>
  <div class="container-fluid">
    <breadcrumb :options="['SDMS Invoice Print']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <div id="printDiv">
          <div class="row" style="text-align: center;">
            <div class="col-md-12">
              <p style="font-weight: bold;font-size: 20px;">Customer Wise Product Sold By Delivery Date</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-1">
              <img :src="`${mainOrigin}assets/images/logo-svg.png`" style="width:90px;height: 90px;"/>
            </div>
            <div class="col-md-4 head">
              <p style="font-weight: bold;font-size: 16px;">ACI Limited</p>
              <p>Head Office: ACI Centre, 245 Tejgaon Industrial Area</p>
              <p>Dhaka-1208, Bangladesh</p>
            </div>
          </div>
          <div class="row r-info">
            <div class="col-md-2">
              <p>Business </p>
              <p>Customer</p>
            </div>
            <div class="col-md-4">
              <p>: {{ masterInfo.business }}</p>
              <p>: {{ masterInfo.customerCode }} - {{masterInfo.customerName}}</p>
            </div>
            <div class="col-md-2">
              <p>Depot</p>
            </div>
            <div class="col-md-4">
              <p>: {{ masterInfo.depotName }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="table-bordered">
                <table class="table table-sm m-0 small">
                  <thead>
                  <tr>
                    <th>Product Name</th>
                    <th>Sales Qty</th>
                    <th>Bonus Qty</th>
                    <th>Total Qty</th>
                    <th>Discount</th>
                    <th>%</th>
                    <th>TP</th>
                    <th>Gross</th>
                    <th>VAT</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(data,i) in dataSets" :key="i">
                    <td>{{ data.ProductName }}</td>
                    <td class="text-right">{{ numberWithCommas(data.SalesQTY) }}</td>
                    <td class="text-right">{{ Number(data.BonusQTY) }}</td>
                    <td class="text-right">{{ Number(data.TotalQnty) }}</td>
                    <td class="text-right">{{ numberWithCommas(data.Discount) }}</td>
                    <td class="text-right">0</td>
                    <td class="text-right">{{ numberWithCommas(data.SalesTP) }}</td>
                    <td class="text-right">{{ numberWithCommas(data.Gross) }}</td>
                    <td class="text-right">{{ numberWithCommas(data.VAT) }}</td>
                  </tr>
                  <tr>
                    <td colspan="7">Grand Total</td>
                    <td class="text-right">{{ numberWithCommas(totalGross.toFixed(2)) }}</td>
                    <td class="text-right">{{ numberWithCommas(totalVAT.toFixed(2)) }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";

export default {
  mixins: [Common],
  data() {
    return {
      dataSets: [],
      masterInfo: {},
      totalGross: 0,
      totalVAT: 0
    }
  },
  computed: {
    customer() {
      return this.$store.state.customer
    }
  },
  mounted() {
    bus.$on('print-customer-wise-sold',(customerCode,startDate,endDate) => {
      this.getData(customerCode,startDate,endDate)
    })
  },
  destroyed() {
    bus.$off('print-customer-wise-sold')
  },
  methods: {
    getData(customerCode,startDate,endDate) {
      this.axiosPost('sdms-report/customer-wise-product-sold',{
        customerCode: customerCode,
        startDate: startDate,
        endDate: endDate
      },(response) => {
        this.dataSets = response.data
        this.masterInfo = response.masterData
        response.data.forEach((item) => {
          this.totalGross += Number(item.Gross)
          this.totalVAT += Number(item.VAT)
        })
        setTimeout(() => {
          this.print()
        },500)
      },function(error) {

      });
    },
    print() {
      $("#printDiv").printThis({
        importCSS: true,
        importStyle: true,
        loadCSS: "",
        footer: $(".footer")
      });
    }
  }
}
</script>

<style scoped>
#printDiv {
  padding: 15px;
}
.head {
  padding: 10px 0 0 30px;
}
.head p {
  margin: 0;
}
.r-info {
  margin: 40px 0;
}
.r-info p {
  margin: 0;
}
.preserve {
  font-size: 15px;
  color: #000000;
  margin: 30px 0 0 0 !important;
  text-transform: capitalize;
}
.note {
  margin-top: 10px;
  margin-bottom: 10px;
}
.bold {
  font-weight: bold;
  font-size: 14px;
}
.signature {
  margin-top: 50px;
}
.signature p {
  margin: 0;
}
.signature .pad {
  padding: 10px 0;
}
.col-md-1 {
  flex: 0 0 8.333333%;
  max-width: 8.333333%;
  position: relative;
  width: 100%;
  padding-right: 15px;
  padding-left: 15px;
}
.col-md-4 {
  flex: 0 0 33.333333%;
  max-width: 33.333333%;
  position: relative;
  width: 100%;
}
.col-md-3 {
  flex: 0 0 25%;
  max-width: 25%;
  position: relative;
  width: 100%;
}
.col-md-2 {
  flex: 0 0 16.666667%;
  max-width: 16.666667%;
  position: relative;
  width: 100%;
  padding-right: 15px;
  padding-left: 15px;
}
.offset-4 {
  margin-left: 33.333333%;
}

</style>

<style>
thead {
  background: #ffffff !important;
  color: #000000 !important;
}
.table-bordered thead th {
  font-weight: bold;
  color: #000000;
}
</style>