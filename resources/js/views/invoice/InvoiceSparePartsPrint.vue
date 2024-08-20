<template>
  <div class="container-fluid">
    <breadcrumb :options="['Spare Parts Invoice Print']"></breadcrumb>
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
              <p>Invoice No: {{invoice.InvoiceNo}}</p>
              <p>Invoice Date: {{moment(invoice.InvoiceDate).format('DD-MM-YYYY')}}</p>
            </div>
          </div>
          <div class="row r-info">
            <div class="col-md-2">
              <p>Name</p>
              <p>Address</p>
              <p>Customer Mobile</p>
            </div>
            <div class="col-md-4">
              <p></p>
              <p>: {{invoice.CustomerName}}</p>
              <p>: {{invoice.PreAddress}}</p>
              <p>: {{invoice.MobileNo}}</p>
            </div>
            <div class="col-md-2">
              <p>Dealer</p>
              <p>Address</p>
              <p>Dealer Mobile</p>
            </div>
            <div class="col-md-4">
              <p>: {{invoice.DealerName}}</p>
              <p>: {{invoice.Add1 + ' '+invoice.Add2}}</p>
              <p>:  {{invoice.Mobile}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="table-bordered">
                <table class="table table-sm m-0 small">
                  <thead class="thead-dark">
                  <tr>
                    <th>SL</th>
                    <th>Particulars</th>
                    <th>BDT</th>
                    <th>PCS</th>
                    <th>Discount(%)</th>
                    <th>Discount Amount</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(inv,index) in invoices" :key="index">
                    <td>{{index + 1}}</td>
                    <td>{{inv.ProductName}} ({{inv.ProductCode}})</td>
                    <td>{{inv.UnitPrice}}</td>
                    <td>{{inv.Quantity}}</td>
                    <td>{{Number(inv.Discount)}}</td>
                    <td>{{inv.DiscountAmount}}</td>
                    <td>{{inv.Total}}</td>
                  </tr>
                  <tr>
                    <td colspan="5" align="right">Total</td>
                    <td>{{totalDiscountAmount}}</td>
                    <td>{{totalAfterDiscountAmount}}</td>
                  </tr>
                  <tr v-if="Number(invoice.AffiliatorDiscount) > 0">
                    <td colspan="5" align="right">Affiliator Discount</td>
                    <td>{{invoice.AffiliatorDiscount}}</td>
                    <td>{{totalAfterDiscountAmount - Number(invoice.AffiliatorDiscount)}}</td>
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
            <div class="col-md-4">
              <p class="bold">Customer Signature</p>
              <div class="pad">
                <p>Signature: </p>
                <p>Date: </p>
              </div>
            </div>
            <div class="offset-md-4 col-md-4">
              <p class="bold">Authorised Signature</p>
              <div class="pad">
                <p>Signature: </p>
                <p>Date: </p>
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
import * as url from "url";

export default {
  mixins: [Common],
  data() {
    return {
      invoice: {},
      invoices: [],
      totalDiscountAmount: 0,
      totalAfterDiscountAmount: 0
    }
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  created() {
    this.getData()
  },
  mounted() {
    document.title = 'Spare Parts Invoice List | DMS';
  },
  methods: {
    getData() {
      console.log(this.$route.params)
      this.axiosGet('invoice-spare-parts/print/'+this.$route.params.invoiceId,(response) => {
        this.invoice = response.data
        let invoiceDetails = []
        response.data.parts_details.forEach((item) => {
          let discountAmount = (item.UnitPrice * item.Quantity) * (item.Discount/100)
          let total = item.UnitPrice * item.Quantity
          let afterDiscountAmount = total - discountAmount
          //TOTAL
          this.totalDiscountAmount += discountAmount
          this.totalAfterDiscountAmount += Number(afterDiscountAmount)
          invoiceDetails.push({
            ProductName: item.ProductName,
            ProductCode: item.ProductCode,
            UnitPrice: item.UnitPrice,
            Quantity: item.Quantity,
            Discount: item.Discount,
            DiscountAmount: discountAmount,
            Total: afterDiscountAmount
          })
        })
        this.invoices = invoiceDetails
        setTimeout(() => {
          window.print()
        },2000)
      },function(error) {

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
}
</style>