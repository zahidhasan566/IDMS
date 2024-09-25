<template>
  <div>
    <div class="modal fade" id="damage-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">Receive Details of <span>{{ title }}</span>
            </div>
          </div>
          <div class="modal-body">
            <ValidationObserver v-slot="{ handleSubmit }">
              <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)">
                <div class="datatable scrollable" style="overflow-x:auto">
                  <table
                      class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm">
                    <tr class="thead-dark">
                      <th>Serial</th>
                      <th>Model</th>
                      <th>Chassis No.</th>
                      <th>Engine No.</th>
                      <th>Quantity</th>
                      <th>Receive Quantity</th>
                      <th>Damaged Quantity</th>
                      <th>Damaged Product Image</th>
                      <th>Unit Price</th>
                    </tr>
                    <tr v-for="(row,i) in details" :key="i">
                      <td>{{ i + 1 }}</td>
                      <td>{{ row.ProductName }}</td>
                      <td>{{ row.ChassisNo }}</td>
                      <td>{{ row.EngineNo }}</td>
                      <td>{{ Number(row.Quantity) }}</td>
                      <td>
                        <ValidationProvider :name="`Receive Quantity-${i}th row`" mode="eager"
                                            :rules="`required|max_value:${Math.abs(row.Quantity)}`"
                                            v-slot="{ errors }">
                          <input type="text" class="form-control" v-model="row.ReceiveQty">
                          <span class="error-message"> {{ errors[0] }}</span>
                        </ValidationProvider>
                      </td>
                      <td>
                        <ValidationProvider :name="`Damage Quantity-${i}th row`" mode="eager"
                                            :rules="`required|max_value:${Math.abs(row.Quantity)}`"
                                            v-slot="{ errors }">
                          <input type="text" class="form-control" v-model="row.DamagedQty">
                          <span class="error-message"> {{ errors[0] }}</span>
                        </ValidationProvider>
                      </td>
                      <td>
                        <template v-if="row.DamagedQty > 0">
<!--                          <ValidationProvider :name="`Image-${i}th row`" mode="eager"-->
<!--                                              rules="required"-->
<!--                                              v-slot="{ errors }">-->
<!--                            <input type="file" @change="fileUpload($event,i)">-->
<!--                            <br>-->
<!--                            <span class="error-message"> {{ errors[0] }}</span>-->
<!--                          </ValidationProvider>-->
                          <input type="file" @change="fileUpload($event,i)">
                          <br>
                        </template>
                        <template v-else>
                          <p>Attach Damage Product's Image</p>
                        </template>
                      </td>
                      <td>{{ numberWithCommas(Number(row.UnitPrice)) }}</td>
                    </tr>
                  </table>
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
    $('#damage-modal').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('damage-event', (invoiceNo) => {
      if (invoiceNo) {
        this.axiosGet('dashboard/receivable-by-id/' + invoiceNo, (response) => {
          response.map((d) => {
            d.ReceiveQty = Number(d.Quantity)
            d.DamagedQty = 0
            d.DamagedImage = ''
          })
          this.details = response
          this.title = `#${invoiceNo}`
          this.invoiceNo = invoiceNo
        }, function (error) {

        });
      }
      $("#damage-modal").modal("toggle");
    })
  },
  destroyed() {
    bus.$off('damage-event')
  },
  methods: {
    onSubmit() {
      if (this.checkValue(this.details)) {
        this.$store.commit('submitButtonLoadingStatus', true);
        this.axiosPost('dashboard/receivables/store', {
          details: this.details,
          invoiceNo: this.invoiceNo
        }, (response) => {
          this.successNoti(response.message);
          $("#damage-modal").modal("toggle");
          bus.$emit('refresh-datatable');
          this.$store.commit('submitButtonLoadingStatus', false);
        }, (error) => {
          this.$store.commit('submitButtonLoadingStatus', false);
          this.infoFailed('Failed!', error.data.response.message)
        })
      } else {
        this.errorNoti('No value found!')
      }
    },
    checkValue(details) {
      let value = 0
      if (details.length > 0) {
        details.forEach((item) => {
          value  = value + Number(item.ReceiveQty) + Number(item.DamagedQty)
        })
      }
      return value > 0;
    },
    fileUpload(e,i) {
      let input = e.target
      let file = input.files[0]
      console.log(file)
      if (file.size > 5000000) {
        this.errorNoti('Maximum file size 5 MB for Event')
      } else {
        this.processImage(file,i)
      }
    },
    processImage(file,i) {
      let instance = this
      let reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function () {
        instance.details[i].DamagedImage = reader.result
      };
      reader.onerror = function (error) {
        console.log('Error: ', error);
      };
    }
  }
}
</script>

<style scoped>
#exampleModalLabel span {
  font-weight: bold;
}
</style>
