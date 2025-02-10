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
            <div class="col-md-3">
              <p>
                <span v-if="masterInfo.paymentMode === 'CA'">Customer Ledger (Cash)</span>
                <span v-else-if="masterInfo.paymentMode === 'CR'">Customer Ledger (Credit)</span>
                <span v-else-if="masterInfo.paymentMode === 'DP'">Customer Ledger (Deposit)</span>
              </p>
              <p>From {{masterInfo.startDate}} </p>
              <p>Code</p>
              <p>Customer Name</p>
              <p>Contact Person</p>
              <p>Address </p>
              <p>Mobile </p>
            </div>
            <div class="col-md-4">
              <p>&nbsp;</p>
              <p>To {{masterInfo.endDate}}</p>
              <p>: {{ masterInfo.customerCode }}</p>
              <p>: {{ masterInfo.customerName }}</p>
              <p>: {{ masterInfo.contactPerson }}</p>
              <p>: {{ masterInfo.address1 }} {{masterInfo.address2}}</p>
              <p>: {{ masterInfo.mobile }}</p>
            </div>
            <div class="col-md-3">
              <p>Credit Limit</p>
              <p>Days Limit</p>
              <p>Credit Due</p>
              <p>Age</p>
              <p>Security Deposit</p>
            </div>
            <div class="col-md-2">
              <p>: 0.00</p>
              <p>: 0.00</p>
              <p>: {{ masterInfo.dueAmount }}</p>
              <p>: {{masterInfo.dueDays}}</p>
              <p>: 0.00</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="table-bordered">
                <table class="table table-sm m-0 small">
                  <thead>
                  <tr>
                    <th>Type</th>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Paid Invoice No.</th>
                    <th>Paid Inv Amount Reference</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>Opening</td>
                    <td></td>
                    <td>{{ masterInfo.startDate }}</td>
                    <td></td>
                    <td>{{ masterInfo.openingRaw < 0 ? '('+numberWithCommas(Math.abs(masterInfo.openingRaw))+')' : numberWithCommas(masterInfo.openingRaw) }}</td>
                    <td><span style="font-weight: bold">Invoice</span>: {{ masterInfo.invoice }}</td>
                    <td><span style="font-weight: bold">Payment</span>: {{ masterInfo.payment }} <span style="font-weight: bold">Adj</span>: {{ masterInfo.adjustment }}</td>
                  </tr>
                  <tr v-for="(value,i) in customerLedgerDetails" :key="i">
                    <td>{{ value.TransName }}</td>
                    <td>{{ value.TransNo }}</td>
                    <td>{{ value.TransDate }}</td>
                    <td>{{ Number(value.Amount) < 0 ? '('+numberWithCommas(Math.abs(Number(value.Amount)))+')' : numberWithCommas(Number(value.Amount)) }}</td>
                    <td>
                      {{ Number(value.balance) < 0 ? '('+numberWithCommas(Math.abs(Number(value.balance)))+')' : numberWithCommas(Number(value.balance)) }}
<!--                      <table style="width: 100%">-->
<!--                        <tr v-for="(v,ind) in value.invoiceInfo"><td :style="ind+1 < value.invoiceInfo.length ? 'border:none !important; border-bottom: 1px solid #dee2e6 !important':'border:none !important'">{{ Number(v.balance) > 0 ? numberWithCommas(Number(v.balance.toFixed(2))) : '('+numberWithCommas(Math.abs(v.balance).toFixed(2))+')' }}</td></tr>-->
<!--                      </table>-->
                    </td>
                    <td>
                      <table style="width: 100%">
                        <tr v-for="(v,ind) in value.invoiceInfo"><td :style="ind+1 < value.invoiceInfo.length ? 'border:none !important; border-bottom: 1px solid #dee2e6 !important':'border:none !important'">{{ v.invoiceNo }}</td></tr>
                      </table>
                    </td>
                    <td>
                      <table style="width: 100%">
                        <tr v-for="(v,ind) in value.invoiceInfo"><td :style="ind+1 < value.invoiceInfo.length ? 'border:none !important; border-bottom: 1px solid #dee2e6 !important':'border:none !important'">{{ v.invoiceAmount ? numberWithCommas(v.invoiceAmount) : 0 }} - {{ v.referenceNo }}</td></tr>
                      </table>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="table-bordered m-t-20">
                <table class="table table-sm m-0 small">
                  <thead>
                  <tr>
                    <th colspan="1">Invoice</th>
                    <th class="text-right">Invoice</th>
                    <th class="text-right">Payment</th>
                    <th class="text-right">Adjustment</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>Total</td>
                    <td class="text-right">{{ masterInfo.invoiceFooter < 0 ? '('+numberWithCommas(Math.abs(masterInfo.invoiceFooter))+')' : numberWithCommas(masterInfo.invoiceFooter) }}</td>
                    <td class="text-right">{{ masterInfo.paymentFooter < 0 ? '('+numberWithCommas(Math.abs(masterInfo.paymentFooter))+')' : numberWithCommas(masterInfo.paymentFooter) }}</td>
                    <td class="text-right">{{ masterInfo.adjustmentFooter < 0 ? '('+numberWithCommas(Math.abs(masterInfo.adjustmentFooter))+')' : numberWithCommas(masterInfo.adjustmentFooter) }}</td>
                  </tr>
                  <tr>
                    <td>Grand Total</td>
                    <td class="text-right">{{ masterInfo.invoiceGrand < 0 ? '('+numberWithCommas(Math.abs(masterInfo.invoiceGrand))+')' : numberWithCommas(masterInfo.invoiceGrand) }}</td>
                    <td class="text-right">{{ masterInfo.paymentGrand < 0 ? '('+numberWithCommas(Math.abs(masterInfo.paymentGrand))+')' : numberWithCommas(masterInfo.paymentGrand) }}</td>
                    <td class="text-right">{{ masterInfo.adjustmentGrand < 0 ? '('+numberWithCommas(Math.abs(masterInfo.adjustmentGrand))+')' : numberWithCommas(masterInfo.adjustmentGrand) }}</td>
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
      masterInfo: {},
      customerLedgerOpening: [],
      customerLedgerDetails: [],
      customerLedgerClosing: [],
      prevBalance: 0
    }
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  mounted() {
    // bus.$on('print-ledger',(customerCode,startDate,endDate,business) => {
    //   this.getData(customerCode,startDate,endDate,business)
    // })
    let customerCode = this.$route.params.customerCode
    let startDate = this.$route.params.startDate
    let endDate = this.$route.params.endDate
    let business = this.$route.params.business
    this.getData(customerCode,startDate,endDate,business)
  },
  destroyed() {
    bus.$off('print-ledger')
  },
  methods: {
    getData(customerCode,startDate,endDate,business) {
      this.axiosPost('sdms-report/customer-ledger',{
        customerCode: customerCode,
        startDate: startDate,
        endDate: endDate,
        business: business
      },(response) => {
        try {
          this.masterInfo = response.masterInfo
          this.customerLedgerOpening = response.customer_ledger_opening
          let customerLedgerDetails = []
          response.customer_ledger_details.forEach((item) => {
            let detail = customerLedgerDetails.find((i) => {
              return i.TransNo === item.TransNo
            })
            if (detail) {
              detail.invoiceInfo.push({
                invoiceNo: item.InvoiceNo,
                invoiceAmount: item.InvoiceAmount,
                referenceNo: item.ReferenceNo,
                paidAmount: item.PaidAmount
              })
            } else {
              item.invoiceInfo = []
              item.invoiceInfo.push({
                invoiceNo: item.InvoiceNo,
                invoiceAmount: item.InvoiceAmount,
                referenceNo: item.ReferenceNo,
                paidAmount: item.PaidAmount
              })
              customerLedgerDetails.push(item)
            }
            this.customerLedgerDetails = customerLedgerDetails
          })
          console.log(this.customerLedgerDetails,"CLD")
          let openingBalance = this.customerLedgerOpening[0].Opening
          this.customerLedgerDetails.map((ledger,key) => {
            if (key === 0) {
              ledger.balance = Number(openingBalance) + Number(ledger.Amount)
              this.prevBalance = ledger.balance
              // ledger.invoiceInfo.map((l,li) => {
              //   l.balance = Number(openingBalance) + Number(ledger.Amount)
              //   this.prevBalance = l.balance
              // })
            } else {
              ledger.balance = Number(ledger.Amount) + Number(this.prevBalance)
              this.prevBalance = ledger.balance
              // ledger.invoiceInfo.map((l) => {
              //   l.balance = Number(ledger.Amount) + Number(this.prevBalance)
              //   this.prevBalance = l.balance
              // })
            }
          })
          setTimeout(() => {
            this.print()
          },500)
        } catch (e) {
          console.log(e)
        }
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