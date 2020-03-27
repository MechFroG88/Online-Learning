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
        v-if="Object.keys(classRel).length 
           && Object.keys(subjRel).length"
        :columns="columns.user"
        :tableData="userArr"
        navbar="username"
        hoverable
        title>
          <template slot="title">
            Users
            <button class="button-primary" @click="openUser()">Add</button>
          </template>
          <template slot="type" slot-scope="{ data }">
            {{ data.type == 1 ?  $t('table.teacher') : $t('table.admin') }}
          </template>
          <template slot="class_subjects" slot-scope="{ data }">
            <template v-for="subj in data.class_subject">
              {{ lang == 'cn' ? classRel[subj.class_id][0] : classRel[subj.class_id][1] }}:
              {{ lang == 'cn' ? subjRel[subj.subject_id][0] : subjRel[subj.subject_id][1] }}
              <br :key="(subj.subject_id+subj.class_id)*subj.subject_id" />
            </template>
            <template v-if="data.class_subject.length == 0">
              --
            </template>
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openUser(data, false)">更改</button>
            <button class="button button-error" @click="confirmDelete(data.id, 'user')"
            style="margin-left: .5rem;">删除</button>
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
            <button class="button-primary" @click="openEvent()">Add</button>
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openEvent(data, false)">更改</button>
            <button class="button button-error" @click="confirmDelete(data.id, 'event')"
            style="margin-left: .5rem;">删除</button>
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
            <button class="button-primary" @click="openPeriod()">Add</button>
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-error" @click="confirmDelete(data.id, 'period')">删除</button>
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
            <button class="button-primary" @click="openClass()">Add</button>
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openClass(data, false)">更改</button>
            <button class="button button-error" @click="confirmDelete(data.id, 'class')"
            style="margin-left: .5rem;">删除</button>
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
            <button class="button-primary" @click="openSubject()">Add</button>
          </template>
          <template slot="action" slot-scope="{ data }">
            <button class="button button-primary" @click="openSubject(data, false)">更改</button>
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

    <modal ref="editUser">
      <form @submit.prevent="submitUser">
        <div class="u-full-width username">
          <label for="username">{{ $t('modal.username') }}: </label>
          <input class="u-full-width" type="text" id="username" 
          :disabled="!isAdd"
          v-model="edit.user.username">
        </div>
        <div class="u-full-width cn_name">
          <label for="user_cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="user_cn_name" 
          v-model="edit.user.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="user_en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="user_en_name" 
          v-model="edit.user.en_name">
        </div>
        <small style="color: red;" v-if="!isAdd"><em>
          * {{ $t('modal.passwordHint') }} 
        </em></small>
        <div class="u-full-width password">
          <label for="password">{{ $t('modal.password') }}: </label>
          <input class="u-full-width" type="text" id="password" 
          v-model="edit.user.password">
        </div>
        <div class="u-full-width type">
          <label for="user_type">{{ $t('modal.user_type') }}: </label>
          <select name="user_type" id="user_type" v-model="edit.user.type">
            <option :value="-1" disabled>
              {{ $t('modal.user_typeSelect') }}
            </option>
            <option :value="0">
              {{ $t('table.admin') }}
            </option>
            <option :value="1">
              {{ $t('table.teacher') }}
            </option>
          </select>
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
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

    <modal ref="editClass">
      <form @submit.prevent="submitClass">
        <div class="u-full-width cn_name">
          <label for="class_cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="class_cn_name" 
          v-model="edit.class.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="class_en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="class_en_name" 
          v-model="edit.class.en_name">
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>

    <modal ref="editSubject">
      <form @submit.prevent="submitSubject">
        <div class="u-full-width cn_name">
          <label for="cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="cn_name" 
          v-model="edit.subject.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="en_name" 
          v-model="edit.subject.en_name">
        </div>
        <div class="u-full-width week">
          <label for="week">{{ $t('modal.week') }}: </label>
          <input class="u-full-width" type="number" id="week" 
          v-model="edit.subject.week">
        </div>
        <div class="u-full-width day">
          <label for="day">{{ $t('modal.day') }}: </label>
          <input class="u-full-width" type="number" id="day" 
          v-model="edit.subject.day">
        </div>

        <input type="submit" class="button button-primary" 
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
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
import { mapState } from 'vuex';

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
    getAllEvents().then((data) => {
      if (data.status == 200) {
        this.eventArr = data.data.map(el => ({
          id: el.id,
          start_date: moment(el.start_date).format('DD-MM-YYYY'),
          end_date: moment(el.end_date).format('DD-MM-YYYY'),
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
        this.buildClassRel();
        getAllSubjects().then((data) => {
          if (data.status == 200) {
            this.subjectArr = data.data;
            this.builSubjRel();
            getUsers().then((data) => {
              if (data.status == 200) {
                this.userArr = data.data;
              }
            })
          }
        })
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
    isAdd: false,
    edit: {
      id: 0,
      user: {
        username: '',
        cn_name: '',
        en_name: '',
        password: '',
        type: -1,
      },
      event: {
        start_date: '',
        end_date: '',
        start_pick_datetime: '',
        end_pick_datetime: ''
      },
      period: {
        start_time: '',
        end_time: '',
      },
      class: {
        cn_name: '',
        en_name: '',
      },
      subject: {
        cn_name: '',
        en_name: '',
        day: 0,
        week: 0,
      },
      day: {},
    },
    delete: {
      id: 0,
      type: ''
    }
  }),
  methods: {
    openUser(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.user = data || this.edit.user;
      if (data) {
        delete this.edit.user.remember_token;
        delete this.edit.user.id;
      }
      this.$refs.editUser.active = true;
    },
    submitUser() {
      if (this.isAdd) {
        createUser(this.edit.user).then((data) => {
          if (data.status == 200) {
            this.$refs.editUser.active = false;
            getUsers().then((data) => {
              if (data.status == 200) {
                this.userArr = data.data;
                this.edit.user = {
                  username: '',
                  cn_name: '',
                  en_name: '',
                  password: '',
                  type: -1,
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
      else {
        if (!this.edit.user.password) delete this.edit.user.password;
        delete this.edit.user.username;
        editUser(this.edit.user, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editUser.active = false;
            getUsers().then((data) => {
              if (data.status == 200) {
                this.userArr = data.data;
                this.edit.user = {
                  username: '',
                  cn_name: '',
                  en_name: '',
                  password: '',
                  type: -1,
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
    },
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
                  end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A')
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
                  end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A')
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
        })
      }
    },
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
      })
    },
    openClass(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.class = data || this.edit.class;
      this.$refs.editClass.active = true;
    },
    submitClass() {
      if (this.isAdd) {
        createClass(this.edit.class).then((data) => {
          if (data.status == 200) {
            this.$refs.editClass.active = false;
            getAllClass().then((data) => {
              if (data.status == 200) {
                this.classArr = data.data;
                this.buildClassRel();
                this.edit.class = {
                  cn_name: '',
                  en_name: ''
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
      else {
        editClass(this.edit.class, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editClass.active = false;
            getAllClass().then((data) => {
              if (data.status == 200) {
                this.classArr = data.data;
                this.buildClassRel();
                this.edit.class = {
                  cn_name: '',
                  en_name: ''
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
    },
    openSubject(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.subject = data || this.edit.subject;
      this.$refs.editSubject.active = true;
    },
    submitSubject() {
      if (this.isAdd) {
        createSubject(this.edit.subject).then((data) => {
          if (data.status == 200) {
            this.$refs.editSubject.active = false;
            getAllSubjects().then((data) => {
              if (data.status == 200) {
                this.subjectArr = data.data;
                this.builSubjRel();
                this.edit.subject = {
                  cn_name: '',
                  en_name: '',
                  day: 0,
                  week: 0,
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
      else {
        editSubject(this.edit.subject, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editSubject.active = false;
            getAllSubjects().then((data) => {
              if (data.status == 200) {
                this.subjectArr = data.data;
                this.builSubjRel();
                this.edit.subject = {
                  cn_name: '',
                  en_name: '',
                  day: 0,
                  week: 0,
                };
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        })
      }
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
              this.edit.day = {};
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
        case 'user':
          deleteUser(this.delete.id).then((data) => {
            if (data.status == 200) {
              this.$refs.confirmModal.active = false;
              getAllClass().then((data) => {
                if (data.status == 200) {
                  this.classArr = data.data;
                  this.buildClassRel();
                  this.$nextTick(this.$forceUpdate);
                }
              })
            }
          })
          break;
        case 'event':
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
                    end_pick_datetime: moment(el.end_pick_datetime).format('DD-MM-YYYY h:mm A')
                  }));
                  this.$nextTick(this.$forceUpdate);
                }
              })
            }
          })
          break;
        case 'period':
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
          })
          break;
        case 'class':
          deleteClass(this.delete.id).then((data) => {
            if (data.status == 200) {
              this.$refs.confirmModal.active = false;
              getUsers().then((data) => {
                if (data.status == 200) {
                  this.userArr = data.data;
                  this.$nextTick(this.$forceUpdate);
                }
              })
            }
          })
          break;
        case 'subject':
          deleteSubject(this.delete.id).then((data) => {
            if (data.status == 200) {
              this.$refs.confirmModal.active = false;
              getAllSubjects().then((data) => {
                if (data.status == 200) {
                  this.subjectArr = data.data;
                  this.builSubjRel();
                  this.$nextTick(this.$forceUpdate);
                }
              })
            }
          })
          break;
      }
    },
    buildClassRel() {
      for (let single_class of this.classArr) {
        this.classRel[single_class.id] = 
          [single_class.cn_name, single_class.en_name];
      }
    },
    builSubjRel() {
      for (let subject of this.subjectArr) {
        this.subjRel[subject.id] = 
          [subject.cn_name, subject.en_name]
      }
    }
  },
  computed: {
    ...mapState({
      lang: 'lang'
    })
  }
}
</script>

<style lang="scss">

</style>