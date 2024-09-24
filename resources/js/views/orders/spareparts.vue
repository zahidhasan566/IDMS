<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Order Spare Parts ']">
        <button class="btn btn-primary btn-sm" @click="orderSpareParts">Add Spare Parts</button>
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
                      <th>Quantity</th>
                      <th>Unit Price</th>
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
                      <td class="text-right">{{ order.Quantity }}</td>
                      <td class="text-right">{{ order.UnitPrice }}</td>
                      <td class="text-right">{{ order.VAT }}</td>
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
                        @paginate="query === ''? getAllSpareParts() : getAllSpareParts()"
                    ></pagination>
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
    <div class="modal fade" data-backdrop="static" id="orderSparePartsModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ editMode ? "Edit" : "Add" }} Spare Parts
            </div>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" @click="closeModal">Close
            </button>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="formProduction" @submit.prevent="handleSubmit(onSubmit) " @keydown.enter="$event.preventDefault()">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-2">
                      <button style="float: left;" type="button" class="btn btn-success btn-sm" @click="addRow" >Add Row</button>
                    </div>
                    <div class="col-md-10" v-if="!editMode">
                      <form action="">
                        <button type="button" style="background-color: #4d79f6;float: right" class="btn btn-info btn-sm" @click="importAllParts">Import {{ module }}</button>
                        <input type="file" style="float: right;height: 27px!important;margin-right: 3px" @change="readExcelFile($event)" class="btn btn-info btn-sm">
                      </form>
                      <a href="#" style="float: right;margin-right: 5px" @click="downloadDemoExcel" class="btn btn-success btn-sm">Download Sample</a>
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
                          <th>Part No<span class="required-field">*</span></th>
                          <th>Vat(%)<span class="required-field">*</span></th>
                          <th>Unit Price<span class="required-field">*</span></th>
                          <th>Current Stock<span class="required-field">*</span></th>
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
                            {{ product.PartNo }}
                          </td>
                          <td>
                            {{ product.Vat }}
                          </td>
                          <td>
                            {{ product.UnitPrice }}
                          </td>
                          <td>
                            {{ product.CurrentStock }}
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
                          <td colspan="7">
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
    <data-export/>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";
import XLSX from "xlsx";
import { saveAs } from "file-saver";
import {saveExcel} from "@progress/kendo-vue-excel-export";

export default {
  mixins: [Common],
  data() {
    return {
      orders:[],
      parts: [],
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
          PartsCode: '',
          ProductCode: '',
          ProductName: '',
          TotalPrice: 0,
          UnitPrice: 0,
          Quantity: 0,
          Vat: 0,
          importStatus:false,
        }],
      }),
      errors: [],
      buttonShow: false,
      buttonStatus: false,
      module: 'Spare Parts',
      GrossTotalPrice: 0,
      increment1: 0,
      increment2: 1,
      customerMaxIndex: 5000,
      isMenuDisabled: false,
      loaded: false,
      PreLoader: false,
    }
  },
  watch: {
    query: function (newQ, old) {
        this.getAllSpareParts();
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
  computed: {
    products() {
      return this.$store.state.products
    },
    me() {
      return this.$store.state.me
    }
  },
  mounted() {
    document.title = 'Order Spare Parts | DMS';
    this.getAllSpareParts();
  },
  methods: {
    getAllSpareParts() {
      axios.get(baseurl + 'api/orders/spare-parts-list?page=' + this.pagination.current_page
          + "&query=" + this.query , this.config()).then((response) => {
        this.orders = response.data.data.data;
        this.pagination.current_page = response.data.data.current_page;
        this.pagination.from = response.data.data.from;
        this.pagination.to = response.data.data.to;
        this.pagination.total = response.data.data.total;
        this.pagination.last_page = response.data.data.last_page;
      }).catch((errors) => {

      })
    },
    searchProduct(val) {
      let productCode = val;
      if (productCode.length > 2) {
        axios.get(baseurl + 'api/orders/search-parts?ProductCode=' + productCode
            , this.config()).then((response) => {
          this.parts = response.data;
        }).catch((error) => {

        })
      }
    },
    setProduct(index) {
      let productCode = this.form.products[index].ProductCode.ProductCode
      this.form.products[index].ProductName = ''
      this.form.products[index].PartNo = ''
      this.form.products[index].Vat = 0
      this.form.products[index].UnitPrice = 0
      this.form.products[index].Quantity = 0
      this.form.products[index].CurrentStock = 0
      this.form.products[index].TotalPrice = 0
      this.axiosGet('orders/get-parts-by-product-code?ProductCode=' + productCode,(response) => {
        this.form.products[index].ProductCode = productCode
        this.form.products[index].ProductName = response.ProductName
        this.form.products[index].PartNo = response.PartNo
        this.form.products[index].Vat = parseFloat(response.Vat)
        this.form.products[index].UnitPrice = parseInt(response.UnitPrice)
        this.form.products[index].CurrentStock = parseInt(response.CurrentStock)
      },(error) => {
        this.errorNoti(error);
      })

    },
    changeProductPrice(e,index) {
      let qty =  e.target.value;
      this.form.products[index].TotalPrice = (parseInt(this.form.products[index].UnitPrice) + parseInt(this.form.products[index].Vat)) * qty
      this.form.products[index].parts = [];
    },
    calculate(i) {
      this.form.products[i].TotalPrice = (Number(this.form.products[i].UnitPrice) + Number(this.form.products[i].Vat)) * Number(this.form.products[i].Quantity)
      this.grandTotal()
    },
    grandTotal() {
      let grandTotal = 0
      this.GrossTotalPrice = 0
      if (this.form.products.length > 0) {
        this.form.products.forEach((item) => {
          grandTotal += (parseFloat(item.UnitPrice) + parseFloat(item.Vat) ) * item.Quantity
        })

        this.GrossTotalPrice =parseInt(grandTotal)
      }
      // console.log(parseFloat(this.GrossTotalPrice))
    },
    emptyProduct(e, i) {
      let $this = $(e.target)
      let currentIndex = $this.attr('data-index')
      this.form.products[i].parts = []
      if (this.form.products[i].ProductName === '') {
        $('[data-index="' + (currentIndex - 1).toString() + '"]').focus();
      }
    },
    orderSpareParts() {
      this.editMode = false;
      this.isMenuDisabled = false;
      $('#orderSparePartsModal').modal({backdrop: 'static', keyboard: false});
      $("#orderSparePartsModal").modal("show");
      //Focus First Element

    },
    closeModal() {
      $("#orderSparePartsModal").modal("hide");
      this.form.products = []
      this.addRow()
      this.grandTotal();
      this.buttonStatus = false
    },
    addRow() {
      this.form.products.push({
        PartsCode: '',
        ProductCode: '',
        ProductName: '',
        PartNo: '',
        UnitPrice: 0,
        Vat: 0,
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
      this.axiosPost('orders/store-parts',{
        products:this.form.products
      }, (response) => {
        if (response.status === 'success') {
          this.successNoti(response.message);
          this.$store.commit('submitButtonLoadingStatus', false);
          this.PreLoader = false;
          this.closeModal();
          this.getAllSpareParts();
        } else {
          this.PreLoader = false;
          this.$store.commit('submitButtonLoadingStatus', false);
          this.errorNoti(response.message);
        }
      })
    },
    downloadDemoExcel() {
      axios.get(baseurl + 'api/orders/export-order-excel',this.config()).then((response) => {
        let dataSets = response.data.data;
          var reportTitle = []
              if (dataSets.length > 0) {
                    let columns = Object.keys(dataSets[0]);
                    columns = columns.filter((item) => item !== 'row_num');

                    let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
                    columns = columns.map((item) => {
                       reportTitle.push(item.replace(rex, '$1$4 $2$3$5'))
                      // return {title, key: item}
              })
              }
          reportTitle.push('Total')
            // Create a workbook and a worksheet
            const wb = XLSX.utils.book_new();
            const ws_data = [reportTitle];


            // Populate worksheet data with dynamic Stock
          dataSets.forEach((singleData, index) => {
                ws_data.push([
                    singleData.ProductCode,
                    singleData.ProductName,
                    singleData.PartNo,
                    singleData.UnitPrice,
                    singleData.Vat,
                    singleData.Quantity,
                    singleData.CurrentStock,
                    { f: `(D${index + 2}+E${index + 2})*F${index + 2}` }  // Adding formula for each student
                ]);
            });

            const ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Append worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // Create a binary string
            const wbout = XLSX.write(wb, { bookType: "xlsx", type: "binary" });

            // Convert binary string to array buffer
            function s2ab(s) {
                const buf = new ArrayBuffer(s.length);
                const view = new Uint8Array(buf);
                for (let i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;
                return buf;
            }

            // Save file
            saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), "order-table.xlsx");
          // bus.$emit('data-table-import','order-table'+moment().format('YYYY-MM-DD'))

        }).catch((errors) => {
        //
      })
    },
    readExcelFile(e) {
      var files = e.target.files, f = files[0];
      var reader = new FileReader();
      reader.onload = (e) => {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, {type: 'array'});
        let sheetName = workbook.SheetNames[0]
        let worksheet = workbook.Sheets[sheetName];
        this.form.Product = XLSX.utils.sheet_to_json(worksheet);
      };
      reader.readAsArrayBuffer(f);
    },
    importAllParts() {
          this.axiosPost('orders/spare-parts-import-data',{
            products:this.form.Product
          }, (response) => {
          let data = this.form.products;
          if (response.products.length > 0) {
            this.form.products.splice(0, 1)
            this.form.products = data.concat(response.products)
            this.grandTotal();
          }
        });
    },
  }

}

</script>

<style scoped>
@media (min-width: 1200px) {
  .modal-xl {
    max-width: 1400px;
  }
}
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