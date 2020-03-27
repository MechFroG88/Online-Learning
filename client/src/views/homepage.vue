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

    <carousel-3d v-if="showCarousel && selected_class && selected_event.id" ref="eventCarousel" :controls-prev-html="'&#10092;'" :controls-next-html="'&#10093;'" 
      @after-slide-change="onAfterSlideChange" :loop="false" :minSwipeDistance="2000"
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
      <span v-if="selected_event.id">{{ $t('home.classSelect') }}</span>
      <span v-else>{{ $t('home.eventSelect') }}</span>
    </h4>

    <!-- modal templates -->
    <modal ref="modal">
      <div class="modal-body">
        <h5 class="time row">
          <div class="label columns three">
            {{ $t('modal.date') }}:
          </div>
          <div class="columns nine">
            {{ today = modal.date }} ({{ getDay(today) }})
          </div>
        </h5>
        <h5 class="time row">
          <div class="label columns three">
            {{ $t('modal.time') }}:
          </div>
          <div class="columns nine">
            {{ modal.period.start_time }} - {{ modal.period.end_time }}
          </div>
        </h5>
        <!-- <small style="color: red;" v-if="modal.choice.id == 0"><em>
          * {{ $t('modal.initHint') }} 
        </em></small> -->
        <form class="ten columns" @submit.prevent="submit">
          <div class="u-full-width subject">
            <label for="choiceSubject">{{ $t('modal.subject') }}: </label>
            <select name="subject" id="choiceSubject" v-model="modal.choice.subject_id"
            :disabled="modal.choice.id ? true : false">
              <option value="" selected disabled>
                {{ $t('modal.subjectSelect') }}
              </option>
              <option v-for="subject in availableSubject" :key="subject.id"
              :value="subject.id">
                {{ lang == 'cn' ? subject.cn_name : subject.en_name }}
              </option>
            </select>
          </div>
          <div v-if="modal.choice.id">
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
              <label for="choiceLink">{{ $t('modal.link') }}: </label>
              <input class="u-full-width" type="text" id="choiceLink"
              v-model="modal.choice.link">
            </div>
            <div class="u-full-width streamId">
              <label for="choiceStreamId">{{ $t('modal.streamId') }}: </label>
              <input class="u-full-width" type="text" id="choiceStreamId" 
              v-model="modal.choice.streamId">
            </div>
            <div class="u-full-width streamPassword">
              <label for="choiceStreamPassword">{{ $t('modal.streamPassword') }}: </label>
              <input class="u-full-width" type="text" id="choiceStreamPassword" 
              v-model="modal.choice.streamPassword">
            </div>
            <div class="u-full-width description">
              <label for="choiceDescription">{{ $t('modal.description') }}: </label>
              <input class="u-full-width" type="text" id="choiceDescription" 
              v-model="modal.choice.description">
            </div>
            
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
import { mapState, mapMutations } from 'vuex';
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
    showCarousel: true,
    selected_class: 0,
    selected_event: {
      id: 0
    },
    modal: {
      date: '',
      period: {},
      choice: {
        id: 0,
        subject_id: '',
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
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
        }
      })
    getAllClass().then((data) => {
      let classes = new Set();
      for (let classobj of this.user.class_subject) classes.add(classobj.class_id);
      if (data.status == 200) {
        let allClasses = data.data;
        for (let class_id of classes)
          this.classArr.push(allClasses.filter(el => el.id == class_id)[0]);
        this.selected_class = this.home.class;
        getAllEvents().then((data) => {
          if (data.status == 200) {
            this.eventArr = data.data.sort((a, b) => 
              moment(a.start_date).isBefore(b.start_date) ? -1 : 1
            );
            getUserChoice().then((data) => {
              if (data.status == 200) {
                this.choiceArr = data.data;
                this.selected_event = this.home.event;
                if (this.selected_class && this.selected_event.id)
                  this.$nextTick(() => {
                    this.$refs.eventCarousel.goSlide(this.home.index);
                  })
              }
            })
          }
        })
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
  },
  methods: {
    ...mapMutations({
      setClass: 'SET_CLASS',
      setEvent: 'SET_EVENT',
      setIndex: 'SET_INDEX'
    }),
    getDay(day) {
      let days = this.$t('timetable.days');
      return days[moment(day, 'DD/MM/YYYY').day()];
    },
    selected(date, period) {
      // submit period first
      this.modal.date = date;
      this.modal.period = period;
      if (this.availableSubject.length == 1) {
        this.submit();
      }
      else {
        this.$refs.modal.active = true;
      }
    },
    update(date, period, choice) {
      // update choice data for that period
      this.modal.date = date;
      this.modal.period = period;
      this.modal.choice = choice;

      if (choice.method != 'zoom' && choice.method) {
        this.modal.choice.otherMethod = choice.method;
        this.modal.choice.method = 'others';
      }
      this.$refs.modal.active = true;
    },
    remove() {
      deleteChoice(this.modal.choice.id).then((data) => {
        if (data.status == 200) {
          this.$refs.modal.active = false;
          getUserChoice().then((data) => {
            if (data.status == 200) {
              this.showCarousel = false;
              this.choiceArr = data.data;
              this.$nextTick(() => {
                this.showCarousel = true;
                this.$nextTick(() => {
                  this.$refs.eventCarousel.goSlide(this.home.index);
                })
              });
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          alert(err.response.data);
        else alert(err.message);
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
            getUserChoice().then((data) => {
              if (data.status == 200) {
                this.showCarousel = false;
                this.choiceArr = data.data;
                this.$nextTick(() => {
                  this.showCarousel = true;
                  this.$nextTick(() => {
                    this.$refs.eventCarousel.goSlide(this.home.index);
                  })
                });
              }
            })
          }
        }).catch((err) => {
          if (err.response)
            alert(err.response.data);
          else alert(err.message);
        }).finally(() => {
          this.modal.choice = {
            id: 0,
            subject: '',
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
          event_id: this.selected_event.id,
          user_id: this.user.id,
          subject_id: this.availableSubject.length == 1 ? 
                      this.availableSubject[0].subject_id 
                      : this.modal.choice.subject_id,
          period_id: this.modal.period.id,
          date: moment(this.modal.date, 'DD-MM-YYYY').format('YYYY-MM-DD'),
          class_id: this.selected_class
        }).then((data) => {
          if (data.status == 200) {
            getUserChoice().then((data) => {
              if (data.status == 200) {
                this.showCarousel = false;
                this.choiceArr = data.data;
                this.$nextTick(() => {
                  this.showCarousel = true;
                  this.$nextTick(() => {
                    this.$refs.eventCarousel.goSlide(this.home.index);
                  })
                })
              }
            })
            this.update(this.modal.date, this.modal.period, data.data);
          }
        }).catch((err) => {
          if (err.response)
            alert(err.response.data);
          else alert(err.message);
        })
      }
    },
    onAfterSlideChange(index) {
      this.setIndex(index);
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang', 
      user: 'user',
      home: 'home'
    }),
  },
  watch: {
    selected_class(id) {
      this.availableSubject = this.user.class_subject.filter(el => el.class_id == id)
        .map(el => this.subjectArr.filter(elem => elem.id == el.subject_id)[0]);
      this.setClass(id);
    },
    selected_event(event) {
      this.start_date = event.start_date;
      this.diff_days = moment(event.end_date).diff(moment(event.start_date), 'days') + 1;
      this.setEvent(event);
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