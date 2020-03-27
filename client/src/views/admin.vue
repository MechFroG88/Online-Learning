<template>
  <div id="_admin">
    <nav>
      <h5>
        Navigate <i class="icon-location" style="margin-left: 1rem;"></i>
      </h5>
      <ul>
        <li><a href="#user">User</a></li>
        <li><a href="#event">Event</a></li>
        <li><a href="#period">Periods</a></li>
        <li><a href="#class">Class</a></li>
        <li><a href="#subject">Subjects</a></li>
        <li><a href="#day">Day</a></li>
      </ul>
    </nav>
    <div class="dashboard">
      <div id="user">
        <dt-table
        ref="user"
        :columns="columns.user"
        :tableData="userArr"
        navbar="username"
        hoverable
        title>
          <template slot="title">
            Users
          </template>
          <template slot="class_subjects" slot-scope="{ data }">
            {{data.class_subject}}
          </template>
        </dt-table>
      </div>

      <div id="event">
        <dt-table
        :columns="columns.event"
        :tableData="eventArr"
        hoverable
        title>
          <template slot="title">
            Events
          </template>
        </dt-table>
      </div>

      <div id="period">
        <dt-table
        :columns="columns.period"
        :tableData="periodArr"
        hoverable
        title>
          <template slot="title">
            Periods
          </template>
        </dt-table>
      </div>

      <div id="class">
        <dt-table
        :columns="columns.classes"
        navbar="class name"
        :tableData="classArr"
        hoverable
        title>
          <template slot="title">
            Class
          </template>
        </dt-table>
      </div>

      <div id="subject">
        <dt-table
        :columns="columns.subject"
        navbar="subject name"
        :tableData="subjectArr"
        hoverable
        title>
          <template slot="title">
            Subjects
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openSubject(data)">更改</button>
            <button class="button button-error" @click="confirmDelete(data.id, 'subject')"
            style="margin-left: .5rem;">删除</button>
          </template>
        </dt-table>
      </div>

      <div id="day">
        <dt-table
        :columns="columns.day"
        :tableData="dayArr"
        hoverable
        title>
          <template slot="title">
            Period count (per day)
          </template>
          <template slot="id" slot-scope="{ data }">
            {{ $t('timetable.days')[data.id%7] }}
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openDay(data)">更改</button>
            <!-- <button class="button button-error" style="margin-left: .5rem;">删除</button> -->
          </template>
        </dt-table>
      </div>
    </div>

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
import columns from '@/api/tableColumns';
import moment from 'moment';

import { createUser, getUsers, editUser, deleteUser } from '@/api/users';
import { createEvent, getAllEvents, editEvent, deleteEvent } from '@/api/event';
import { createPeriod, getAllPeriods, deletePeriod } from '@/api/period';
import { createClass, getAllClass, editClass, deleteClass } from '@/api/class';
import { createSubject, getAllSubjects, editSubject, deleteSubject } from '@/api/subject';
import { getAllDays, editDay } from '@/api/day';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getUsers().then((data) => {
      if (data.status == 200) {
        this.userArr = data.data;
        // this.$refs.user.is_loading = false;
      }
    })
    getAllEvents().then((data) => {
      if (data.status == 200) {
        this.eventArr = data.data.map(el => ({
          id: el.id,
          start_date: moment(el.start_date).format('DD-MM-YYYY h:mm A'),
          end_date: moment(el.end_date).format('DD-MM-YYYY h:mm A'),
          start_pick_datetime: moment(el.start_pick_datetime).format('DD-MM-YYYY h:mm A'),
          end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A')
        }));
      }
    })
    getAllPeriods().then((data) => {
      if (data.status == 200) {
        this.periodArr = data.data.map(el => ({
          id: el.id,
          start_time: moment(el.start_time, 'hh:mm:ss').format('h:mm A'),
          end_time: moment(el.end_time, 'hh:mm:ss').format('h:mm A'),
        }));
      }
    })
    getAllClass().then((data) => {
      if (data.status == 200) {
        this.classArr = data.data;
      }
    })
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
      }
    })
    getAllDays().then((data) => {
      if (data.status == 200) {
        this.dayArr = data.data;
      }
    })
  },
  data: () => ({
    columns,
    userArr: [],
    eventArr: [],
    periodArr: [],
    classArr: [],
    subjectArr: [],
    dayArr: [],
    classRel: {},
    subjRel: {},
    edit: {
      day: {}
    },
    delete: {
      id: 0,
      type: ''
    }
  }),
  methods: {
    editUser() {

    },
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
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      })
    },
    confirmDelete(id, type) {
      this.$refs.confirmModal.active = true;
      this.delete.id = id;
      this.delete.type = type;
    },
    doDelete() {
      switch(this.delete.type) {
        
      }
    }
  }
}
</script>

<style lang="scss">

</style>