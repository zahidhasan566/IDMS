<template>
  <div class="container-fluid" id="spare-parts">
    <breadcrumb :options="['Spare Parts Return Invoice']"></breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" id="return_form">
            <ValidationObserver v-slot="{ handleSubmit }">
              <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                <ValidationProvider name="Invoice No" mode="eager" rules="required" v-slot="{ errors }">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <p>Enter the Invoice number and get the Invoice records below.</p>
                        <label for="invoiceNo">Invoice Number</label>
                        <input type="text" class="form-control" id="invoiceNo" v-model="invoiceNo">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </div>
                  </div>
                </ValidationProvider>
                <div class="row m-t-5">
                  <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                  </div>
                </div>
              </form>
            </ValidationObserver>
            <br>
          </div>
        </div>
      </div>
    </div>
    <div v-if="invoiceDetails.length > 0">
      <ValidationObserver v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(onSubmitReturn)" @keydown.enter="$event.preventDefault()">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <table class="table thead-dark">
                  <tr class="thead-dark">
                    <th>Sl. No</th>
                    <th>Particulars</th>
                    <th>BDT</th>
                    <th>Sold Qty</th>
                    <th>Return Qty</th>
                    <th>Discount(%)</th>
                    <th>Discount Amount</th>
                    <th>VAT</th>
                    <th>Total</th>
                  </tr>
                  <tr v-for="(invD,i) in invoiceDetails" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td>({{ invD.ProductCode }}) {{ invD.ProductName }}</td>
                    <td>{{ invD.UnitPrice }}</td>
                    <td>{{ invD.Quantity }}</td>
                    <td>
                      <ValidationProvider name="Quantity" mode="eager"
                                          :rules="`required|min_value:0|max_value:${invD.PastQuantity}`"
                                          v-slot="{ errors }">
                        <input type="number" class="form-control" v-model="invD.rQuantity"
                               @keyup="changeQuantityOrDiscount(i)">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </ValidationProvider>
                    </td>
                    <td>
                      <ValidationProvider name="Discount" mode="eager"
                                          :rules="`required|min_value:0|max_value:${invD.PastDiscount}`"
                                          v-slot="{ errors }">
                        <input type="number" class="form-control" v-model="invD.Discount"
                               @keyup="changeQuantityOrDiscount(i)">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </ValidationProvider>
                    </td>
                    <td>
                      {{ invD.DiscountAmount }}
                    </td>
                    <td>{{ Number(invD.VAT).toFixed(2) }}</td>
                    <td>{{ Number(invD.TotalAmount).toFixed(2) }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary">Confirm Return</button>
            </div>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url';
import {Common} from "../../mixins/common";
import moment from "moment";

export default {
  name: "Invoice",
  mixins: [Common],
  data() {
    return {
      fields: [],
      invoiceNo: '',
      invoiceMaster: '',
      invoiceDetails: []
    }
  },
  created() {
    //
  },
  mounted() {
    document.title = 'Spare Parts Invoice Return | DMS';
  },
  methods: {
    changeQuantityOrDiscount(i) {
      let discountAmount = (Number(this.invoiceDetails[i].UnitPrice) * (Number(this.invoiceDetails[i].Quantity) - Number(this.invoiceDetails[i].rQuantity))) * (Number(this.invoiceDetails[i].Discount) / 100)
      let price = Number(this.invoiceDetails[i].UnitPrice) * (Number(this.invoiceDetails[i].Quantity) - Number(this.invoiceDetails[i].rQuantity))
      console.log(discountAmount, price)
      this.invoiceDetails[i].DiscountAmount = discountAmount
      this.invoiceDetails[i].TotalAmount = price - discountAmount
    },
    onSubmit() {
      this.axiosPost('invoice-spare-parts/get-data-by-id', {
        invoiceNo: this.invoiceNo
      }, (response) => {
        this.invoiceMaster = response.invoiceMaster
        this.invoiceDetails = response.invoiceDetails
      }, (error) => {

      })
    },
    onSubmitReturn() {
      if (this.invoiceDetails.length > 0) {
        this.axiosPost('invoice-spare-parts/return', {
          invoiceDetails: this.invoiceDetails,
          invoiceNo: this.invoiceMaster.InvoiceNo
        }, (response) => {
          this.fields = []
          this.invoiceNo = ''
          this.invoiceMaster = ''
          this.invoiceDetails = []
          this.successNoti(response.message)
        }, (error) => {
          this.errorNoti(error.response.data.message)
        })
      }
    }
  }
}
</script>

<style scoped>
#spare-parts #quantity {
  height: 50px;
  font-size: 20px;
  margin-left: -10px;
  margin-right: -10px;
  z-index: 0;
  text-align: center;
}

#return_form .form-control {
  font-size: 10px;
  height: 29px;
}

#return_form .form-group {
  margin-bottom: 0;
}

#return_form label {
  font-size: 11px !important;
  margin-bottom: 0 !important;
  margin-top: 10px !important;
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

#invoice2 .auto-complete2 ul li {
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}

#invoice2 .auto-complete2 ul li a {
  color: #000000;
}

#invoice2 .auto-complete2 ul li:hover {
  background: #fff3cd;
}

#invoice2 :focus {
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
  color: #4d79f6b5 !important;
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

</style>