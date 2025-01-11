<template>
  <div class="content">
    <div class="container-fluid">
        <breadcrumb :options="['Order Bike ']">
          <button class="btn btn-primary btn-sm" @click="orderBikes">Add Bike</button>
          <!--      <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>-->
        </breadcrumb>
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <div class="datatable" v-if="!isLoading">
              <div class="card-body">
                <div class="d-flex">
                  <div class="card-tools">
                    <div class="row">
                      <div class="col-md-12">
                        <input v-model="query" type="text" class="form-control" placeholder="Search">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table
                      class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead class="thead-dark">
                    <tr>
                      <th>Order No</th>
                      <th>OrderDate</th>
                      <th>UserName</th>
                      <th>Product Code</th>
                      <th>Unit Price</th>
                      <th>Quantity</th>
                      <th>Vat</th>
                      <th>Total Price</th>
                      <th>Level1 Approved</th>
                      <th>Level2 Approved</th>
                      <th>Level3 Approved</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(order, i) in orders"  v-if="orders.length">
                      <td>{{ order.OrderNo }}</td>
                      <td>{{ order.OrderDate }}</td>
                      <td>{{ order.UserName }}</td>
                      <td>{{ order.ProductCode }} : {{ order.ProductName }}</td>
                      <td class="text-right">{{ order.UnitPrice }}</td>
                      <td class="text-right">{{ order.Quantity }}</td>
                      <td class="text-right">{{ order.Vat }}</td>
                      <td class="text-right">{{ order.TotalPrice }}</td>
                      <td class="text-right">{{ order.Level1Approved }}</td>
                      <td class="text-right">{{ order.Level2Approved }}</td>
                      <td class="text-right">{{ order.Level3Approved }}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="data-count">
                      Show {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} rows
                    </div>
                  </div>
                  <div class="col-8">
                    <pagination
                        v-if="pagination.last_page > 1"
                        :pagination="pagination"
                        :offset="5"
                        @paginate="query === '' ? getAllOrderBikeList() : getAllOrderBikeList()"></pagination>
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <skeleton-loader :row="14"/>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" data-backdrop="static" id="orderBikesModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ editMode ? "Edit" : "Add" }} Bike
            </div>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" @click="closeModal">Close
            </button>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
          <form class="form-horizontal" id="formProduction"  @submit.prevent="handleSubmit(onSubmit) " @keydown.enter="$event.preventDefault()">
            <div class="card">
              <div class="modal-body">
            <div class="row">
              <div class="col-md-2">
                <button style="float: left;" type="button" class="btn btn-success btn-sm" @click="addRow" >Add
                  Row
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small" id="indexed-table">
                  <thead class="thead-dark">
                  <tr>
                    <th>Code <span class="required-field">*</span></th>
                    <th>Product Name<span class="required-field">*</span></th>
                    <th>Vat(15%)<span class="required-field">*</span></th>
                    <th>Unit Price<span class="required-field">*</span></th>
                    <th>Per Unit TP<span class="required-field">*</span></th>
                    <th>Quantity<span class="required-field">*</span></th>
                    <th>Total Price<span class="required-field">*</span></th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody id="invoice">
                  <tr v-for="(product,i) in form.products" :key="i">
                    <td>

                      <v-select :filterable="false" :options="parts" label="BikeName" v-model="product.ProductCode"
                                @click="searchProduct" @input="setProduct(i)"></v-select>
                    </td>
                    <td>
                      {{ product.ProductName }}
                    </td>
                    <td>
                      {{ product.Vat }}
                    </td>
                    <td>
                      {{ product.UnitPrice }}
                    </td>
                      <td>

                          {{ (parseFloat(product.Vat)  +  parseFloat(product.UnitPrice)).toFixed(2)}}
                    </td>
                    <td class="text-center">
                        <input type="number" id="qty"
                               placeholder="Qty"
                               v-model="product.Quantity"
                               @input="calculate(i)" min="1" step="1">

                    </td>
                    <td>
                      {{ product.TotalPrice }}
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm" @click="removeProductRow(i)"><i
                          class="ti-close"></i></button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6">
                      <b>Total Amount</b>
                    </td>
                    <td class="text-right" colspan="1">
                      {{GrossTotalPrice.toFixed(2)}}

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
                    <submit-form name="Create"/>
                  </div>
                </div>
              </div>
            </div>
          </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";

export default {
  mixins: [Common],
  data() {
    return {
      productModels:[],
      parts: [],
      orders:[],
      pagination: {
        current_page: 1,
        last_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      query: "",
      priceGrid: false,
      editMode: false,
      isLoading: false,
      addRowStatus: 'UnitPrice',
      form:({
        products: [{
          ProductCode: '',
          ProductName: '',
          TotalPrice: 0,
          UnitPrice: 0,
          Quantity: 0,
          Vat: 0,
        }],
        Product: [],
      }),
      errors: [],
      buttonShow: false,
      GrossTotalPrice: 0,
      customerMaxIndex: 5000,
      isMenuDisabled: false,
      loaded: false,
      PreLoader: false,
    }
  },
  watch: {
    query: function (newQ, old) {
      this.getAllOrderBikeList();
    }
  },
  created() {
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        let productLength = this.form.products.length
        if (productLength > 0) {
          if (this.form.products[productLength - 1].TotalPrice) {
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
  computed: {
    products() {
      return this.$store.state.products
    },
    me() {
      return this.$store.state.me
    }
  },
  mounted() {
    document.title = 'Order Bike| DMS';
    this.getAllOrderBikeList();
    this.searchProduct();
  },
  methods: {
    getAllOrderBikeList() {
      axios.get(baseurl + 'api/orders/bike-list?page=' + this.pagination.current_page
          + "&query=" + this.query
          , this.config()).then((response) => {
        if (response.data.data.data.length>0){
          this.orders = response.data.data.data;
          this.pagination.current_page = response.data.data.current_page;
          this.pagination.from = response.data.data.from;
          this.pagination.to = response.data.data.to;
          this.pagination.total = response.data.data.total;
          this.pagination.last_page = response.data.data.last_page;
        } else {
          this.errorNoti(error);
        }
      }).catch((e) => {
        this.isLoading = false;
      })
    },

    searchProduct(val) {
      let productCode = val;
      axios.get(baseurl + 'api/orders/search-product?ProductCode=' + productCode, this.config()).then((response) => {
        this.parts = response.data;
      }).catch((error) => {

      })
    },
    setProduct(index) {
      if (this.form.products[index].ProductCode !== null && this.form.products[index].ProductCode.ProductCode !== undefined) {
        let productCode = this.form.products[index].ProductCode.ProductCode
        this.form.products[index].ProductName = ''
        this.form.products[index].Vat = 0
        this.form.products[index].UnitPrice = 0
        this.form.products[index].Quantity = 0
        this.form.products[index].TotalPrice = 0
        this.axiosGet('orders/get-bike-by-product-code?ProductCode=' + productCode,(response) => {
          this.form.products[index].ProductName = response.ProductName
          this.form.products[index].Vat = parseFloat(response.Vat)
          this.form.products[index].UnitPrice = parseFloat(response.UnitPrice)
          this.grandTotal();
        },(error) => {
          this.errorNoti(error);
        })
        this.grandTotal();
      }else {
        this.form.products[index].ProductCode = ''
        this.form.products[index].ProductName = ''
        this.form.products[index].Vat = 0
        this.form.products[index].UnitPrice = 0
        this.form.products[index].Quantity = 0
        this.form.products[index].TotalPrice = 0
        this.grandTotal();
      }

    },

    // searchProduct() {
    //     axios.get(baseurl + 'api/orders/search-product'
    //         , this.config()).then((response) => {
    //       this.parts = response.data;
    //     }).catch((error) => {
    //
    //     })
    //
    // },
    // setProduct(index) {
    //   let productCode = this.form.products[index].ProductCode.ProductCode
    //   this.form.products[index].ProductName = ''
    //   this.form.products[index].Vat = 0
    //   this.form.products[index].UnitPrice = 0
    //   this.form.products[index].Quantity = 0
    //   this.form.products[index].TotalPrice = 0
    //   this.axiosGet('orders/get-bike-by-product-code?ProductCode=' + productCode,(response) => {
    //     this.form.products[index].ProductName = response.ProductName
    //     this.form.products[index].Vat = parseInt(response.Vat)
    //     this.form.products[index].UnitPrice = parseInt(response.UnitPrice)
    //     this.grandTotal();
    //   },(error) => {
    //     this.errorNoti(error);
    //   })
    // },
    changeProductPrice(e,index) {
      let qty =  e.target.value;
      this.form.products[index].TotalPrice = (parseInt(this.form.products[index].UnitPrice) + parseInt(this.form.products[index].Vat)) * qty
       this.form.products[index].parts = [];
    },
    emptyProduct(e, i) {
      let $this = $(e.target)
      let currentIndex = $this.attr('data-index')
      this.form.products[i].parts = []
      if (this.form.products[i].ProductName === '') {
        $('[data-index="' + (currentIndex - 1).toString() + '"]').focus();
      }
    },
    orderBikes() {
      this.editMode = false;
      this.isMenuDisabled = false;
      $('#orderBikesModal').modal({backdrop: 'static', keyboard: false});
      $("#orderBikesModal").modal("show");
      //Focus First Element

    },
    closeModal() {
      $("#orderBikesModal").modal("hide");
      this.form.products = []
      this.addRow()
      this.grandTotal();
      this.buttonStatus = false
      this.getAllOrderBikeList();
    },
    calculate(i) {
      this.form.products[i].TotalPrice = (parseFloat(this.form.products[i].UnitPrice) + parseFloat(this.form.products[i].Vat)) * Number(this.form.products[i].Quantity)
      this.grandTotal()
    },
    grandTotal() {
      let grandTotal = 0
      this.GrossTotalPrice = 0
      if (this.form.products.length > 0) {
        this.form.products.forEach((item) => {
          grandTotal += (item.UnitPrice + item.Vat) * item.Quantity
        })
        this.GrossTotalPrice = grandTotal
      }
    },
    nextRow(index) {
        this.addRow()
    },
    addRow() {
      this.form.products.push({
        ProductCode: '',
        ProductName: '',
        TotalPrice: 0,
        UnitPrice: 0,
        Quantity: 0,
        Vat: 0,
      });
      // setTimeout(() => {
      //   this.rearrangeDataIndex('indexed-table', 4)
      // }, 100)
    },
    onSubmit() {
      this.PreLoader = true;
      this.$store.commit('submitButtonLoadingStatus', true);
        this.axiosPost('orders/store-bike',{
          products:this.form.products
        }, (response) => {
          if (response.status === 'success') {
            this.PreLoader = false;
            this.successNoti(response.message);
            this.$store.commit('submitButtonLoadingStatus', false);
            this.closeModal();
            this.getAllOrderBikeList()
          } else {
            this.PreLoader = false;
            this.$store.commit('submitButtonLoadingStatus', false);
            this.errorNoti(response.message);
          }
        })
    },
    removeProductRow(id) {
      this.form.products.splice(id, 1)
      if (this.errors[id] !== undefined) {
        this.errors.splice(id, 1)
      }
      this.grandTotal()
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
  color: #4d79f6b5  !important;
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
</style>