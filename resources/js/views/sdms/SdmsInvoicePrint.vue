<template>
  <div class="container-fluid">
    <breadcrumb :options="['SDMS Invoice Print']"></breadcrumb>
    <div class="card">
      <div class="card-body">
        <div id="printDiv">
          <div class="row" style="text-align: center;">
            <div class="col-md-12">
              <p style="font-weight: bold;font-size: 20px;">Invoice</p>
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
            <div class="offset-4 col-md-3 head">
              <p>Yamaha Motorcycles</p>
              <p>Head Office</p>
            </div>
          </div>
          <div class="row r-info">
            <div class="col-md-2">
              <p>Code</p>
              <p>Type</p>
              <p>Name</p>
              <p>Contact Person</p>
              <p>Address </p>
              <p>Mobile </p>
            </div>
            <div class="col-md-4">
              <p>: {{invoice.CustomerCode}}</p>
              <p>: {{invoice.CustTypeName}}</p>
              <p>: {{invoice.CustomerName}}</p>
              <p>: {{invoice.ContactPerson}}</p>
              <p>: {{invoice.Address}}</p>
              <p>: {{invoice.Mobile}}</p>
            </div>
            <div class="col-md-2">
              <p>Invoice No</p>
              <p>CISS</p>
              <p>Invoice Date</p>
              <p>Delivery Date</p>
              <p>Field Force</p>
              <p>Territory</p>
              <p>Reference </p>
              <p>P.O. No.& Date </p>
            </div>
            <div class="col-md-4">
              <p>: {{invoiceNo}}</p>
              <p>: {{invoice.CISSNo}}</p>
              <p>: {{invoice.InvoiceDate}}</p>
              <p>: {{invoice.DeliveryDate}}</p>
              <p>: {{invoice.Level1Name}}</p>
              <p>: {{invoice.TTYName}}</p>
              <p>: {{invoice.Reference}}</p>
              <p>: {{invoice.PONumber}} - {{invoice.PODate}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="table-bordered">
                <table class="table table-sm m-0 small">
                  <thead>
                  <tr>
                    <th>SL</th>
                    <th>Description</th>
                    <th>DP</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Amount (TK.)</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(inv,index) in invoices" :key="index">
                    <td>{{index + 1}}</td>
                    <td>
                      <span style="font-weight: bolder">{{inv.ProductName}}</span>
                      <p style="margin: 0;" v-for="(invoice,i) in inv.ChassisList" :key="i">{{ invoice }}</p>
                    </td>
                    <td>{{Number(inv.DP)}}</td>
                    <td>{{inv.Quantity}}</td>
                    <td>{{Number(inv.Discount)}}</td>
                    <td>{{Number(inv.NET)}}</td>
                  </tr>
                  <tr>
                    <td colspan="3" align="right">Total</td>
                    <td>{{totalQuantity}}</td>
                    <td>{{totalDiscountAmount}}</td>
                    <td>{{totalAfterDiscountAmount}}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <p class="preserve" v-if="Number(invoice.AffiliatorDiscount) > 0">In Word: {{ inWords(Math.ceil(totalAfterDiscountAmount - Number(invoice.AffiliatorDiscount))) }}</p>
              <p class="preserve" v-else>In Word: {{ inWords(Math.ceil(totalAfterDiscountAmount)) }}</p>
            </div>
          </div>
          <div class="row signature">
            <div class="col-md-6" style="text-align: center">
              <p class="bold">Product & Warranty Period</p>
              <div class="pad" style="border-top: 1px solid #000000">
                <div style="margin-top: 20px;">
                  <div style="height: 30px; border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;"></div>
                  <div style="border: 1px solid #000000">
                    <p>Received By</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6" style="text-align: center">
              <p class="bold">Warranty Not Covered</p>
              <div class="pad" style="border-top: 1px solid #000000">
                <div style="margin-top: 20px;">
                  <div style="height: 30px; border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000;"></div>
                  <div style="border: 1px solid #000000">
                    <p>Delivered By</p>
                  </div>
                </div>
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
      invoiceNo: '',
      invoice: '',
      invoices: [],
      totalDiscountAmount: 0,
      totalAfterDiscountAmount: 0,
      totalQuantity: 0
    }
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  mounted() {
    document.title = 'SDMS Invoice Print | DMS';
    bus.$on('print-invoice',(invoiceNo) => {
      this.getData(invoiceNo)
    })
  },
  methods: {
    getData(invoiceNo) {
      this.axiosGet('sdms-report/invoice-list/'+invoiceNo,(response) => {
        this.invoiceNo = response.invoiceNo
        this.invoice = response.data[0]
        let invoices = []
        response.data.forEach((item) => {
          let inv = invoices.find((each) => {
            return each.ProductName === item.ProductName
          })
          if (inv) {
            if (item.ChassisNo.length > 4) {
              inv.ChassisList.push(item.ChassisNo)
            }
          } else {
            if (item.ChassisNo.length > 4) {
              item.ChassisList = []
              item.ChassisList.push(item.ChassisNo)
            }
            invoices.push(item)
          }
        })
        invoices.forEach((item) => {
          this.totalQuantity += Number(item.Quantity)
          this.totalDiscountAmount += Number(item.Discount)
          this.totalAfterDiscountAmount += Number(item.NET)
        })
        this.invoices = invoices
        setTimeout(() => {
          this.print()
          // window.print()
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