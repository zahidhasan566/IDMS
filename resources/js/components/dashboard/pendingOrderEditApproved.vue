<template>
  <div class="container-fluid">
    <breadcrumb :options="['Edit & Approved Pending Orders']">

      <router-link :to="{name: 'Dashboard'}" class="btn btn-info btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form class="form-horizontal" id="formProduction" @submit.prevent="handleSubmit(onSubmit) " @keydown.enter="$event.preventDefault()">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-2">
                          <button style="float: left;" type="button" class="btn btn-success btn-sm" @click="addRow" >Add Row</button>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <table
                              class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small"
                              id="indexed-table">
                            <thead class="thead-dark">
                            <tr>
                              <th style="width:250px">Code <span class="required-field">*</span></th>
                              <th>Product Name<span class="required-field">*</span></th>
                              <th>Vat(%)<span class="required-field">*</span></th>
                              <th>Unit Price<span class="required-field">*</span></th>
                              <th>Quantity<span class="required-field">*</span></th>
                              <th>Total Price<span class="required-field">*</span></th>
                              <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="invoice">
                            <tr v-for="(product,i) in form.products" :key="i">
                              <td>
                                <div  v-if="product.importStatus">
                                  <div v-model="product.ProductCode">{{product.PartName}}</div>
                                </div>
                                <div v-else>
                                  <v-select :filterable="false" :options="parts" label="PartName" v-model="product.ProductCode"
                                            @search="searchProduct" @input="setProduct(i)"></v-select>
                                </div>
                                <span class="error"
                                      v-if="errors[i] !== undefined && errors[i].ProductCode !== undefined">{{errors[i].ProductCode}}</span>
                              </td>
                              <td>
                                {{ product.ProductName }}
                              </td>
                              <td>
                                {{ product.VAT }}
                              </td>
                              <td>
                                {{ product.UnitPrice }}
                              </td>
                              <td class="text-center">
                                <input name="qty" id="qty"
                                       placeholder="Qty" type="number"
                                       v-model="product.Quantity"
                                       @keydown.enter="nextRow(i)"
                                       @keyup="calculate(i)"  min="1">

                              </td>
                              <td>
                                {{ product.TotalPrice }}
                              </td>
                              <td>
                                <button type="button" class="btn btn-danger btn-sm" @click="removeRow(i)"><i
                                    class="ti-close"></i></button>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="5">
                                <b>Total Amount</b>
                              </td>
                              <td class="text-right" colspan="1">
                                {{GrossTotalPrice}}

                              </td>
                              <td class="text-right" colspan="1">

                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" style="text-align: end; margin-top: 10px;">
                      <div class="form-group row mb-0">
                        <div class="col-sm-12 text-right">
                          <submit-form name="Approved"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </ValidationObserver>

              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "EvaluationReport",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      parts: [],
      isLoading: false,
      addRowStatus: 'UnitPrice',
      form:({
        products: [{
          ProductCode: '',
          ProductName: '',
          TotalPrice: 0,
          UnitPrice: 0,
          Quantity: 0,
          VAT: 0,
        }],
      }),
      errors: [],
      GrossTotalPrice: 0,
      PreLoader: false,
    }
  },
  created() {
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        let productLength = this.products.length
        if (productLength > 0) {
          if (this.products[productLength - 1].TotalPrice) {
            this.addRow();
          }
        }
      }
      if (e.key === 'Escape') {
        let productLength = this.form.products.length
        if (productLength > 0) {
          this.form.products.forEach((item) => {
            item.parts = []
          })
        }
      }
      if (e.key === 'F2') {
        e.preventDefault()
        this.onSubmit()
      }
    });
  },
  computed:{
    products() {
      return this.$store.state.products
    },
    me() {
      return this.$store.state.me
    }
  },
  mounted() {
    document.title = 'Edit & Approved| DMS';
    this.getAllPendingOrder()

  },
  methods: {
    getAllPendingOrder(){
      axios.get(baseurl + "api/dashboard/edit-approve?orderNo="+this.$route.query.orderNo, this.config()).then((response) => {
        this.form.products = response.data.data;
            response.data.data.map((row) => {
              row.TotalPrice=0
            })
      this.calculate()
      }).catch((error) => {
        this.errorNoti(error);
      })
    },
    searchProduct(val) {
      let productCode = val;
      if (productCode.length > 2) {
        axios.get(baseurl + 'api/orders/search-parts?ProductCode=' + productCode
            , this.config()).then((response) => {
          this.parts = response.data;
        }).catch((error) => {
          this.errorNoti(error);
        })
      }
    },
    setProduct(index) {
      let productCode = this.form.products[index].ProductCode.ProductCode
      this.form.products[index].ProductName = ''
      this.form.products[index].VAT = 0
      this.form.products[index].UnitPrice = 0
      this.form.products[index].Quantity = 0
      this.form.products[index].TotalPrice = 0
      this.axiosGet('orders/get-parts-by-product-code?ProductCode=' + productCode,(response) => {
        this.form.products[index].ProductCode = productCode
        this.form.products[index].ProductName = response.ProductName
        this.form.products[index].VAT = parseFloat(response.Vat).toFixed(2)
        this.form.products[index].UnitPrice = parseInt(response.UnitPrice).toFixed(2)
      },(error) => {
        this.errorNoti(error);
      })

    },
    changeProductPrice(e,index) {
      let qty =  e.target.value;
      this.form.products[index].TotalPrice = (parseInt(this.form.products[index].UnitPrice) + parseInt(this.form.products[index].VAT)) * qty
      this.form.products[index].parts = [];
    },
    calculate() {
      for(let i = 0; i < this.form.products.length; i++){
        this.form.products[i].TotalPrice = parseFloat((Number(this.form.products[i].UnitPrice) + Number(this.form.products[i].VAT)) * Number(this.form.products[i].Quantity)).toFixed(2)
      }
    this.grandTotal()
    },
    grandTotal() {
      let grandTotal = 0
      this.GrossTotalPrice = 0
      if (this.form.products.length > 0) {
        this.form.products.forEach((item) => {
          grandTotal += (parseFloat(item.UnitPrice) + parseFloat(item.VAT) ) * item.Quantity
        })

        this.GrossTotalPrice =parseFloat(grandTotal).toFixed(2)
      }
    },
    emptyProduct(e, i) {
      let $this = $(e.target)
      let currentIndex = $this.attr('data-index')
      this.form.products[i].parts = []
      if (this.form.products[i].ProductName === '') {
        $('[data-index="' + (currentIndex - 1).toString() + '"]').focus();
      }
    },
    addRow() {
      this.form.products.push({
        ProductCode: '',
        ProductName: '',
        UnitPrice: 0,
        VAT: 0,
        Quantity: 0,
        TotalPrice: 0,
        importStatus:false
      });
      setTimeout(() => {
        this.rearrangeDataIndex('indexed-table', 4)
      }, 100)
    },
    removeRow(id) {
      this.form.products.splice(id, 1)
      if (this.errors[id] !== undefined) {
        this.errors.splice(id, 1)
      }
      this.grandTotal()
    },
    nextRow(i) {
      let priceIndexVal = this.form.products[i].TotalPrice > 0;
      if ((this.form.products.length - 1 === i) || (priceIndexVal)) {
        this.addRowStatus = 'UnitPrice'
        this.addRow()
      }
    },
    onSubmit() {
      this.PreLoader = true;
      this.$store.commit('submitButtonLoadingStatus', true);
      this.axiosPost('dashboard/pending-orders/store',{
        products:this.form.products
      }, (response) => {
        if (response.status ==="success") {
          this.successNoti(response.message);
          this.$store.commit('submitButtonLoadingStatus', false);
          this.PreLoader = false;
          this.$router.push({name: 'Dashboard'})
        } else {
          this.PreLoader = false;
          this.$store.commit('submitButtonLoadingStatus', false);
          this.errorNoti(response.message);
        }
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
  color: #000000  !important;
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
@media print{
  .dealerPrint{
    color:#000000 !important;
  }


}

</style>