<template>
  <div>
    <div class="modal fade" id="order-details-event" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">Order Details of <span>{{ title }}</span></div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
              Close
            </button>
          </div>
          <div class="modal-body">
            <div class="datatable scrollable" style="overflow-x:auto">
              <table
                  class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm">
                <tr class="thead-dark">
                  <th>Serial</th>
                  <th>Model</th>
                  <th>Unit Price</th>
                  <th>Vat</th>
                  <th>Quantity</th>
                  <th>TotalPrice</th>
                </tr>
                <tr v-for="(row,i) in details" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ row.ProductCode }} : {{ row.Model }}</td>
                  <td>{{ Number(row.UnitPrice) }}</td>
                  <td>{{ Number(row.Vat) }}</td>
                  <td>{{ Number(row.Quantity) }}</td>
                  <td>{{ (row.TotalPrice)}}</td>
                </tr>
              </table>
            </div>
          </div>
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
      details: [],
      invoiceNo: '',
      buttonText: 'Submit',
      buttonShow: true

    }
  },
  computed: {},
  mounted() {
    $('#order-details-event').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('order-details-event', (orderNo) => {
      console.log('sdsd',orderNo)
      if (orderNo) {
        this.axiosGet('dashboard/get-order-details/' + orderNo, (response) => {
          this.details = response.data
          this.title = `#${orderNo}`
          this.myModelclose = false;
        }, function (error) {

        });
      }
      $("#order-details-event").modal("toggle");
    })
  },
  destroyed() {
    bus.$off('order-details-event')
  },
  methods: {
    closeModal() {
      $("#order-details-event").modal("toggle");
    },
  }
}
</script>

<style scoped>
#exampleModalLabel span{
  font-weight: bold;
}
</style>
