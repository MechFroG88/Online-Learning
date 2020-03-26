<template>
  <div id="_homepage">
    <div class="class-select-group six columns">
      <label for="class-select">
        {{ $t('home.class') }}
      </label>
      <select class="u-full-width" id="class-select" v-model="selected_class">
        <option :value="0" disabled>
          {{ $t('home.classSelect') }}
        </option>
        <option v-for="single_class in classArr" :key="single_class.id"
        :value="single_class.id">
          {{ lang == 'cn' ? single_class.cn_name : single_class.en_name }}
        </option>
      </select>
    </div>
    <carousel-3d v-if="selected_class != 0" ref="eventCarousel" :controls-prev-html="'&#10092;'" :controls-next-html="'&#10093;'" 
      :controls-width="30" :controls-height="60" :controls-visible="true" :clickable="false"
      :height="600" :key="selected_class" 
      :disable3d="true" :space="400">
      <slide class="slide" v-for="(i, ind) in 10" :index="ind" :key="i">
        <div class="slide-container">
          <h5 class="title">
            {{i}}/4/2020
          </h5>
          <timetable 
            class="timetable"
            ref="timetable"
            @selected="selected"
            @edit="update"
            :periods="periods" 
            :date="new Date(`4/${i}/2020`)"
            :choices="choices.filter(el => el.class_id == selected_class)"></timetable>
        </div>
      </slide>
    </carousel-3d>
    <modal ref="modal">
      <div class="modal-body" slot="body">
        <h5 class="time row">
          <div class="label columns two">
            {{ $t('modal.date') }}:
          </div>
          <div class="columns nine">
            {{ modal.date }}
          </div>
        </h5>
        <h5 class="time row">
          <div class="label columns two">
            {{ $t('modal.time') }}:
          </div>
          <div class="columns nine">
            {{ modal.period.start_time }} - {{ modal.period.end_time }}
          </div>
        </h5>
        <form class="ten columns" @submit.prevent="submit">
          <div class="u-full-width method">
            <label>{{ $t('modal.method') }}: </label>
            <div class="u-full-width">
              <input type="radio" name="method" id="zoom" value="zoom"
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
            <input class="u-full-width" type="url" id="choiceLink" 
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

          <input type="submit" class="button button-primary" 
          style="margin-top: 1rem;"
          :value="$t('modal.submit')">
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { classes, periods, choices } from '@/api/mock/event';
import moment from 'moment';

import timetable from '@/components/timetable';
import modal from '@/components/modal';
export default {
  components: {
    timetable,
    modal
  },
  data: () => ({
    classes,
    periods,
    choices,
    classArr: [],
    selected_class: 1,
    modal: {
      date: '',
      period: {},
      choice: {
        method: '',
        link: '',
        streamId: '',
        streamPassword: '',
        description: ''
      },
      otherMethod: ''
    }
  }),
  mounted() {
    let classes = new Set();
    for (let classobj of this.user.class_subject) classes.add(classobj.class);
    for (let class_id of classes)
      this.classArr.push(this.classes.filter(el => el.id == class_id)[0]);
  },
  methods: {
    goToIndex(index) {
      this.$refs.eventCarousel.goSlide(index);
    },
    selected(date, period) {
      // submit period first
      this.modal.date   = moment(date).format('DD-MM-YYYY');
      this.modal.period = period;
    },
    update(date, period, choice) {
      // update choice data for that period
      this.modal.date   = moment(date).format('DD-MM-YYYY');
      this.modal.period = period;
      this.modal.choice = choice;
      if (choice.method != 'zoom') {
        this.modal.choice.otherMethod = choice.method;
        this.modal.choice.method = 'others';
      }
    },
    submit() {
      
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang', 
      user: 'user' 
    }),
  }
}
</script>

<style lang="scss">

</style>