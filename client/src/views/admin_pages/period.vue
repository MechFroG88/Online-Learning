<template>
  <div id="_period">
    <dt-table
    :columns="period"
    :tableData="periodArr"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.periods') }}
        <button class="button-primary" @click="openPeriod()">{{ $t('table.add') }}</button>
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-error" @click="confirmDelete(data.id)">删除</button>
      </template>
    </dt-table>

    <!-- modals -->
    <modal ref="confirmModal" class="delete-modal">
      <div class="modal-body">
        <h5>{{ $t('modal.confirmDelete') }}</h5>
        <button class="button button-error" @click="doDelete"
        style="float: right; margin-top: 3rem;">
          {{ $t('modal.confirm') }}
        </button>
      </div>
    </modal>

    <modal ref="editPeriod">
      <form @submit.prevent="submitPeriod">
        <div class="u-full-width start_time">
          <label for="start_time">{{ $t('modal.start_time') }}: </label>
          <input class="u-full-width" type="time" id="start_time" 
          v-model="edit.period.start_time">
        </div>
        <div class="u-full-width end_time">
          <label for="end_time">{{ $t('modal.end_time') }}: </label>
          <input class="u-full-width" type="time" id="end_time" 
          v-model="edit.period.end_time">
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import moment from 'moment';
import { period } from '@/api/tableColumns';
import { createPeriod, getAllPeriods, deletePeriod } from '@/api/period';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getAllPeriods().then((data) => {
      if (data.status == 200) {
        this.periodArr = data.data.map(el => ({
          id: el.id,
          start_time: moment(el.start_time, 'hh:mm:ss').format('h:mm A'),
          end_time: moment(el.end_time, 'hh:mm:ss').format('h:mm A'),
        }));
      }
    })
  },
  data: () => ({
    period,
    periodArr: [],
    isAdd: false,
    edit: {
      id: 0,
      period: {
        start_time: '',
        end_time: '',
      },
    },
    delete: {
      id: 0
    }
  }),
  methods: {
    openPeriod() {
      this.$refs.editPeriod.active = true;
    },
    submitPeriod() {
      createPeriod({
        start_time: moment(this.edit.period.start_time, 'HH:mm').format('HH:mm:ss'),
        end_time: moment(this.edit.period.end_time, 'HH:mm').format('HH:mm:ss')
      }).then((data) => {
        if (data.status == 200) {
          this.$refs.editPeriod.active = false;
          getAllPeriods().then((data) => {
            if (data.status == 200) {
              this.periodArr = data.data.map(el => ({
                id: el.id,
                start_time: moment(el.start_time, 'hh:mm:ss').format('h:mm A'),
                end_time: moment(el.end_time, 'hh:mm:ss').format('h:mm A'),
              }));
              this.edit.period = {
                start_time: '',
                end_time: '',
              };
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error add',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error add',
            text: err.message
          })
      })
    },
    confirmDelete(id) {
      this.$refs.confirmModal.active = true;
      this.delete.id = id;
    },
    doDelete() {
      deletePeriod(this.delete.id).then((data) => {
        if (data.status == 200) {
          this.$refs.confirmModal.active = false;
          getAllPeriods().then((data) => {
            if (data.status == 200) {
              this.periodArr = data.data.map(el => ({
                id: el.id,
                start_time: moment(el.start_time, 'hh:mm:ss').format('h:mm A'),
                end_time: moment(el.end_time, 'hh:mm:ss').format('h:mm A'),
              }));
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.message
          })
      })
    }
  }
}
</script>

<style lang="scss">

</style>