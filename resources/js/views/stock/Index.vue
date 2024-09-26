<template>
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab"  href="#bike" role="tab" @click="openTab('bike')">
              <span> <i class="ti-hand-drag"></i>Bike</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#parts" role="tab" @click="openTab('parts')">
              <span> <i class="ti-home"></i>Spare Parts</span>
            </a>
          </li>

        </ul>
        <div class="tab-content">
          <div class="tab-pane active p-3" id="bike" role="tabpanel">
            <Bike v-if="tabProps.bike"/>
          </div>

          <div class="tab-pane  p-3" id="parts" role="tabpanel">
            <Parts v-if="tabProps.parts"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../mixins/common";
import Bike from "../../components/stock/BikeStock.vue"
import Parts from "../../components/stock/PartsStock.vue";

export default {
  mixins: [Common],
  components: {Bike,Parts},
  data() {
    return {
      tabProps: {
        bike: true,
        parts: false
      }
    }
  },
  created() {
  },
  mounted() {

  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  methods: {
    allTables() {
      this.tabProps = {
        bike: false,
        parts: false,
      }
    },
    openTab(page) {
      this.allTables()
      if (page === 'bike') this.tabProps.bike = true
      if (page === 'parts') this.tabProps.parts = true
      // bus.$emit(`open-${page}-tab`)
    }
  }
}
</script>

<style>

</style>