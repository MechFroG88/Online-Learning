<template>
  <div id="_homepage">
    <div class="selects row">
      <div class="event-select-group six columns">
        <label class="event-select">
          {{ $t('home.event') }}
        </label>
        <select class="u-full-width" id="class-select" v-model="selected_event">
          <option :value="{id: 0}" selected disabled>
            {{ $t('home.eventSelect') }}
          </option>
          <option v-for="event in eventArr" :key="event.id"
          :value="event">
            {{ event.start_date | moment('DD-MM-YYYY') }} 
            {{ lang == 'cn' ? '至' 
            : lang == 'en' ? 'to' 
            : lang == 'bm' ? 'hingga'
            : '-' }} 
            {{ event.end_date | moment('DD-MM-YYYY') }}
          </option>
        </select>
      </div>
      <div class="class-select-group six columns">
        <label for="class-select">
          {{ $t('home.class') }}
        </label>
        <select class="u-full-width" id="class-select" v-model="selected_class">
          <option :value="0" selected disabled>
            {{ $t('home.classSelect') }}
          </option>
          <option v-for="single_class in classArr" :key="single_class.id"
          :value="single_class.id">
            {{ lang == 'cn' ? single_class.cn_name : single_class.en_name }}
          </option>
        </select>
      </div>
    </div>

    <h5 class="u-full-width" v-if="selected_event.id">
      <b>{{ $t('home.eventEffect') }}</b> : 
      {{ selected_event.start_pick_datetime | moment('DD-MM-YYYY, LT') }}
      {{ lang == 'cn' ? '至' 
       : lang == 'en' ? 'to' 
       : lang == 'bm' ? 'hingga'
       : '-' }}
       {{ selected_event.end_pick_datetime | moment('DD-MM-YYYY, LT') }}
    </h5>

    <carousel-3d v-if="selected_class != 0" ref="eventCarousel" :controls-prev-html="'&#10092;'" :controls-next-html="'&#10093;'" 
      :controls-width="30" :controls-height="60" :controls-visible="true" :clickable="false"
      :height="600" :key="selected_class" 
      :disable3d="true" :space="400">
      <slide class="slide" v-for="(i, ind) in diff_days" :index="ind" :key="i">
        <div class="slide-container">
          <h5 class="title">
            {{ today = $options.filters.moment_add(start_date, ind) }} ({{ getDay(today) }})
          </h5>
          <timetable 
            class="timetable"
            ref="timetable"
            @selected="selected"
            @edit="update"
            @remove="remove"
            :periods="periodArr" 
            :date="start_date | moment_add(ind)"
            :choices="choiceArr.filter(el => el.class_id == selected_class)"
            :subjects="subjectArr"></timetable>
        </div>
      </slide>
    </carousel-3d>

    <h4 class="no-class" v-else>
      {{ $t('home.classSelect') }}
    </h4>

    <!-- modal templates -->
    <modal ref="confirmModal" class="delete-modal">
      <div class="modal-body">
        <h5>{{ $t('modal.confirmDelete') }}</h5>
        <button class="button button-error" style="float: right; margin-top: 3rem;">
          {{ $t('modal.confirm') }}
        </button>
      </div>
    </modal>
    <modal ref="modal">
      <div class="modal-body">
        <h5 class="time row">
          <div class="label columns three">
            {{ $t('modal.date') }}:
          </div>
          <div class="columns eight">
            {{ modal.date }}
          </div>
        </h5>
        <h5 class="time row">
          <div class="label columns three">
            {{ $t('modal.time') }}:
          </div>
          <div class="columns eight">
            {{ modal.period.start_time }} - {{ modal.period.end_time }}
          </div>
        </h5>
        <small style="color: red;" v-if="modal.choice.id == 0"><em>
          * {{ $t('modal.initHint') }} 
        </em></small>
        <form class="ten columns" @submit.prevent="submit">
          <div class="u-full-width subject">
            <label for="choiceSubject">Subject: </label>
            <input class="u-full-width" type="text" id="choiceSubject"
            v-model="modal.choice.subject">
          </div>
          <div class="u-full-width method">
            <label>{{ $t('modal.method') }}: </label>
            <div class="u-full-width">
              <input type="radio" name="method" id="zoom" value="zoom" selected
              v-model="modal.choice.method">
              <span class="label-body">
                  Zoom
              </span>
            </div>
            <div class="u-full-width">
              <input type="radio" name="method" id="others" value="others"
              v-model="modal.choice.method">
              <span class="label-body">
                  {{ $t('modal.otherMethod') }}:
              </span>
              <input type="text" v-model="modal.otherMethod" style="margin-left: .5rem;">
            </div>
          </div>
          <div class="u-full-width link">
            <label for="choiceLink">Link: </label>
            <input class="u-full-width" type="text" id="choiceLink"
            v-model="modal.choice.link">
          </div>
          <div class="u-full-width streamId">
            <label for="choiceStreamId">Stream Id: </label>
            <input class="u-full-width" type="text" id="choiceStreamId" 
            v-model="modal.choice.streamId">
          </div>
          <div class="u-full-width streamPassword">
            <label for="choiceStreamPassword">Stream Password: </label>
            <input class="u-full-width" type="text" id="choiceStreamPassword" 
            v-model="modal.choice.streamPassword">
          </div>
          <div class="u-full-width description">
            <label for="choiceDescription">Description: </label>
            <input class="u-full-width" type="text" id="choiceDescription" 
            v-model="modal.choice.description">
          </div>

          <div v-if="modal.choice.id">
            <input type="submit" class="button button-primary" 
            style="margin-top: 1rem;"
            :value="$t('modal.submit')">
            <br>
            <button class="button button-error" 
            @click="remove"
            style="margin-top: 1rem;">
            {{ $t('modal.delete') }}
            </button>
          </div>
          <div v-else>
            <input type="submit" class="button button-primary" 
            style="margin-top: 1rem;"
            :value="$t('modal.initSubmit')">
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { classes, periods, choices } from '@/api/mock/event';
import { getAllClass } from '@/api/class';
import { getAllEvents } from '@/api/event';
import { getAllPeriods } from '@/api/period';
import { getUserChoice, submitChoice, editChoice, deleteChoice } from '@/api/choice';
import { getAllSubjects } from '@/api/subject';
import moment from 'moment';

import timetable from '@/components/timetable';
import modal from '@/components/modal';
export default {
  components: {
    timetable,
    modal
  },
  data: () => ({
    classArr: [],
    eventArr: [],
    periodArr: [],
    choiceArr: [],
    subjectArr: [],
    availableSubject: [],
    start_date: null,
    diff_days: 0,
    selected_class: 0,
    selected_event: {
      id: 0
    },
    modal: {
      date: '',
      period: {},
      choice: {
        id: 0,
        method: 'zoom',
        link: '',
        streamId: '',
        streamPassword: '',
        description: ''
      },
      otherMethod: ''
    }
  }),
  mounted() {
    getAllClass().then((data) => {
      let classes = new Set();
      for (let classobj of this.user.class_subject) classes.add(classobj.class_id);
      if (data.status == 200) {
        let allClasses = data.data;
        for (let class_id of classes)
          this.classArr.push(allClasses.filter(el => el.id == class_id)[0]);
      }
    })
    getAllEvents().then((data) => {
      if (data.status == 200) {
        this.eventArr = data.data;
      }
    })
    getAllPeriods().then((data) => {
      if (data.status == 200) {
        this.periodArr = data.data.map(el => ({
          id: el.id,
          start_time: this.$options.filters.moment_time(el.start_time, 'hh:mm:ss', 'hh:mm'),
          end_time: this.$options.filters.moment_time(el.end_time, 'hh:mm:ss', 'hh:mm')
        }))
      }
    })
    getUserChoice().then((data) => {
      if (data.status == 200) {
        this.choiceArr = data.data;
      }
    })
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
      }
    })
  },
  methods: {
    goToIndex(index) {
      this.$refs.eventCarousel.goSlide(index);
    },
    getDay(day) {
      let days = this.$t('timetable.days');
      return days[moment(day, 'DD/MM/YYYY').day()];
    },
    selected(date, period) {
      // submit period first
      
      this.modal.date = date;
      this.modal.period = period;
    },
    update(date, period, choice) {
      // update choice data for that period
      this.modal.date = date;
      this.modal.period = period;
      this.modal.choice = choice;
      console.log(choice);

      if (choice.method != 'zoom' && choice.method) {
        this.modal.choice.otherMethod = choice.method;
        this.modal.choice.method = 'others';
      }
    },
    remove() {
      deleteChoice(this.modal.choice.id).then((data) => {
        console.log(data);
      })
    },
    submit() {
      if (this.modal.choice.id) {
        editChoice({
          method: this.modal.choice.method != 'zoom' ? this.modal.otherMethod : 'zoom',
          link: this.modal.choice.link,
          streamid: this.modal.choice.streamId,
          streamPassword: this.modal.choice.streamPassword,
          description: this.modal.choice.description
        }, this.modal.choice.id).then((data) => {
          if (data.status == 200) {
            this.$refs.modal.active = false;
          }
        }).catch((err) => {
          console.log(err);
        }).finally(() => {
          this.modal.choice = {
            method: 'zoom',
            link: '',
            streamId: '',
            streamPassword: '',
            description: ''
          };
        })
      }
      else {
        submitChoice({
          event_id: selected_event.id,
          user_id: this.user.id,
        })
      }
    },
  },
  computed: {
    ...mapState({ 
      lang: 'lang', 
      user: 'user' 
    }),
  },
  watch: {
    selected_class(id) {
      this.availableSubject = this.user.class_subject.filter(el => el.class_id == id)
        .map(el => this.subjectArr.filter(elem => elem.id == el.subject_id)[0]);
      console.log(this.availableSubject);
    },
    selected_event(event) {
      this.start_date = event.start_date;
      this.diff_days = moment(event.end_date).diff(moment(event.start_date), 'days') + 1;
    }
  },
  filters: {
    moment(date, format) {
      return moment(date).format(format);
    },
    moment_time(time, oldFormat, newFormat) {
      return moment(time, oldFormat).format(newFormat);
    },
    moment_add(time, day) {
      return moment(time).add(day, 'd').format('DD-MM-YYYY');
    }
  }
}
</script>

<style lang="scss">

</style>