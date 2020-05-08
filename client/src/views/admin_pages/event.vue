<template>
  <div id="_event">
    <dt-table
    :columns="event"
    :tableData="eventArr"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.events') }}
        <button class="button-primary" @click="openEvent()">{{ $t('table.add') }}</button>
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-primary" @click="openEvent(data, false)"
        style="margin-right: .5rem;">{{ $t('admin.edit') }}</button>
        <button class="button button-error" @click="confirmDelete(data.id)"
        v-if="!data.deleted_at">{{ $t('admin.close') }}</button>
        <button class="button button-warning" @click="doRestore(data.id)"
        v-else>{{ $t('admin.open') }}</button>
      </template>
      <template slot="export" slot-scope="{ data }">
        <button class="button-success" @click="exportData(data.id)">
          {{ $t('admin.download') }}
        </button>
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

    <modal ref="editEvent">
      <form @submit.prevent="submitEvent">
        <div class="u-full-width start_date">
          <label for="start_date">{{ $t('modal.start_date') }}: </label>
          <input class="u-full-width" type="date" id="start_date" 
          v-model="edit.event.start_date">
        </div>
        <div class="u-full-width end_date">
          <label for="end_date">{{ $t('modal.end_date') }}: </label>
          <input class="u-full-width" type="date" id="end_date" 
          v-model="edit.event.end_date">
        </div>
        <div class="u-full-width start_pick_datetime">
          <label for="start_pick_datetime">{{ $t('modal.start_pick_datetime') }}: </label>
          <input class="u-full-width" type="datetime-local" id="start_pick_datetime" 
          v-model="edit.event.start_pick_datetime">
        </div>
        <div class="u-full-width end_pick_datetime">
          <label for="end_pick_datetime">{{ $t('modal.end_pick_datetime') }}: </label>
          <input class="u-full-width" type="datetime-local" id="end_pick_datetime" 
          v-model="edit.event.end_pick_datetime">
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
import { event } from '@/api/tableColumns';
import { createEvent, getAllEvents, editEvent, deleteEvent, restoreEvent } from '@/api/event';

import dtTable from '@/components/table';
import modal from '@/components/modal';
import request from '@/api/request';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getAllEvents().then((data) => {
      if (data.status == 200) {
        this.eventArr = data.data.map(el => ({
          id: el.id,
          start_date: moment(el.start_date).format('DD-MM-YYYY'),
          end_date: moment(el.end_date).format('DD-MM-YYYY'),
          start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
          end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A'),
          deleted_at: el.deleted_at,
          rowDisabled: el.deleted_at ? true : false
        }));
      }
    })
  },
  data: () => ({
    event,
    eventArr: [],
    isAdd: false,
    edit: {
      id: 0,
      event: {
        start_date: '',
        end_date: '',
        start_pick_datetime: '',
        end_pick_datetime: ''
      },
    },
    delete: {
      id: 0
    }
  }),
  methods: {
    openEvent(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.event = data || this.edit.event;
      if (data) {
        this.edit.event.start_date = moment(this.edit.event.start_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
        this.edit.event.end_date = moment(this.edit.event.end_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
        this.edit.event.start_pick_datetime = moment(this.edit.event.start_pick_datetime, 'DD-MM-YYYY h:mm A').format('YYYY-MM-DDTHH:mm')
        this.edit.event.end_pick_datetime = moment(this.edit.event.end_pick_datetime, 'DD-MM-YYYY h:mm A').format('YYYY-MM-DDTHH:mm')
      }
      this.$refs.editEvent.active = true;
    },
    submitEvent() {
      if (this.isAdd) {
        createEvent({
          ...this.edit.event,
          start_pick_datetime: this.edit.event.start_pick_datetime.replace('T', ' ').concat(':00'),
          end_pick_datetime: this.edit.event.end_pick_datetime.replace('T', ' ').concat(':00')
        }).then((data) => {
          if (data.status == 200) {
            this.$refs.editEvent.active = false;
            getAllEvents().then((data) => {
              if (data.status == 200) {
                this.eventArr = data.data.map(el => ({
                  id: el.id,
                  start_date: moment(el.start_date).format('DD-MM-YYYY h:mm A'),
                  end_date: moment(el.end_date).format('DD-MM-YYYY h:mm A'),
                  start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
                  end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A'),
                  deleted_at: el.deleted_at,
                  rowDisabled: el.deleted_at ? true : false
                }));
                this.edit.event = {
                  start_date: '',
                  end_date: '',
                  start_pick_datetime: '',
                  end_pick_datetime: ''
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
      }
      else {
        editEvent({
          ...this.edit.event,
          start_pick_datetime: this.edit.event.start_pick_datetime.replace('T', ' ').concat(':00'),
          end_pick_datetime: this.edit.event.end_pick_datetime.replace('T', ' ').concat(':00')
        }, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editEvent.active = false;
            getAllEvents().then((data) => {
              if (data.status == 200) {
                this.eventArr = data.data.map(el => ({
                  id: el.id,
                  start_date: moment(el.start_date).format('DD-MM-YYYY h:mm A'),
                  end_date: moment(el.end_date).format('DD-MM-YYYY h:mm A'),
                  start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
                  end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A'),
                  deleted_at: el.deleted_at,
                  rowDisabled: el.deleted_at ? true : false
                }));
                this.edit.event = {
                  start_date: '',
                  end_date: '',
                  start_pick_datetime: '',
                  end_pick_datetime: ''
                };
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
      }
    },
    confirmDelete(id) {
      this.$refs.confirmModal.active = true;
      this.delete.id = id;
    },
    doDelete() {
      deleteEvent(this.delete.id).then((data) => {
        if (data.status == 200) {
          this.$refs.confirmModal.active = false;
          getAllEvents().then((data) => {
            if (data.status == 200) {
              this.eventArr = data.data.map(el => ({
                id: el.id,
                start_date: moment(el.start_date).format('DD-MM-YYYY h:mm A'),
                end_date: moment(el.end_date).format('DD-MM-YYYY h:mm A'),
                start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
                end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A'),
                deleted_at: el.deleted_at,
                rowDisabled: el.deleted_at ? true : false
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
    },
    doRestore(id) {
      restoreEvent(id).then((data) => {
        if (data.status == 200) {
          getAllEvents().then((data) => {
            if (data.status == 200) {
              this.eventArr = data.data.map(el => ({
                id: el.id,
                start_date: moment(el.start_date).format('DD-MM-YYYY h:mm A'),
                end_date: moment(el.end_date).format('DD-MM-YYYY h:mm A'),
                start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
                end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A'),
                deleted_at: el.deleted_at,
                rowDisabled: el.deleted_at ? true : false
              }));
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error restore',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error restore',
            text: err.message
          })
      })
    },
    exportData(id) {
      window.open(request.defaults.baseURL + `export/${id}`, '_blank');
    },
  }
}
</script>

<style lang="scss">

</style>