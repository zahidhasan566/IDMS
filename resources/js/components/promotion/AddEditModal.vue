<template>
  <div>
    <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-12">
                    <ValidationProvider name="Promo Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="promoName">Promo Name <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="promoName" placeholder="Promo Name" id="promoName">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Start Date" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="startDate">Start Date <span class="error">*</span></label>
                        <datepicker v-model="startDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="End Date" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="endDate">End Date <span class="error">*</span></label>
                        <datepicker v-model="endDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Sales Quantity" mode="eager" rules="required|min_value:0" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="quantity">Sales Quantity </label>
                        <input type="number" class="form-control" min="0" v-model="quantity" id="quantity" placeholder="Quantity">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Amount" mode="eager" rules="required|min_value:0" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="amount">Amount </label>
                        <input type="number" class="form-control" min="0" v-model="amount" id="amount" placeholder="Amount">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Brand" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="brand">Select Brand </label>
                        <select class="form-control" id="brand" v-model="brand" @change="loadProducts">
                          <option value="">Select</option>
                          <option :value="b.BrandCode" v-for="(b,i) in brands" :key="i">{{ b.BrandName }}</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Product" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="product">Select Product </label>
                        <v-select :filterable="true" :options="products" label="title" v-model="product" :multiple="true"></v-select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <submit-form v-if="buttonShow" :name="buttonText"/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import {mapGetters} from "vuex";

export default {
  mixins: [Common],
  data() {
    return {
      title: '',
      promoId: '',
      promoName: '',
      startDate: '',
      endDate: '',
      quantity: 0,
      amount: 0,
      brands: [],
      brand: '',
      products: [],
      product: [],
      buttonText: '',
      actionType: '',
      buttonShow: true,
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    }
  },
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-user', (row) => {
      if (row) {
        let instance = this;
        this.axiosGet('product-promotion/get-promotion-info/'+row.PromoId,(response) =>{
          let promo = response.data;
          instance.title = 'Update Promotion';
          instance.buttonText = "Update";
          instance.promoId = row.PromoId;
          instance.promoName = promo.PromoName;
          instance.startDate = promo.PromoStartDate;
          instance.endDate = promo.PromoEndDate;
          instance.quantity = promo.SalesQnty;
          instance.amount = promo.Amount;
          instance.brand = promo.BrandCode;
          instance.buttonShow = true;
          instance.actionType = 'edit';
          instance.product = []
          promo.details.forEach((item) => {
            instance.product.push({
              id: item.ProductCode,
              title: item.ProductName
            })
          })
        },(error) => {

        });
      } else {
        this.title = 'Add Promotion';
        this.promoId = '';
        this.promoName = '';
        this.startDate = '';
        this.endDate = '';
        this.quantity = '';
        this.amount = '';
        this.brand = '';
        this.buttonText = "Add";
        this.actionType = 'add'
      }
      $("#add-edit-dept").modal("toggle");
      this.getData();
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
  },
  methods: {
    getData() {
      this.axiosGet('product-promotion/support-data',(response) => {
        this.brands = response.data
      },(error) => {

      })
    },
    loadProducts() {
      let data = []
      this.axiosGet('product-promotion/get-product-by-brand/'+this.brand,(response) => {
        response.data.forEach((item) => {
          data.push({
            id: item.ProductCode,
            title: item.ProductName
          })
        })
        this.products = data
        this.product = data
      })
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'product-promotion/create';
      else url = 'product-promotion/update/'+this.promoId
      this.axiosPost(url, {
        promoId: this.promoId,
        promoName: this.promoName,
        startDate: this.startDate,
        endDate: this.endDate,
        quantity: this.quantity,
        amount: this.amount,
        brand: this.brand,
        product: this.product
      }, (response) => {
        this.successNoti(response.message);
        $("#add-edit-dept").modal("toggle");
        bus.$emit('refresh-datatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })
    }
  }
}
</script>