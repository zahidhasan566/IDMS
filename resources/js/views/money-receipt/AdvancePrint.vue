<template>
  <div class="container">
    <!-- Logos -->
    <div class="header-section logo-section">
      <img src="https://prebook.ifadmotors.com/assets/images/iml-logo-mq.png" alt="IFAD Motors Ltd." class="logo-iml">
      <img src="https://prebook.ifadmotors.com/assets/images/logo-re-red.png" alt="Royal Enfield" class="logo-re">
    </div>

    <!-- Prebooking Receipt Title -->
    <div class="receipt-title">
      Advance Money Receipt
    </div>

    <!-- Company Information and Booking Info -->
    <div class="header-section">
      <div class="company-info">
        <strong>IFAD Motors Ltd.</strong><br>
        IFAD Tower, Plot 7 (new), Tejgaon Industrial Area,<br>
        Dhaka-1208 <br>
        Hotline: 16598
      </div>
      <div class="invoice-details">
        <strong>Receipt No.</strong><br>
        <small>#{{invoice.MoneyRecNo}}</small> <br>
        <small>Booking Date: {{ moment(invoice.CreatedAt).format('DD MMMM, YYYY') }}</small> <br>
      </div>
    </div>

    <!-- Receipt Issued to -->
    <div class="section-border">
      <h5>Receipt issued to:</h5>
      <div>
        <div>Name: {{ invoice.InvoiceTo }}</div>
        <div>Phone: {{ invoice.InvoicePhone }}</div>
        <div>Address: {{ invoice.InvoiceAddress }}</div>
      </div>
    </div>

    <!-- Booking Summary -->
    <div class="section-border">
      <h5>Advance Summary</h5>
      <div class="booking-summary">
        <table>
          <thead>
          <tr>
            <th>Advance Type</th>
            <th style="text-align: right;">Advance Amount (BDT)</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(inv,i) in invoices" :key="i">
            <td>{{ inv.TypeName }}</td>
            <td style="text-align: right;">{{ Number(inv.Amount) }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Additional Details -->
    <div class="details">
      <table class="table-condensed">
        <tr>
          <td>Order Status:</td>
          <td><strong>Confirmed</strong></td>
        </tr>
        <tr>
          <td>Delivery Location:</td>
          <td>Dhaka - Tejgaon Flagship Center</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import * as url from "url";
import moment from "moment"

export default {
  mixins: [Common],
  data() {
    return {
      invoice: {},
      invoices: []
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
    document.title = 'Advance Money Receipt Print | DMS';
  },
  methods: {
    getData() {
      this.axiosGet('money-receipt/'+this.$route.params.moneyRecNo,(response) => {
        this.invoice = response.invoice
        this.invoices = response.invoices
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
body {
  font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
  font-size: 10pt;
  line-height: 1.6;
  background-color: #f9f9f9;
  margin: 0;
  padding: 0;
}
.container {
  margin: 5px auto 20px auto;
  max-width: 800px;
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border: 1px solid #ddd;
  page-break-inside: avoid;
}
@media print {
  body {
    margin: 0;
  }
  .container {
    page-break-inside: avoid;
  }
}
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.logo-iml, .logo-re {
  max-height: 45px;
}
.logo-re {
  max-width: 180px;
  margin-top: 10px;
}
.company-info {
  font-size: 12px;
}
.invoice-details {
  text-align: right;
  font-size: 12px;
}
h5 {
  font-size: 16px;
  margin-top: 30px;
  border-bottom: 1px solid #000;
  padding-bottom: 5px;
}
.booking-summary table {
  width: 100%;
  margin-bottom: 20px;
}
.booking-summary th, .booking-summary td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
  color: #0b0b0b;
}
.booking-summary th {
  background: #f0f0f0;
  font-weight: bold;
}
.details {
  margin-top: 15px;
  margin-bottom: 30px;
}
.table-condensed td {
  padding: 4px 20px 4px 4px;
}
.table-condensed td:nth-child(2)::before { content: ' : '; padding-right: 3px; }
.receipt-title {
  text-align: center;
  font-size: 18px;
  font-weight: bold;
  padding: 10px;
  margin: 30px auto 40px auto;
  width: 250px;
  background-color: #f7f7f7;
  border: 1px solid #ccc;
  border-radius: 8px;
}
.section-border {
  margin-top: 15px;
  /*border-bottom: 1px solid #333;*/
  padding-bottom: 10px;
}
.print-date { display: none; }

/* Responsive adjustments */
@media only screen and (max-width: 600px) {
  .container {
    width: 100%;
    padding: 15px;
  }
  .header-section {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .company-info,
  .invoice-details {
    text-align: center;
  }
  .logo-re {
    max-width: 150px;
  }
  .logo-iml {
    margin-bottom: 10px;
  }
}
</style>