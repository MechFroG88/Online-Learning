<template>
  <div id="_day">
    <dt-table
    :columns="day"
    :tableData="dayArr"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.day') }}
      </template>
      <template slot="id" slot-scope="{ data }">
        {{ $t('timetable.days')[data.id%7] }}
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-primary" @click="openDay(data)">更改</button>
      </template>
    </dt-table>

    <!-- modals -->
    <modal ref="editDay">
      <form @submit.prevent="submitDay">
        <h5>{{ $t('timetable.days')[edit.day.id%7] }}</h5>
        <label for="limit">
          节数上限
        </label>
        <input type="number" id="limit" 
        class="u-full-width"
        v-model="edit.day.count">

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import { day } from '@/api/tableColumns';
import { getAllDays, editDay } from '@/api/day';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getAllDays().then((data) => {
      if (data.status == 200) {
        this.dayArr = data.data;
      }
    })
  },
  data: () => ({
    day,
    dayArr: [],
    isAdd: false,
    edit: {
      id: 0,
      day: {},
    },
    delete: {
      id: 0,
    }
  }),
  methods: {
    openDay(data) {
      this.edit.day = data;
      this.$refs.editDay.active = true;
    },
    submitDay() {
      editDay(this.edit.day.id, this.edit.day.count).then((data) => {
        if (data.status == 200) {
          this.$refs.editDay.active = false;
          getAllDays().then((data) => {
            if (data.status == 200) {
              this.dayArr = data.data;
              this.edit.day = {};
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error edit',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error edit',
            text: err.message
          })
      })
    },
  }
}
</script>

<style lang="scss">

</style>