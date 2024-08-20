<template>
  <div>
    <div class="modal fade" id="details-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">Receive Details of <span>{{ title }}</span></div>
          </div>
          <div class="modal-body">
            <div class="datatable scrollable" style="overflow-x:auto">
              <table
                  class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm">
                <tr class="thead-dark">
                  <th>Serial</th>
                  <th>Model</th>
                  <th>Chassis No.</th>
                  <th>Engine No.</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                </tr>
                <tr v-for="(row,i) in details" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ row.ProductName }}</td>
                  <td>{{ row.ChassisNo }}</td>
                  <td>{{ row.EngineNo }}</td>
                  <td>{{ Number(row.Quantity) }}</td>
                  <td>{{ numberWithCommas(Number(row.UnitPrice)) }}</td>
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
    $('#details-modal').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('details-event', (invoiceNo) => {
      if (invoiceNo) {
        this.axiosGet('dashboard/receivable-by-id/' + invoiceNo, (response) => {
          this.details = response
          this.title = `#${invoiceNo}`
        }, function (error) {

        });
      }
      $("#details-modal").modal("toggle");
    })
  },
  destroyed() {
    bus.$off('details-event')
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosGet('user/modal', function (response) {
        instance.businessList = response.business;
        instance.departmentList = response.department;
        instance.roles = response.roles;
        instance.allSubMenu = response.allSubMenus;
        instance.filteredSubMenu = instance.checkSubMenus();
      }, function (error) {

      });
    },
    decode_utf8(s) {
      return decodeURIComponent(escape(s));
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'user/add';
      else url = 'user/update'
      this.axiosPost(url, {
        staffId: this.staffId,
        staffName: this.staffName,
        designation: this.designation,
        business: this.business.Business,
        department: this.department,
        deptCode: this.deptCode,
        email: this.email,
        mobile: this.mobile,
        status: this.status,
        userType: this.userType,
        allowedBusiness: this.selectedBusiness,
        allowedDepartment: this.selectedDepartment,
        password: this.password,
        selectedSubMenu: this.allSubMenuId
      }, (response) => {
        this.successNoti(response.message);
        $("#details-modal").modal("toggle");
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

<style scoped>
#exampleModalLabel span{
  font-weight: bold;
}
</style>
