<template>
  <div id="_homepage">
    <div class="display-info" v-if="$route.name == 'choice'">
      <h5><em>
        {{ $t('admin.home.currentLogin', { name: lang == 'cn' ? user.cn_name : user.en_name }) }}
      </em></h5>
      <button class="button-error" @click="backToAdmin">
        {{ $t('admin.home.back') }}
      </button>
    </div>

    <div class="u-full-width" v-if="$route.name == 'masterChoice'"
    style="display: flex; justify-content: flex-end; margin-bottom: .8rem;">
      <button class="button-error" @click="backToAdmin">
        {{ $t('admin.home.back') }}
      </button>
    </div>

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
            :subjects="subjectArr"
            :master="isMaster"></timetable>
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
        <form class="ten columns" @submit.prevent="submit">
          <div class="u-full-width user" v-if="isMaster && !modal.choice.id">
            <label for="masterUser">{{ $t('modal.master.user') }}: </label>
            <select name="subject" id="masterUser" v-model="user">
              <option value="" selected disabled>
                {{ $t('modal.master.userSelect') }}
              </option>
              <option v-for="user in master.userArr" :key="user.id"
              :value="user">
                {{ lang == 'cn' ? user.cn_name : user.en_name }}
              </option>
            </select>
          </div>
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
import { getUsers } from '@/api/users';
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
    isAdmin: false,
    isMaster: false,
    classArr: [],
    eventArr: [],
    periodArr: [],
    choiceArr: [],
    subjectArr: [],
    user: {},
    availableSubject: [],
    start_date: null,
    diff_days: 0,
    showCarousel: false,
    selected_class: 0,
    selected_event: {
      id: 0
    },
    modal: {
      date: '',
      period: {},
      choice: {
        id: 0,
        subject_id: 0,
        method: 'zoom',
        link: '',
        streamId: '',
        streamPassword: '',
        description: ''
      },
      otherMethod: ''
    },
    master: {
      allUser: [],
      userArr: []
    }
  }),
  mounted() {
    // Initial conditions for different modes
    if (this.$route.name == 'home') this.user = this.$store.state.user;
    else if (this.$route.name == 'masterChoice') {
      this.isMaster = true;
      getUsers().then((data) => {
        if (data.status == 200) {
          this.master.allUser = data.data;
          this.selected_class = this.home.class;
          this.selected_event = this.home.event;
        }
      })
    }
    else {
      this.isAdmin = true;
      this.user = this.$store.state.sub_user;
    }

    // API calls
    getAllSubjects().then((data) => {
      if (data.status == 200) {
        this.subjectArr = data.data;
        }
      })
    getAllClass().then((data) => {
      if (data.status == 200) {
        if (this.$route.name != 'masterChoice') {
          let classes = new Set();
          for (let classobj of this.user.class_user) classes.add(classobj.class_id);
          let allClasses = data.data;
          for (let class_id of classes)
            this.classArr.push(allClasses.filter(el => el.id == class_id)[0]);
        }
        else {
          this.classArr = data.data;
        }
        getAllEvents().then((data) => {
          if (data.status == 200) {
            this.eventArr = data.data.sort((a, b) => 
              moment(a.start_date).isBefore(b.start_date) ? -1 : 1
            );
            if (this.master.userArr.length || !this.isMaster) {
              this.selected_class = this.home.class;
              this.selected_event = this.home.event;
              getUserChoice(this.isMaster ? this.master.userArr[0].id 
                : this.isAdmin ? this.user.id : null).then((data) => {
                if (data.status == 200) {
                  this.choiceArr = data.data;
                  this.showCarousel = true;
                  this.$nextTick(() => {
                    if (this.showCarousel && this.selected_class && this.selected_event.id && this.$refs.eventCarousel)
                        this.$refs.eventCarousel.goSlide(this.home.index);
                  })
                }
              })
            }
          }
        })
      }
    })
    getAllPeriods().then((data) => {
      if (data.status == 200) {
        this.periodArr = data.data.map(el => ({
          id: el.id,
          start_time: this.$options.filters.moment_time(el.start_time, 'HH:mm:ss', 'HH:mm'),
          end_time: this.$options.filters.moment_time(el.end_time, 'HH:mm:ss', 'HH:mm')
        }))
      }
    })
  },
  methods: {
    ...mapMutations({
      setClass: 'SET_CLASS',
      setEvent: 'SET_EVENT',
      setIndex: 'SET_INDEX',
      resetSub: 'RESET_SUBUSER'
    }),
    getDay(day) {
      let days = this.$t('timetable.days');
      return days[moment(day, 'DD/MM/YYYY').day()];
    },
    selected(date, period) {
      // submit period first
      this.modal.date = date;
      this.modal.period = period;
      this.modal.choice = {
        id: 0,
        subject_id: 0,
        method: 'zoom',
        link: '',
        streamId: '',
        streamPassword: '',
        description: ''
      };
      if (!this.isMaster && this.availableSubject.length == 1) {
        this.submit();
      }
      else {
        this.user = {};
        this.$refs.modal.active = true;
      }
    },
    update(date, period, choice) {
      // update choice data for that period
      this.modal.date = date;
      this.modal.period = period;
      this.modal.choice = choice;

      if (choice.method != 'zoom' && choice.method) {
        this.modal.otherMethod = choice.method;
        this.modal.choice.method = 'others';
      }
      this.$refs.modal.active = true;
    },
    remove() {
      deleteChoice(this.modal.choice.id).then((data) => {
        if (data.status == 200) {
          this.$refs.modal.active = false;
          getUserChoice(this.isMaster ? this.master.userArr[0].id
          : this.isAdmin ? this.user.id : null).then((data) => {
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
          this.$notify({
            type: 'error',
            title: 'Error deleting choice',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error deleting choice',
            text: err.message
          })
      })
    },
    submit() {
      if (this.modal.choice.id) {
        editChoice({
          method: this.modal.choice.method != 'zoom' ? this.modal.otherMethod : 'zoom',
          link: this.modal.choice.link,
          streamId: this.modal.choice.streamId,
          streamPassword: this.modal.choice.streamPassword,
          description: this.modal.choice.description
        }, this.modal.choice.id).then((data) => {
          if (data.status == 200) {
            getUserChoice(this.isMaster ? this.master.userArr[0].id
            : this.isAdmin ? this.user.id : null).then((data) => {
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
            this.$notify({
              type: 'error',
              title: 'Error editing',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error editing',
              text: err.message
            })
        }).finally(() => {
          this.$refs.modal.active = false;
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
                      this.availableSubject[0].id
                      : this.modal.choice.subject_id,
          period_id: this.modal.period.id,
          date: moment(this.modal.date, 'DD-MM-YYYY').format('YYYY-MM-DD'),
          class_id: this.selected_class
        }).then((data) => {
          if (data.status == 200) {
            if (this.$store.state.user.username == 'T00110')
              this.$notify('刘老师的课我们也能上吗？😣😣😣')
            if (this.$store.state.user.username == 'T00139')
              this.$notify('葱哥的课就是我们想上的课！😍')
            this.update(this.modal.date, this.modal.period, data.data);
          }
        }).catch((err) => {
          this.$refs.modal.active = false;
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error submitting choice',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error submitting choice',
              text: err.message
            })
        }).finally(() => {
          getUserChoice(this.isMaster ? this.master.userArr[0].id
          : this.isAdmin ? this.user.id : null).then((data) => {
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
        })
      }
    },
    getAvailableSubjects(id) {
      this.availableSubject = this.user.class_user.filter(el => el.class_id == id)
          .map(el => this.subjectArr.filter(elem => elem.id == el.subject_id)[0]);
    },
    onAfterSlideChange(index) {
      this.setIndex(index);
    },
    backToAdmin() {
      this.resetSub();
      this.$router.push({ name: 'admin' });
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang',
      home: 'home'
    }),
  },
  watch: {
    selected_class(id) {
      this.setClass(id);
      this.showCarousel = false;
      if (this.isMaster) {
        this.master.userArr = this.master.allUser
          .filter(el => el.class_user
            .some(elem => elem.class_id == id))
        getUserChoice(this.master.userArr[0].id).then((data) => {
          if (data.status == 200) {
            this.choiceArr = data.data;
            this.showCarousel = true;
            this.$nextTick(() => {
              if (this.showCarousel && this.selected_class && this.selected_event.id && this.$refs.eventCarousel)
                this.$refs.eventCarousel.goSlide(this.home.index);
            })
          }
        })
      }
      else this.getAvailableSubjects(id)
    },
    selected_event(event) {
      this.start_date = event.start_date;
      this.diff_days = moment(event.end_date).diff(moment(event.start_date), 'days') + 1;
      this.setEvent(event);
    },
    user(val) {
      if (this.isMaster && Object.keys(val).length) {
        this.getAvailableSubjects(this.selected_class);
      }
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