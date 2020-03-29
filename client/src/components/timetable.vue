<template>
  <div id="_timetable">
    <template v-for="(period, ind) in periods">
      <div class="period-cell filler" :key="period.start_time"
      v-if="ind == 0 ? false : period.start_time != periods[ind-1].end_time">
        <div class="time">
          <span>{{ periods[ind-1].end_time }}</span>
        </div>
        <div class="info">
          {{ $t('timetable.empty') }}
        </div>
      </div>
      <div class="period-cell" 
      :class="[
        filtered_choice.filter(el => el.period_id == period.id)[0] ? 
          filtered_choice.filter(el => el.period_id == period.id)[0].user_id == user.id 
            ? 'self'
          : 'occupied' 
        : 'empty'
      ]"
      :key="period.id">
        <div class="time">
          <span>{{ period.start_time }}</span>
        </div>
        <div class="info">
          <div v-if="fc = filtered_choice.filter(el => el.period_id == period.id)[0]" style="position: relative;">
            <div class="teacher-name">
              {{ lang == 'cn' ? fc.cn_name : fc.en_name }}
               -- 
              {{ lang == 'cn' ? getSubject(fc.subject_id).cn_name
               : getSubject(fc.subject_id).en_name }}
            </div>
            
            <button class="button button-success" 
            style="margin-top: .5rem;"
            v-if="fc.user_id == user.id"
            @click="edit(period, 
            filtered_choice.filter(el => el.period_id == period.id)[0])">
              {{ $t('timetable.modify') }}
            </button>
          </div>
          <div v-else>
            <button class="button select" @click="select(period)">
              {{ $t('timetable.select') }}
            </button>
          </div>
        </div>
      </div>
      <div class="period-cell" :key="period.end_time"
      v-if="ind == periods.length - 1">
        <div class="time">
          <span>{{ period.end_time }}</span>
        </div>
        <div class="info"></div>
      </div>
    </template>
  </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';

export default {
  props: {
    date: {
      type: String,
      required: true
    },
    periods: {
      type: Array,
      required: true
    },
    choices: {
      type: Array,
      default: () => [],
    },
    subjects: {
      type: Array,
      default: () => [],
    }
  },
  mounted() {
    if (this.$route.name == 'home') this.user = this.$store.state.user;
    else {
      this.isAdmin = true;
      this.user = this.$store.state.sub_user;
    }
    this.filtered_choice = this.choices.filter(el => 
      moment(el.date, 'YYYY-MM-DD').format('DD-MM-YYYY') == this.date);
  },
  data: () => ({
    infos: [],
    filtered_choice: [],
    selected_period: {},
  }),
  methods: {
    select(period) {
      this.$emit('selected', this.date, period);
    },
    edit(period, choice) {
      this.$emit('edit', this.date, period, choice);
    },
    getSubject(id) {
      return this.subjects.filter(el => el.id == id)[0];
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang'
    }),
  }
}
</script>

<style lang="scss">

</style>