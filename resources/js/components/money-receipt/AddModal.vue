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
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Customer Name" mode="eager" rules="required|max:150" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="customerName">Customer Name<span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="customerName"
                               v-model="invoiceTo" placeholder="Customer Name">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Customer Phone" mode="eager" rules="required|max:18" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="customerPhone">Customer Phone <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="customerPhone"
                               v-model="invoicePhone" placeholder="Customer Phone">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-12">
                    <ValidationProvider name="Customer Address" mode="eager" rules="required|max:200" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="customerAddress">Customer Address <span class="error">*</span></label>
                        <textarea class="form-control" v-model="invoiceAddress" id="customerAddress" cols="30" rows="3" :class="{'error-border': errors[0]}"></textarea>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Engine No" mode="eager" rules="max:20" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="engineNo">Engine No. </label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="engineNo"
                               v-model="engineNo" placeholder="Engine No">
                      </div>
                      <span class="error-message"> {{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Frame No" mode="eager" rules="max:50" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="frameNo">Frame No. </label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="frameNo"
                               v-model="frameNo" placeholder="Frame No">
                      </div>
                      <span class="error-message"> {{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-12">
                    <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm">
                      <tr class="thead-dark">
                        <th>Advance Type</th>
                        <th>Advance Amount</th>
                      </tr>
                      <tbody>
                      <tr v-for="(t,ti) in types" :key="ti">
                        <td>{{ t.TypeName }}</td>
                        <td>
                          <input type="number" class="form-control amount" v-model="t.Amount" min="0">
                        </td>
                      </tr>
                      </tbody>
                    </table>
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
  props: ['types'],
  data() {
    return {
      title: '',
      invoiceTo: '',
      invoicePhone: '',
      invoiceAddress: '',
      engineNo: '',
      frameNo: '',
      fields: [],
      buttonText: '',
      type: 'add',
      actionType: 'add',
      buttonShow: true
    }
  },
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('advance-money-receipt-event', () => {
      this.title = 'Create Advance Money Receipt';
      this.buttonText = "Create";
      $("#add-edit-dept").modal("toggle");
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('advance-money-receipt-event')
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosGet('roles/all',function (response) {
        instance.roles = response.data;
      },function (error) {

      });
    },
    onSubmit() {
      const total = this.types.reduce((sum, item) => sum + Number(item.Amount), 0);
      if (total > 0) {
        this.$store.commit('submitButtonLoadingStatus', true);
        let url = 'money-receipt/create-payment';
        this.axiosPost(url, {
          invoiceTo: this.invoiceTo,
          invoicePhone: this.invoicePhone,
          invoiceAddress: this.invoiceAddress,
          engineNo: this.engineNo,
          frameNo: this.frameNo,
          amount: total,
          types: this.types
        }, (response) => {
          this.successNoti(response.message);
          $("#add-edit-dept").modal("toggle");
          bus.$emit('refresh-datatable');
          this.$store.commit('submitButtonLoadingStatus', false);
        }, (error) => {
          this.errorNoti(error);
          this.$store.commit('submitButtonLoadingStatus', false);
        })
      } else {
        this.errorNoti('Invoice amount must be greater than 0.')
      }
    }
  }
}
</script>

<style scoped>
.amount {
  text-align: right;
}
</style>
